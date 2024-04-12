<?php
    error_reporting(E_ALL ^ E_DEPRECATED);
    require_once("REST.api.php");
    require_once("lib/Database.class.php");
    require_once("lib/Signup.class.php");

    class API extends REST {

        public $data = "";

        private $db = NULL;

        public function __construct(){
            parent::__construct();      // Init parent contructor
            
            $this->db = Database::getConnection();  // Initialize db connection
        }

        /*
            Application API start
        */

        /*
         *  Signup api  
         *  default url : https://localhost/api/signup
         * 
         *  POST request parameters:
         *  @username, @password, @email
         * 
         *  If all parameters were set :
         *     => Creates instance for Signup class. On Successful insert returns successful response.
         *     => Incase of MySql error. Returns response with error message.
         * 
         *  Else :
         *     => Returns response for Bad Request.
         */
        public function signup() {
            if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($this->_request['username']) && isset($this->_request['password']) && isset($this->_request['email'])) {
                
                try {
                    $s = new Signup($this->_request['username'], $this->_request['password'], $this->_request['email']);
                    $id = $s->getInsertID();
                    $data = [
                        "id" => $id,
                        "message" =>  "User Successfully Created"
                    ];
                    $data = $this->json($data);
                    
                    $this->response($data, 201);
                } catch(Exception $e) {
                    $data = [
                        "error" => "Signup Failed",
                        "message" => $e->getMessage()
                    ];
                    
                    $this->response(
                        $this->json($data),
                        409
                    );

                }

            } else {
                
                $data = [
                    "message" => "Bad Request"
                ];
                $data = $this->json($data);

                $this->response($data, 400);
            }
        }

        public function verify() {
            if($_SERVER['REQUEST_METHOD'] == "POST" && isset($this->_request['tkn'])) {
                $conn = Database::getConnection();
                $tkn = $this->_request['tkn'];

                $sql = "SELECT * FROM auth WHERE token=`$tkn`";
                $result = mysqli_fetch_assoc(mysqli_query($conn, $sql));

                if($result) {
                    $query = "UPDATE auth set active=1 where token=`$tkn`";
                    if(mysqli_query($conn, $query)) {
                        $data = [
                            "message" => "User Verified"
                        ];
                        $this->response($this->json($data), 200);
                    } else {
                        $data = [
                            "message" => "Verification Failed"
                        ];
                        $this->response($this->json($data), 409);
                    }
                }
            }
        }


        /*
            Application API end
        */

        // Generating hash and verifying -- testing 

        // This will cause error, as for duplicate entry. Comment out insert query in Signup class.
        public function gen_hash() {
            if(isset($this->_request['pass'])) {
                $user = new SignUp("", "admin", "");
                $usr_p = $user->hashPassword();

                $s = new SignUp("", $this->_request['pass'], "");

                $data = [
                    "real hash" => $usr_p,
                    "input hash" => $s->hashPassword(),
                    "verify" => password_verify($this->_request['pass'], $usr_p)
                ];

                $data = $this->json($data);
                $this->response($data, 200);
            }
        }

        public function verifyDB() {
            echo "hello";
        }

        // experimenting password hash -- testing api
        public function test_hash() {
            if(isset($this->_request['pass'])) {
                $salt = "hello";
                $pwd_u = "password";
                // $hash = crypt($this->_request['pass'], $salt);
                $hash = password_hash($pwd_u, '2y', array('cost' => 10));
                

                $data = array("password" => $this->_request['pass'], "hashed pwd" => $hash, "verification" => password_verify($this->_request['pass'], $hash));
                $data = $this->json($data);

                $this->response($data, 200);
            }
        }


        /*
         * Public method for access api.
         * This method dynmically call the method based on the query string
         *
         */
        public function processApi(){
            $func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
            if((int)method_exists($this,$func) > 0)
                $this->$func();
            else {
                $message = array("status" => "Not Found", "msg" => "The method you are requesting is not found :    ");
                $message = $this->json($message);
                $this->response($message,404);   // If the method not exist with in this class, response would be "Page not found".
            }
        }

        /*************API SPACE START*******************/

        private function about(){

            if($this->get_request_method() != "POST"){
                $error = array('status' => 'WRONG_CALL', "msg" => "The type of call cannot be accepted by our servers.");
                $error = $this->json($error);
                $this->response($error,406);
            }
            $data = array('version' => '0.1', 'desc' => 'This API is created by Blovia Technologies Pvt. Ltd., for the public usage for accessing data about vehicles.');
            $data = $this->json($data);
            $this->response($data,200);

        }

        private function verify_(){
            $user = $this->_request['user'];
            $password =  $this->_request['pass'];

            $flag = 0;
            if($user == "admin"){
                if($password == "adminpass123"){
                    $flag = 1;
                }
            }

            if($flag == 1){
                $data = [
                    "status" => "verified"
                ];
                $data = $this->json($data);
                $this->response($data,200);
            } else {
                $data = [
                    "status" => "unauthorized"
                ];
                $data = $this->json($data);
                $this->response($data,403);
            }
        }

        private function test(){
            $data = $this->json(getallheaders());
            $this->response($data,200);
        }


        /*************API SPACE END*********************/

        /*
            Encode array into JSON
        */
        private function json($data){
            if(is_array($data)){
                return json_encode($data, JSON_PRETTY_PRINT);
            } else {
                return "{}";
            }
        }

    }

    // Initiate Library

    $api = new API;
    $api->processApi();
?>