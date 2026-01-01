<?php
namespace App\Models;

class APIInstance extends BaseModel
{
    public function getAllInstances(): array
    {
        return $this->fetchAll(
            "SELECT i.*, p.name AS provider_name
             FROM api_instances i
             JOIN api_providers p ON p.id = i.provider_id
             ORDER BY i.created_at DESC"
        );
    }

    public function getInstanceById(int $id)
    {
        return $this->fetch(
            "SELECT i.*, p.name AS provider_name
             FROM api_instances i
             JOIN api_providers p ON p.id = i.provider_id
             WHERE i.id = ?",
            [$id]
        );
    }

    public function createInstance(array $data): bool
    {
        $sql = "
            INSERT INTO api_instances (
                provider_id,
                name,
                base_url,
                api_key,
                status,
                created_at,
                updated_at
            ) VALUES (
                :provider_id,
                :name,
                :base_url,
                :api_key,
                :status,
                NOW(),
                NOW()
            )
        ";

        return (bool) $this->query($sql, [
            ':provider_id' => $data['provider_id'],
            ':name'        => $data['name'],
            ':base_url'    => $data['base_url'],
            ':api_key'     => $data['api_key'],
            ':status'      => $data['status']
        ]);
    }

    public function updateInstance(int $id, array $data): bool
    {
        $sql = "
            UPDATE api_instances
            SET
                provider_id = :provider_id,
                name        = :name,
                base_url    = :base_url,
                api_key     = :api_key,
                status      = :status,
                updated_at  = NOW()
            WHERE id = :id
        ";

        return (bool) $this->query($sql, [
            ':provider_id' => $data['provider_id'],
            ':name'        => $data['name'],
            ':base_url'    => $data['base_url'],
            ':api_key'     => $data['api_key'],
            ':status'      => $data['status'],
            ':id'          => $id
        ]);
    }

    public function deleteInstance(int $id): bool
    {
        $services = $this->fetch(
            "SELECT COUNT(*) AS total FROM api_services WHERE api_instance_id = ?",
            [$id]
        );

        if ((int)($services->total ?? 0) > 0) {
            return false;
        }

        return (bool) $this->query(
            "DELETE FROM api_instances WHERE id = ?",
            [$id]
        );
    }
}
