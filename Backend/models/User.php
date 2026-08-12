<?php

/**
 * User Model
 */

require_once __DIR__ . '/../config/database.php';

class User
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function findById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([(int)$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }

    public function findByEmail($email)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }

    public function findByEmailVerification($token)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE verification_token = ?");
            $stmt->execute([$token]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }

    public function emailExists($email)
    {
        try {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            return (bool)$stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }

    public function create($data)
    {
        try {
            $sql = "INSERT INTO users (full_name, email, password_hash, role, status, is_verified, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $this->db->prepare($sql);
            $res = $stmt->execute([
                $data['full_name'],
                $data['email'],
                $data['password_hash'],
                $data['role'] ?? 'fresher',
                $data['status'] ?? 'active',
                $data['is_verified'] ?? 0
            ]);
            return $res ? $this->db->lastInsertId() : false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function updatePassword($userId, $hash)
    {
        try {
            $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            return $stmt->execute([$hash, (int)$userId]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function update($id, $data)
    {
        try {
            $fields = [];
            $params = [];
            foreach ($data as $key => $val) {
                $fields[] = "$key = ?";
                $params[] = $val;
            }
            $params[] = (int)$id;
            $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (Exception $e) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
            return $stmt->execute([(int)$id]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function getUsers($limit = 10, $offset = 0, $search = '', $role = '', $status = '')
    {
        try {
            $sql = "SELECT * FROM users WHERE 1=1";
            $params = [];

            if (!empty($search)) {
                $sql .= " AND (full_name LIKE ? OR email LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }
            if (!empty($role)) {
                $sql .= " AND role = ?";
                $params[] = $role;
            }
            if (!empty($status)) {
                $sql .= " AND status = ?";
                $params[] = $status;
            }

            $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
            $params[] = (int)$limit;
            $params[] = (int)$offset;

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getTotalUsers($search = '', $role = '', $status = '')
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM users WHERE 1=1";
            $params = [];

            if (!empty($search)) {
                $sql .= " AND (full_name LIKE ? OR email LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }
            if (!empty($role)) {
                $sql .= " AND role = ?";
                $params[] = $role;
            }
            if (!empty($status)) {
                $sql .= " AND status = ?";
                $params[] = $status;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $row = $stmt->fetch();
            return $row ? (int)$row['count'] : 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function getRoles()
    {
        return ['admin', 'mentor', 'fresher'];
    }

    public function getUserStats($id)
    {
        return [
            'sessions' => 0,
            'courses' => 0,
            'certificates' => 0
        ];
    }

    public function markEmailAsVerified($userId)
    {
        return $this->update($userId, [
            'is_verified' => 1,
            'verification_token' => null,
            'email_verified_at' => date('Y-m-d H:i:s')
        ]);
    }
}