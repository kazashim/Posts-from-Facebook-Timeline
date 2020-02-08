<?php 
/* 
 * User Class 
 * This class is used for database related (connect, insert, update, and delete) operations 
 */ 
 
class User { 
    private $dbHost     = DB_HOST; 
    private $dbUsername = DB_USERNAME; 
    private $dbPassword = DB_PASSWORD; 
    private $dbName     = DB_NAME; 
    private $userTbl    = DB_USER_TBL; 
    private $postTbl    = DB_POST_TBL; 
     
    function __construct(){ 
        if(!isset($this->db)){ 
            // Connect to the database 
            $conn = new mysqli($this->dbHost, $this->dbUsername, $this->dbPassword, $this->dbName); 
            if($conn->connect_error){ 
                die("Failed to connect with MySQL: " . $conn->connect_error); 
            }else{ 
                $this->db = $conn; 
            } 
        } 
    } 

    function checkUser($userData = array()){ 
        if(!empty($userData)){ 
            // Check whether user data already exists in database 
            $prevQuery = "SELECT * FROM ".$this->userTbl." WHERE oauth_provider = '".$userData['oauth_provider']."' AND oauth_uid = '".$userData['oauth_uid']."'"; 
            $prevResult = $this->db->query($prevQuery); 
            if($prevResult->num_rows > 0){ 
                // Update user data if already exists 
                $query = "UPDATE ".$this->userTbl." SET first_name = '".$userData['first_name']."', last_name = '".$userData['last_name']."', email = '".$userData['email']."', gender = '".$userData['gender']."', picture = '".$userData['picture']."', link = '".$userData['link']."', modified = NOW() WHERE oauth_provider = '".$userData['oauth_provider']."' AND oauth_uid = '".$userData['oauth_uid']."'"; 
                $update = $this->db->query($query); 
            }else{ 
                // Insert user data 
                $query = "INSERT INTO ".$this->userTbl." SET oauth_provider = '".$userData['oauth_provider']."', oauth_uid = '".$userData['oauth_uid']."', first_name = '".$userData['first_name']."', last_name = '".$userData['last_name']."', email = '".$userData['email']."', gender = '".$userData['gender']."', picture = '".$userData['picture']."', link = '".$userData['link']."', created = NOW(), modified = NOW()"; 
                $insert = $this->db->query($query); 
            } 
             
            // Get user data from the database 
            $result = $this->db->query($prevQuery); 
            $userData = $result->fetch_assoc(); 
        } 
         
        // Return user data 
        return $userData; 
    } 