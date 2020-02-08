<?php 
// Include configuration file 
require_once 'config.php'; 
 
// Include User class 
require_once 'User.class.php'; 
 
if(isset($accessToken)){ 
    if(isset($_SESSION['facebook_access_token'])){ 
        $fb->setDefaultAccessToken($_SESSION['facebook_access_token']); 
    }else{ 
        // Put short-lived access token in session 
        $_SESSION['facebook_access_token'] = (string) $accessToken; 
         
          // OAuth 2.0 client handler helps to manage access tokens 
        $oAuth2Client = $fb->getOAuth2Client(); 
         
        // Exchanges a short-lived access token for a long-lived one 
        $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']); 
        $_SESSION['facebook_access_token'] = (string) $longLivedAccessToken; 
         
        // Set default access token to be used in script 
        $fb->setDefaultAccessToken($_SESSION['facebook_access_token']); 
    } 
     
    // Redirect the user back to the same page if url has "code" parameter in query string 
    if(isset($_GET['code'])){ 
        header('Location: ./'); 
    } 
     
    // Getting user's profile info from Facebook 
    try { 
        $graphResponse = $fb->get('/me?fields=name,first_name,last_name,email,link,gender,picture'); 
        $fbUser = $graphResponse->getGraphUser(); 
    } catch(FacebookResponseException $e) { 
        echo 'Graph returned an error: ' . $e->getMessage(); 
        session_destroy(); 
        // Redirect user back to app login page 
        header("Location: ./"); 
        exit; 
    } catch(FacebookSDKException $e) { 
        echo 'Facebook SDK returned an error: ' . $e->getMessage(); 
        exit; 
    } 