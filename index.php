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

     // Initialize User class 
     $user = new User(); 
     
     // Getting user's profile data 
     $fbUserData = array(); 
     $fbUserData['oauth_uid']  = !empty($fbUser['id'])?$fbUser['id']:''; 
     $fbUserData['first_name'] = !empty($fbUser['first_name'])?$fbUser['first_name']:''; 
     $fbUserData['last_name']  = !empty($fbUser['last_name'])?$fbUser['last_name']:''; 
     $fbUserData['email']      = !empty($fbUser['email'])?$fbUser['email']:''; 
     $fbUserData['gender']     = !empty($fbUser['gender'])?$fbUser['gender']:''; 
     $fbUserData['picture']    = !empty($fbUser['picture']['url'])?$fbUser['picture']['url']:''; 
     $fbUserData['link']       = !empty($fbUser['link'])?$fbUser['link']:''; 
      
     // Insert or update user data to the database 
     $fbUserData['oauth_provider'] = 'facebook'; 
     $userData = $user->checkUser($fbUserData); 
     $userID = $userData['id']; 
      
     // Storing user data in the session 
     $_SESSION['userData'] = $userData; 
      
     if($userData){ 
         // Fetch the user's feed 
         $userFeeds = $fb->get("/".$fbUser['id']."/feed?limit=".FB_POST_LIMIT, $accessToken); 
         $feedBody = $userFeeds->getDecodedBody(); 
         $feedData = $feedBody["data"]; 
          
         if(!empty($feedData)){ 
             // Delete old posts from the database 
             $user->deletePosts($userID); 
              
             $postData = array(); 
             foreach($feedData as $row){ 
                 if(!empty($row['id'])){ 
                     $postID = $row['id']; 
                      
                     // Fetch the post info 
                     $response = $fb->get('/'.$postID, $accessToken); 
                     $data = $response->getDecodedBody(); 
                      
                     // Fetch post attachment info 
                     $response = $fb->get('/'.$postID.'/attachments', $accessToken); 
                     $attchData = $response->getDecodedBody(); 
                      
                     $postData['user_id'] = $userID; 
                     $postData['post_id'] = $data['id']; 
                     $postData['message'] = $data['message']; 
                     $postData['created_time'] = $data['created_time']; 
                     $postData['published_by'] = $fbUser['id']; 
                     $postData['attach_type'] = !empty($attchData['data'][0]['type'])?$attchData['data'][0]['type']:''; 
                     $postData['attach_title'] = !empty($attchData['data'][0]['title'])?$attchData['data'][0]['title']:''; 
                     $postData['attach_image'] = !empty($attchData['data'][0]['media']['image']['src'])?$attchData['data'][0]['media']['image']['src']:''; 
                     $postData['attach_link'] = !empty($attchData['data'][0]['url'])?$attchData['data'][0]['url']:''; 
                      
                     // Insert post data in the database 
                     $insertPost = $user->insertPost($postData); 
                 } 
             } 
         } 
     } 
      
     // Get logout url 
     $logoutURL = $helper->getLogoutUrl($accessToken, FB_REDIRECT_URL.'logout.php'); 

     // Render Facebook profile data 
    if(!empty($userData)){ 
        $output  = '<h2>Facebook Profile Details</h2>'; 
        $output .= '<div class="ac-data">'; 
        $output .= '<img src="'.$userData['picture'].'"/>'; 
        $output .= '<p><b>Facebook ID:</b> '.$userData['oauth_uid'].'</p>'; 
        $output .= '<p><b>Name:</b> '.$userData['first_name'].' '.$userData['last_name'].'</p>'; 
        $output .= '<p><b>Email:</b> '.$userData['email'].'</p>'; 
        $output .= '<p><b>Gender:</b> '.$userData['gender'].'</p>'; 
        $output .= '<p><b>Logged in with:</b> Facebook'.'</p>'; 
        $output .= '<p><b>Profile Link:</b> <a href="'.$userData['link'].'" target="_blank">Click to visit Facebook page</a></p>'; 
        $output .= '<p><b>Logout from <a href="'.$logoutURL.'">Facebook</a></p>'; 
        $output .= '</div>'; 
    }else{ 
        $output = '<h3 style="color:red">Some problem occurred, please try again.</h3>'; 
    } 
}else{ 
    // Get login url 
    $permissions = ['email']; // Optional permissions 
    $loginURL = $helper->getLoginUrl(FB_REDIRECT_URL, $permissions); 
     
    // Render Facebook login button 
    $output = '<a href="'.htmlspecialchars($loginURL).'"><img src="images/fb-login-btn.png"></a>'; 
} 
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
<title>Login with Facebook using PHP </title>
<meta charset="utf-8">
<!-- stylesheet file -->
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <div class="fb-box">
        <!-- Display login button / Facebook profile information -->
        <?php echo $output; ?>
    </div>
	
    <!-- List user posts -->
    <?php
   
    if(!empty($userID)){ 
        // Fetch posts from the database 
        $con = array( 
            'where' => array('user_id' => $userID), 
            'limit' => FB_POST_LIMIT 
        ); 
        $posts = $user->getPosts($con); 
         
        if(!empty($posts)){ 
    ?>
        <div class="post-list">
            <h2>Facebook Feeds</h2>
            <?php foreach($posts as $row){ 
                $image = !empty($row['attach_image'])?'<img src="'.$row['attach_image'].'"/>':''; 
                $title = (strlen($row['attach_title'])>55)?substr($row['attach_title'],0,55):$row['attach_title']; 
                $message = (strlen($row['message'])>120)?substr($row['message'],0,110).'...':$row['message']; 
            ?>
            <a href="<?php echo $row['attach_link']; ?>" target="_blank">
            <div class="pbox">
                <div class="img"><?php echo $image; ?></div>
                <div class="cont">
                    <h4><?php echo $title; ?></h4>
                    <p><?php echo $message; ?></p>
                </div>
            </div>
            </a>
            <?php } ?>
        </div>
    <?php } 
    } ?>
</div>
</body>
</html>