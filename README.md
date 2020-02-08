# Fetch User Posts from Facebook Timeline with Graph API using PHP

Facebook PHP SDK provides an easy way to access Facebook API. The PHP SDK helps to authenticate and login with Facebook account on the web application. After the authentication, various information can be fetched from the user’s Facebook account using Graph API. Facebook Graph API is very useful to retrieve the profile data and feed from the user timeline.

If you want to enhance the Facebook OAuth functionality and get additional information from the FB account, Graph API is the best option. After the authentication with Facebook, you can retrieve the profile and posts data from the user’s account. In this tutorial, we will show you how to fetch the user post from the Facebook timeline with Graph API using PHP.

In this Facebook post parser script, the following functionality will be implemented using PHP.

- Login with Facebook using PHP SDK v5.
- Fetch profile information from Facebook using Graph API.
- Fetch the user’s posts from Facebook Timeline using Graph API.

```php
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
 
// Include the autoloader provided in the SDK 
require_once __DIR__ . '/facebook-php-graph-sdk/src/Facebook/autoload.php'; 
 
// Include required libraries 
use Facebook\Facebook; 
use Facebook\Exceptions\FacebookResponseException; 
use Facebook\Exceptions\FacebookSDKException; 
```php