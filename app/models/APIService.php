<?php
namespace App\Models;

class APIService extends BaseModel
{
    /**
     * Get all services for a specific API instance
     */
    public function getByInstance(int $instanceId)
    {
        return $this->fetchAll(
            "SELECT s.*, i.name AS instance_name
             FROM api_services s
             JOIN api_instances i ON i.id = s.api_instance_id
             WHERE s.api_instance_id = ?
             ORDER BY s.category ASC, s.name ASC",
            [$instanceId]
        );
    }

    /**
     * Get every service with instance context for admin view
     */
    public function getAll()
    {
        return $this->fetchAll(
            "SELECT s.*, i.name AS instance_name
             FROM api_services s
             JOIN api_instances i ON i.id = s.api_instance_id
             ORDER BY i.name ASC, s.category ASC, s.name ASC"
        );
    }

    /**
     * Get all visible & active services (for user-facing usage later)
     */
    public function getActiveVisible()
    {
        return $this->fetchAll(
            "SELECT *
             FROM api_services
             WHERE status = 'active'
               AND visible = 'yes'
             ORDER BY category ASC, name ASC"
        );
    }

    /**
     * Insert or update a service safely (UPSERT)
     *
     * - Preserves admin-controlled fields
     * - Updates API-controlled fields
     * - Never auto-enables or auto-exposes services
     */
    public function upsertService(array $data): bool
    {
        $sql = "
            INSERT INTO api_services (
                api_instance_id,
                external_service_id,
                name,
                category,
                type,
                api_rate,
                final_price,
                min_qty,
                max_qty,
                extra,
                status,
                visible,
                created_at
            ) VALUES (
                :api_instance_id,
                :external_service_id,
                :name,
                :category,
                :type,
                :api_rate,
                :final_price,
                :min_qty,
                :max_qty,
                :extra,
                'inactive',
                'no',
                NOW()
            )
            ON DUPLICATE KEY UPDATE
                name        = VALUES(name),
                category    = VALUES(category),
                type        = VALUES(type),
                api_rate    = VALUES(api_rate),
                min_qty     = VALUES(min_qty),
                max_qty     = VALUES(max_qty),
                extra       = VALUES(extra),
                updated_at  = NOW()
        ";

        return $this->query($sql, [
            ':api_instance_id'     => $data['api_instance_id'],
            ':external_service_id'=> $data['external_service_id'],
            ':name'               => $data['name'],
            ':category'           => $data['category'],
            ':type'               => $data['type'],
            ':api_rate'           => $data['api_rate'],
            ':final_price'        => $data['final_price'],
            ':min_qty'            => $data['min_qty'],
            ':max_qty'            => $data['max_qty'],
            ':extra'              => json_encode($data['extra'] ?? [])
        ]);
    }

    /**
     * Update admin-controlled fields only
     */
    public function updateAdminSettings(
        int $id,
        float $markup,
        string $status,
        string $visible
    ): bool {
        $sql = "
            UPDATE api_services
            SET
                markup = ?,
                final_price = api_rate + ?,
                status = ?,
                visible = ?,
                updated_at = NOW()
            WHERE id = ?
        ";

        return $this->query($sql, [
            $markup,
            $markup,
            $status,
            $visible,
            $id
        ]);
    }

    /**
     * Toggle service status (active / inactive)
     */
    public function toggleStatus(int $id): bool
    {
        $sql = "
            UPDATE api_services
            SET
                status = IF(status = 'active', 'inactive', 'active'),
                updated_at = NOW()
            WHERE id = ?
        ";

        return $this->query($sql, [$id]);
    }

    /**
     * Toggle service visibility (yes / no)
     */
    public function toggleVisibility(int $id): bool
    {
        $sql = "
            UPDATE api_services
            SET
                visible = IF(visible = 'yes', 'no', 'yes'),
                updated_at = NOW()
            WHERE id = ?
        ";

        return $this->query($sql, [$id]);
    }

    /**
     * Get a single service by ID
     */
    public function getById(int $id)
    {
        return $this->fetch(
            "SELECT *
             FROM api_services
             WHERE id = ?",
            [$id]
        );
    }

    /**
     * Count services for an API instance
     */
    public function countByInstance(int $instanceId): int
    {
        $row = $this->fetch(
            "SELECT COUNT(*) AS total
             FROM api_services
             WHERE api_instance_id = ?",
            [$instanceId]
        );

        return (int) ($row->total ?? 0);
    }
}
