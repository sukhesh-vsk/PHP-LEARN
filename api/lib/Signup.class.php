<?php

require_once('Database.class.php');
require_once(__DIR__ . '/../vendor/autoload.php');

class SignUp
{
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
    public function __construct($username, $password, $email)
    {
        $this->db = Database::getConnection();

        $this->username = $username;
        $this->password = $this->hashPassword($password);
        $this->email = $email;
        $this->token = $this->genToken();


        if($this->sendVerifyEmail()) {
            $query = "INSERT INTO `apis`.`auth` (username, password, email, active, token)  values('$this->username', '$this->password', '$this->email', 0, '$this->token')";
        } else {
            throw new Exception("Unable to complete verification..");
        }

        if (!mysqli_query($this->db, $query)) {

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
    public function hashPassword()
    {
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
    private function genToken()
    {
        $bytes = random_bytes(16);
        return bin2hex($bytes);
    }

    /*
     *  sendVerifyEmail() -> boolean
     * 
     *  @noparams
     * 
     *  Sends a verification email to the user.
     *  Uses Brevo API to send the email.
     *  Returns false if unable to send email. 
     *  
     *  (currently for testing purpose this returns false always, 
     *    so that the user is not created in the database.)
     */

    public function sendVerifyEmail()
    {
        $config_json = file_get_contents(__DIR__ . "/../../../env.json");
        $env = json_decode($config_json, true);

        // Configure API key authorization: api-key
        $config = Brevo\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $env['api_key']);
        // Configure API key authorization: partner-key
        $config = Brevo\Client\Configuration::getDefaultConfiguration()->setApiKey('partner-key', $env['api_key']);

        $apiInstance = new Brevo\Client\Api\TransactionalEmailsApi(
            new GuzzleHttp\Client(),
            $config
        );
<<<<<<< HEAD

        $htmlContent = file_get_contents(__DIR__ . "/../templates/template.html");
        $htmlContent = preg_replace("{{{token}}}", $this->token, $htmlContent);
        $htmlContent = preg_replace("{{{usermail}}}", $this->email, $htmlContent);
        $htmlContent = preg_replace("{{{username}}}", $this->username, $htmlContent);

=======
<<<<<<< HEAD
>>>>>>> 087d8f3 (Updated file)
        // \Brevo\Client\Model\SendSmtpEmail | Values to send a transactional email
        $sendSmtpEmail = new \Brevo\Client\Model\SendSmtpEmail([
            'subject' => 'Test Email',
            'sender' => ['name' => 'ClassPro','email' => 'noreply@saakletu.com'],
            'replyTo' => ['name' => 'Admin', 'email' => 'sukhesh.vsk2005@gmail.com'],
            'to' => [['name' => $this->username, 'email' => $this->email]],
            'htmlContent' => $htmlContent,
        ]);

        try {
            $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
<<<<<<< HEAD
            return true;
=======
            print_r($result);
=======

        $htmlContent = file_get_contents(__DIR__ . "/../templates/template.html");
        $htmlContent = preg_replace("{{{token}}}", $this->token, $htmlContent);
        $htmlContent = preg_replace("{{{usermail}}}", $this->email, $htmlContent);
        $htmlContent = preg_replace("{{{username}}}", $this->username, $htmlContent);

        // \Brevo\Client\Model\SendSmtpEmail | Values to send a transactional email
        $sendSmtpEmail = new \Brevo\Client\Model\SendSmtpEmail([
            'subject' => 'Test Email',
            'sender' => ['name' => 'ClassPro','email' => 'noreply@saakletu.com'],
            'replyTo' => ['name' => 'Admin', 'email' => 'sukhesh.vsk2005@gmail.com'],
            'to' => [['name' => $this->username, 'email' => $this->email]],
            'htmlContent' => $htmlContent,
        ]);

        try {
            $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
            return true;
>>>>>>> 10c88c9 (Added email authentication api)
>>>>>>> 087d8f3 (Updated file)
        } catch (Exception $e) {
            echo 'Exception when calling TransactionalEmailsApi->sendTransacEmail: ', $e->getMessage(), PHP_EOL;
        }
        return false;
    }

    /*
     *  getInsertID() -> user id (int) 
     * 
     *  @noparams
     * 
     *  returns the last inserted ID from database.
     */
    public function getInsertID()
    {
        return mysqli_insert_id($this->db);
    }

}
