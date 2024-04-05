<?php

require_once "Database.php";

class User
{
    private static $instance;
    private $db;

    private function __construct($database)
    {
        $this->db = $database;
    }

    public static function getInstance($database)
    {
        if (!self::$instance) {
            self::$instance = new User($database);
        }
        return self::$instance;
    }

    public function createUser($email, $birthdate, $username, $firstname, $lastname, $password)
    {
        try {
            $pdo = $this->db->getConnection();
            $query = "INSERT INTO users (email, birthdate, username, firstname, lastname, password_hash, genre) VALUES (:email, :birthdate, :username, :firstname, :lastname, :password, NULL)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':birthdate', $birthdate);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':firstname', $firstname);
            $stmt->bindParam(':lastname', $lastname);
            $stmt->bindParam(':password', $password);
            $stmt->execute();
            $id = $pdo->lastInsertId();
            $this->createUserPreferences($id);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function createUserPreferences($id)
    {
        try {
            $pdo = $this->db->getConnection();
            $query = "INSERT INTO users_preferences (user_id) VALUES (:id)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    function softDeleteUser($id)
    {
        try {
            $pdo = $this->db->getConnection();
            $query = "UPDATE users SET isDeleted = 1 WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    

    public function getUserByEmail($email)
    {
        try {
            $query = "SELECT * FROM users WHERE email = :email";
            $stmt = $this->db->getConnection()->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            return $user;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function getUserById($id)
    {
        try {
            $query = "SELECT * FROM users WHERE id = :id";
            $stmt = $this->db->getConnection()->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            return $user;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function hashpwd($password)
    {
        $salt = "vive le projet tweet_academy";
        $noHashedPwd = $salt . $password;
        $newPwd = hash('ripemd160', $noHashedPwd);
        return $newPwd;
    }

    function getUsersPreferences($id)
    {
        try {
            if (!$this->db) {
                throw new Exception("Database connection is null");
            }

            $query = "SELECT * FROM users_preferences WHERE user_id = :id";
            $stmt = $this->db->getConnection()->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $preferences = $stmt->fetch(PDO::FETCH_ASSOC);
            return $preferences;
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            return false;
        }
    }

    public function updatePublicData($id, $bio, $localisation, $website) {
        $pdo = $this->db->getConnection();

        $sql = "SELECT COUNT(*) AS count FROM users_preferences WHERE user_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $dataExists = ($result['count'] > 0);

        if ($dataExists) {
            $sql = "UPDATE users_preferences SET bio = :bio, localisation = :localisation, website = :website WHERE user_id = :id";
        } else {
            $sql = "INSERT INTO users_preferences (user_id, bio, localisation, website) VALUES (:id, :bio, :localisation, :website)";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':bio', $bio, PDO::PARAM_STR);
        $stmt->bindValue(':localisation', $localisation, PDO::PARAM_STR);
        $stmt->bindValue(':website', $website, PDO::PARAM_STR);
        $stmt->execute();
    }
    
    public function updatePfp($id, $profile_picture)
    {
        try {
            $pdo = $this->db->getConnection();
    
            $checkQuery = $pdo->prepare("SELECT COUNT(*) FROM users_preferences WHERE user_id = :id");
            $checkQuery->execute(array(':id' => $id));
            $rowCount = $checkQuery->fetchColumn();
    
            if ($rowCount == 0) {
                $insertQuery = $pdo->prepare("INSERT INTO users_preferences (user_id, profile_picture) VALUES (:id, :profile_picture)");
                $insertQuery->execute(array(':id' => $id, ':profile_picture' => $profile_picture));
                if ($insertQuery->rowCount() > 0) {
                    $insertQuery->closeCursor();
                    return true;
                }
            } else {
                $updateQuery = $pdo->prepare("UPDATE users_preferences SET profile_picture = :profile_picture WHERE user_id = :id");
                $updateQuery->execute(array(':id' => $id, ':profile_picture' => $profile_picture));
                if ($updateQuery->rowCount() > 0) {
                    $updateQuery->closeCursor();
                    return true;
                }
            }
    
            return false; 
    
        } catch (PDOException $e) {
            error_log("Error updating profile: " . $e->getMessage());
            return false;
        }
    }
    

    public function updateProfileBanner($id, $profile_banner)
    {
        try {
            $pdo = $this->db->getConnection();
            $checkQuery = $pdo->prepare("SELECT COUNT(*) FROM users_preferences WHERE user_id = :id");
            $checkQuery->execute(array(':id' => $id));
            $rowCount = $checkQuery->fetchColumn();

            if ($rowCount == 0) {
                $insertQuery = $pdo->prepare("INSERT INTO users_preferences (user_id, profile_banner) VALUES (:id, :profile_banner)");
                $insertQuery->execute(array(':id' => $id, ':profile_banner' => $profile_banner));
            } else {
                $updateQuery = $pdo->prepare("UPDATE users_preferences SET profile_banner = :profile_banner WHERE user_id = :id");
                $updateQuery->execute(array(':id' => $id, ':profile_banner' => $profile_banner));
            }

            if ($insertQuery && $insertQuery->rowCount() > 0) {
                return true;
            } elseif ($updateQuery && $updateQuery->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log("Error updating profile: " . $e->getMessage());
            return false;
        }
    }
    public function updateProfile($id, $username, $email, $firstname, $lastname, $birthdate)
    {
        try {
            $pdo = $this->db->getConnection();

            $emailExist = $this->emailExists($email);

            $updateQuery = $pdo->prepare("UPDATE users SET username = :username, email = :email, firstname = :firstname, lastname = :lastname, birthdate = :birthdate WHERE id = :id");

            $updateQuery->execute(
                array(
                    ':id' => $id,
                    ':username' => $username,
                    ':email' => $email,
                    ':firstname' => $firstname,
                    ':lastname' => $lastname,
                    ':birthdate' => $birthdate
                )
            );

            if ($updateQuery->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log("Error updating profile: " . $e->getMessage());
            return false;
        }
    }


    public function updatePassword($id, $hashedNewPassword)
    {
        try {
            $pdo = $this->db->getConnection();

            $updateQuery = $pdo->prepare("UPDATE users SET password_hash = :password WHERE id = :id");

            $updateQuery->execute(array(':password' => $hashedNewPassword, ':id' => $id));

            if ($updateQuery->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log("Error updating password: " . $e->getMessage());
            return false;
        }
    }

    function emailExists($email)
    {
        try {
            $query = "SELECT COUNT(*) FROM users WHERE email = ?";
            $stmt = $this->db->getConnection()->prepare($query);
            $stmt->bindParam(1, $email);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            return $count > 0;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function getUserByUsername($username)
    {
        try {
            $query = "SELECT * FROM users WHERE username = :username";
            $stmt = $this->db->getConnection()->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            return $user;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

}