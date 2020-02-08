<?php 
/* 
 * Database and API Configuration 
 */ 
 
// Database configuration 
define('DB_HOST', 'facebook'); 
define('DB_USERNAME', 'root'); 
define('DB_PASSWORD', ''); 
define('DB_NAME', ''); 
define('DB_USER_TBL', 'users'); 
define('DB_POST_TBL', 'user_posts'); 
 
// Facebook API configuration 
define('FB_APP_ID', 'Insert_Facebook_App_ID'); // Replace {app-id} with your app id 
define('FB_APP_SECRET', 'Insert_Facebook_App_Secret'); // Replace {app-secret} with your app secret 
define('FB_REDIRECT_URL', 'Callback_URL');  
define('FB_POST_LIMIT', 10); 
 
// Start session 
if(!session_id()){ 
    session_start(); 
} 

// Include the autoloader provided in the SDK 
require_once __DIR__ . '/facebook-php-graph-sdk/src/Facebook/autoload.php'; 
 
// Include required libraries 
use Facebook\Facebook; 
use Facebook\Exceptions\FacebookResponseException; 
use Facebook\Exceptions\FacebookSDKException; 
 
// Call Facebook API 
$fb = new Facebook(array( 
    'app_id' => FB_APP_ID, 
    'app_secret' => FB_APP_SECRET, 
    'default_graph_version' => 'v5.x', 
)); 

// Get redirect login helper 
$helper = $fb->getRedirectLoginHelper(); 
 
// Try to get access token 
try { 
    if(isset($_SESSION['facebook_access_token'])){ 
        $accessToken = $_SESSION['facebook_access_token']; 
    }else{ 
          $accessToken = $helper->getAccessToken(); 
    } 
} catch(FacebookResponseException $e) { 
     echo 'Graph returned an error: ' . $e->getMessage(); 
      exit; 
} catch(FacebookSDKException $e) { 
    echo 'Facebook SDK returned an error: ' . $e->getMessage(); 
      exit; 
}