<?php
namespace App\Models;

class Product extends BaseModel {
    public function getAllProducts() {
        return $this->fetchAll("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.status = 'active'");
    }

    public function getAllProductsAdmin() {
        return $this->fetchAll("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC");
    }

    public function getProductById($id) {
        return $this->fetch("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?", [$id]);
    }

    public function getCategories() {
        return $this->fetchAll("SELECT * FROM categories WHERE status = 'active'");
    }

    public function getProductsByCategory($categoryId) {
        return $this->fetchAll("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? AND p.status = 'active'", [$categoryId]);
    }

    public function create($data) {
        $sql = "INSERT INTO products (name, slug, category_id, description, short_description, price, original_price, stock, min_order, max_order, status) 
                VALUES (:name, :slug, :category_id, :description, :short_description, :price, :original_price, :stock, :min_order, :max_order, :status)";
        $params = [
            'name' => $data['name'],
            'slug' => $data['slug'],
            'category_id' => $data['category_id'],
            'description' => $data['description'],
            'short_description' => $data['short_description'],
            'price' => $data['price'],
            'original_price' => $data['original_price'],
            'stock' => $data['stock'],
            'min_order' => $data['min_order'],
            'max_order' => $data['max_order'],
            'status' => $data['status']
        ];
        return $this->query($sql, $params);
    }

    public function update($data) {
        $sql = "UPDATE products SET name = :name, slug = :slug, category_id = :category_id, description = :description, 
                short_description = :short_description, price = :price, original_price = :original_price, stock = :stock, 
                min_order = :min_order, max_order = :max_order, status = :status WHERE id = :id";
        $params = [
            'name' => $data['name'],
            'slug' => $data['slug'],
            'category_id' => $data['category_id'],
            'description' => $data['description'],
            'short_description' => $data['short_description'],
            'price' => $data['price'],
            'original_price' => $data['original_price'],
            'stock' => $data['stock'],
            'min_order' => $data['min_order'],
            'max_order' => $data['max_order'],
            'status' => $data['status'],
            'id' => $data['id']
        ];
        return $this->query($sql, $params);
    }

    public function delete($id) {
        return $this->query("DELETE FROM products WHERE id = ?", [$id]);
    }
}
