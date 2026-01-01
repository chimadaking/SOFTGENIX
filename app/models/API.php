<?php
namespace App\Models;

class API extends BaseModel {
    public function getProviders() {
        return $this->fetchAll("SELECT * FROM api_providers");
    }

    public function addProvider($data) {
        $sql = "INSERT INTO api_providers (name, base_url, api_key, api_secret, status) VALUES (?, ?, ?, ?, ?)";
        return $this->query($sql, [$data['name'], $data['base_url'], $data['api_key'], $data['api_secret'], $data['status']]);
    }

    public function logRequest($userId, $endpoint, $method, $requestData, $responseCode, $responseData) {
        $sql = "INSERT INTO api_logs (user_id, endpoint, request_method, request_data, response_code, response_data, ip_address) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        return $this->query($sql, [$userId, $endpoint, $method, json_encode($requestData), $responseCode, json_encode($responseData), $_SERVER['REMOTE_ADDR']]);
    }

    public function createProvider($data) {
        $sql = "INSERT INTO api_providers (name, base_url, api_key, api_secret, status) VALUES (?, ?, ?, ?, 'active')";
        return $this->query($sql, [$data['name'], $data['base_url'], $data['api_key'], $data['api_secret']]);
    }

    public function testProvider($id) {
        $provider = $this->fetch("SELECT * FROM api_providers WHERE id = ?", [$id]);
        if (!$provider) return false;
        
        // Simulate API test (in real app, make actual HTTP request)
        return $provider->status == 'active';
    }

    public function getLogs() {
        return $this->fetchAll("SELECT * FROM api_logs ORDER BY created_at DESC LIMIT 50");
    }
}
