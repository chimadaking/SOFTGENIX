<?php
namespace App\Controllers;

class APIController extends BaseController
{
    private $instanceModel;
    private $providerModel;
    private $serviceModel;

    public function __construct()
    {
        $this->instanceModel = $this->model('APIInstance');
        $this->providerModel = $this->model('Provider');
        $this->serviceModel  = $this->model('APIService');
    }

    protected function requireAdmin()
    {
        if (!isLoggedIn() || !isAdmin()) {
            flash('error', 'Access denied. Admin only.');
            redirect('dashboard');
        }
    }

    /* =========================
       API INSTANCES
    ========================= */

    public function index()
    {
        $this->requireAdmin();

        $data = [
            'title'     => 'API Instances',
            'instances' => $this->instanceModel->getAllInstances()
        ];

        $this->view('admin/api', $data);
    }

    public function create()
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'provider_id' => (int)($_POST['provider_id'] ?? 0),
                'name'        => trim($_POST['name'] ?? ''),
                'base_url'    => trim($_POST['base_url'] ?? ''),
                'api_key'     => trim($_POST['api_key'] ?? ''),
                'status'      => ($_POST['status'] ?? 'active') === 'active' ? 'active' : 'inactive'
            ];

            if ($this->instanceModel->createInstance($data)) {
                flash('success', 'API instance added successfully!');
                redirect('api');
            }

            flash('error', 'Failed to add API instance', 'alert alert-danger');
        }

        $data = [
            'title'     => 'Add API Instance',
            'providers' => $this->providerModel->getActiveProviders()
        ];

        $this->view('admin/api_create', $data);
    }

    public function edit($id)
    {
        $this->requireAdmin();

        $instance = $this->instanceModel->getInstanceById((int)$id);
        if (!$instance) {
            flash('error', 'API instance not found', 'alert alert-danger');
            redirect('api');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'provider_id' => (int)($_POST['provider_id'] ?? $instance->provider_id),
                'name'        => trim($_POST['name']),
                'base_url'    => trim($_POST['base_url']),
                'api_key'     => trim($_POST['api_key']),
                'status'      => ($_POST['status'] ?? 'active') === 'active' ? 'active' : 'inactive'
            ];

            if ($this->instanceModel->updateInstance((int)$id, $data)) {
                flash('success', 'API instance updated successfully');
                redirect('api');
            }

            flash('error', 'Failed to update API instance', 'alert alert-danger');
        }

        $data = [
            'title'     => 'Edit API Instance',
            'instance'  => $instance,
            'providers' => $this->providerModel->getActiveProviders()
        ];

        $this->view('admin/api_edit', $data);
    }

    public function delete($id)
    {
        $this->requireAdmin();

        if ($this->instanceModel->deleteInstance((int)$id)) {
            flash('success', 'API instance deleted successfully');
        } else {
            flash(
                'error',
                'Cannot delete instance with existing services. Remove services first.',
                'alert alert-danger'
            );
        }

        redirect('api');
    }

    /* =========================
       SERVICE SYNC
    ========================= */

    public function syncServices($instanceId)
    {
        $this->requireAdmin();

        $instance = $this->instanceModel->getInstanceById((int)$instanceId);
        if (!$instance) {
            flash('error', 'API instance not found', 'alert alert-danger');
            redirect('api');
        }

        $provider = $this->providerModel->getProviderById($instance->provider_id);
        if (!$provider || empty($provider->service_schema)) {
            flash('error', 'Provider service schema not configured', 'alert alert-danger');
            redirect('api');
        }

        $schema = json_decode($provider->service_schema, true);
        $servicesSchema = $schema['services'] ?? null;

        if (!$servicesSchema) {
            flash('error', 'Invalid provider service schema', 'alert alert-danger');
            redirect('api');
        }

        $endpoint = rtrim($instance->base_url, '/') . ($servicesSchema['endpoint'] ?? '');
        $method   = strtoupper($servicesSchema['method'] ?? 'POST');
        $payload  = $servicesSchema['payload'] ?? [];
        $payload['key'] = $instance->api_key;

        $response = $this->callExternalAPI($endpoint, $method, $payload);

        // 🔑 FIX: handle wrapped APIs like PayToSMM
        if (isset($response['data']) && is_array($response['data'])) {
            $response = $response['data'];
        }

        if (!is_array($response)) {
            flash('error', 'Failed to fetch services from provider', 'alert alert-danger');
            redirect('api');
        }

        $map      = $servicesSchema['map'] ?? [];
        $defaults = $servicesSchema['defaults'] ?? [];

        $synced = 0;

        foreach ($response as $raw) {
            $externalId = $raw[$map['id']] ?? null;
            if (!$externalId) continue;

            $rate = (float)($raw[$map['price']] ?? 0);
            if ($rate <= 0) continue;

            $this->serviceModel->upsertService([
                'api_instance_id'     => (int)$instanceId,
                'external_service_id' => (string)$externalId,
                'name'                => (string)($raw[$map['name']] ?? 'Unnamed'),
                'category'            => $raw[$map['category']] ?? null,
                'type'                => $raw[$map['type']] ?? null,
                'api_rate'            => $rate,
                'final_price'         => $rate,
                'min_qty'             => (int)($raw[$map['min']] ?? $defaults['min'] ?? 1),
                'max_qty'             => (int)($raw[$map['max']] ?? $defaults['max'] ?? 100000),
                'extra'               => $raw
            ]);

            $synced++;
        }

        flash('success', "Service sync completed. {$synced} services processed.");
        redirect('api');
    }

    /* =========================
       API SERVICES
    ========================= */

    public function services($instanceId = null)
    {
        $this->requireAdmin();

        $services = $instanceId
            ? $this->serviceModel->getByInstance((int)$instanceId)
            : $this->serviceModel->getAll();

        $this->view('admin/api_services', [
            'title'    => 'Manage API Services',
            'services' => $services
        ]);
    }

    public function updateService()
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('api/services');
        }

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $id      = (int)($_POST['id'] ?? 0);
        $markup  = isset($_POST['markup']) ? (float)$_POST['markup'] : 0.0;
        $status  = ($_POST['status'] ?? 'inactive') === 'active' ? 'active' : 'inactive';
        $visible = ($_POST['visible'] ?? 'no') === 'yes' ? 'yes' : 'no';

        $service = $this->serviceModel->getById($id);
        if (!$service) {
            flash('error', 'Service not found', 'alert alert-danger');
            redirect('api/services');
        }

        if ($markup < 0) {
            $markup = 0;
        }

        if ($this->serviceModel->updateAdminSettings($id, $markup, $status, $visible)) {
            flash('success', 'Service updated successfully');
        } else {
            flash('error', 'Failed to update service', 'alert alert-danger');
        }

        $redirectPath = 'api/services' . ($service->api_instance_id ? '/' . $service->api_instance_id : '');
        redirect($redirectPath);
    }

    /* =========================
       UTIL
    ========================= */

    private function callExternalAPI(string $url, string $method, array $payload)
    {
        $ch = curl_init();

        if ($method === 'GET') {
            $url .= '?' . http_build_query($payload);
        }

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json']
        ]);

        if ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}
