<?php 
/* 
 * Database and API Configuration 
 */ 
 
// Database configuration 
define('DB_HOST', 'MySQL_Database_Host'); 
define('DB_USERNAME', 'MySQL_Database_Username'); 
define('DB_PASSWORD', 'MySQL_Database_Password'); 
define('DB_NAME', 'MySQL_Database_Name'); 
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