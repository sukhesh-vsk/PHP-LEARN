<?php

require_once('Database.class.php');

class SignUp {
    private $username, $password, $email;
    
    private $db;

    public function __construct($username, $password, $email) {
        $this->db = Database::getConnection();

        $this->username = $username;
        $this->password = $password;
        $this->email = $email;

    }

    public function hashPassword() {
        $options = [
            "cost" => 12,
        ];

        return password_hash($this->password, PASSWORD_BCRYPT, $options);
    }
}