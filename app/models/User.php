<?php
namespace App\Models;

class User extends BaseModel {
    public function register($data) {
        $sql = "INSERT INTO users (username, email, password, first_name, last_name, status) VALUES (:username, :email, :password, :first_name, :last_name, 'active')";
        $params = [
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name']
        ];
        
        if ($this->query($sql, $params)) {
            $userId = $this->lastInsertId();
            // Assign default role (user)
            $this->query("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)", [$userId, ROLE_USER]);
            // Create wallet
            $this->query("INSERT INTO wallet_balances (user_id, balance) VALUES (?, 0)", [$userId]);
            // Create loyalty account
            $this->query("INSERT INTO loyalty_accounts (user_id) VALUES (?)", [$userId]);
            return true;
        }
        return false;
    }

    public function login($email, $password) {
        $user = $this->fetch("SELECT u.*, r.name as role FROM users u 
                             JOIN user_roles ur ON u.id = ur.user_id 
                             JOIN roles r ON ur.role_id = r.id 
                             WHERE u.email = ?", [$email]);
        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        return false;
    }

    public function findByEmail($email) {
        return $this->fetch("SELECT * FROM users WHERE email = ?", [$email]);
    }

    public function findById($id) {
        return $this->fetch("SELECT * FROM users WHERE id = ?", [$id]);
    }

    public function getAllUsers() {
        return $this->fetchAll("SELECT u.*, r.name as role FROM users u 
                               JOIN user_roles ur ON u.id = ur.user_id 
                               JOIN roles r ON ur.role_id = r.id");
    }

    public function updateProfile($userId, $data) {
        $sql = "UPDATE users SET first_name = :first_name, last_name = :last_name WHERE id = :id";
        $params = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'id' => $userId
        ];
        return $this->query($sql, $params);
    }

    public function changePassword($userId, $currentPassword, $newPassword) {
        $user = $this->findById($userId);
        if ($user && password_verify($currentPassword, $user->password)) {
            $sql = "UPDATE users SET password = :password WHERE id = :id";
            return $this->query($sql, ['password' => password_hash($newPassword, PASSWORD_BCRYPT), 'id' => $userId]);
        }
        return false;
    }

    public function updateStatus($userId, $status) {
        $sql = "UPDATE users SET status = ? WHERE id = ?";
        return $this->query($sql, [$status, $userId]);
    }

    public function updateRole($userId, $roleId) {
        $sql = "UPDATE user_roles SET role_id = ? WHERE user_id = ?";
        return $this->query($sql, [$roleId, $userId]);
    }

    public function deleteUser($userId) {
        $sql = "DELETE FROM users WHERE id = ?";
        return $this->query($sql, [$userId]);
    }
}
