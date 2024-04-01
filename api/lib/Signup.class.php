<?php

require_once('Database.class.php');

class SignUp {
    private $username, $password, $email, $token, $id;
    
    private $db;

    public function __construct($username, $password, $email) {
        $this->db = Database::getConnection();

        $this->username = $username;
        $this->password = $this->hashPassword($password);
        $this->email = $email;
        $this->token = $this->genToken();

        $query = "INSERT INTO `apis`.`auth` (username, password, email, active, token)  values('$this->username', '$this->password', '$this->email', 0, '$this->token')";
        
        if( !mysqli_query($this->db, $query) ) {
            throw new Exception("Unable to Signup..");
        } 
        
    }
    
    public function hashPassword() {
        $options = [
            "cost" => 12,
        ];
        
        return password_hash($this->password, PASSWORD_BCRYPT, $options);
    }
    
    private function genToken() {
        $bytes = random_bytes(16);
        return bin2hex($bytes);
    }

    public function getInsertID() {
        return mysqli_insert_id($this->db);
    }
}