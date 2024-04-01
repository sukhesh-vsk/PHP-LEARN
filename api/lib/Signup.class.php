<?php

require_once('Database.class.php');

class SignUp {
    private $username, $password, $email, $token;
    
    private $db;

    
    /*
    *  Signup Constructor
    *
    *  params @username (string),
    *         @password (string), 
    *         @email (string)
    *
    *  Establishes database connection, hashes the password, creates random token.
    *  Bind Values to DB Query.
    *  
    *  Throws exception upon any MySQL error. Exception is catched and handled on index file.
    */
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
    
    /*
    *  hashPassword() -> hashed password (string)
    *   
    *  @noparams
    *
    *  Uses php's built-in password_hash() function.
    *  password_hash takes @password, @hashing_algorithm, @options (optional)
    *  Blowfish algorithm is used.
    *  cost factor is set to 12. For optimal security.
    */
    public function hashPassword() {
        $options = [
            "cost" => 12,
        ];
        
        return password_hash($this->password, PASSWORD_BCRYPT, $options);
    }
    
    /*
     *   genToken() -> random token (string)
     * 
     *   generates random 16 bytes binary value.
     *   converts it to hex format.
     */
    private function genToken() {
        $bytes = random_bytes(16);
        return bin2hex($bytes);
    }

    /*
     *  getInsertID() -> user id (int) 
     * 
     *  @noparams
     * 
     *  returns the last inserted ID from database.
     */
    public function getInsertID() {
        return mysqli_insert_id($this->db);
    }
}