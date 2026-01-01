<?php
namespace App\Models;

class Provider extends BaseModel
{
    public function getActiveProviders(): array
    {
        return $this->fetchAll(
            "SELECT * FROM api_providers WHERE status = 'active' ORDER BY name ASC"
        );
    }

    public function getProviderById(int $id)
    {
        return $this->fetch(
            "SELECT * FROM api_providers WHERE id = ?",
            [$id]
        );
    }
}
