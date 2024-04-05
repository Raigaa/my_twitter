<?php

class Database
{
    private static $instance;
    private $host = "localhost";
    private $user = "root";
    private $password = "root";
    private $database = "tweet_academy";
    private $conn;

    private function __construct()
    {
        $this->connect();
        $this->id = uniqid();
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function connect()
    {
        try {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->database", $this->user, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }

    public function closeConnection()
    {
        $this->conn = null;

    }

    public function isConnected()
    {   
        return $this->conn !== null;
    }
}