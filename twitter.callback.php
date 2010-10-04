<?php

/*
|-------------------------------------------------------------------
| TWITTER CALLBACK FILE
|-------------------------------------------------------------------
|
| The callback file that Twitter redirects to upon authentication requests.
| Get the user's access tokens from Twitter, verify credentials and 
| redirect based on response from Twitter
|
*/

/*
|
| Start session
|
*/
session_start(); // Read more : http://www.php.net/session

/*
|
| Load the libraries
|
*/
include_once dirname( __FILE__ ) . "/config.php";
include_once dirname( __FILE__ ) . "/twitter_lib/twitter.oauth.php";

/*
|
| If access tokens are not available redirect to connect page. 
|
*/
#if( empty( $_SESSION['access_token'] ) || empty( $_SESSION['access_token']['oauth_token'] ) || empty( $_SESSION['access_token']['oauth_token_secret'] ) ) :
if( empty( $_SESSION['oauth_token'] ) || empty( $_SESSION['oauth_token_secret'] ) ) :
  header('Location: ./clearsessions.php'); // Read more : http://www.php.net/header
endif;

/*
|
| Get user access tokens out of the session. 
|
*/
# $access_token = $_SESSION['access_token'];
$access_token = array( 'oauth_token' => $_SESSION['oauth_token'], 'oauth_token_secret' => $_SESSION['oauth_token_secret'] );

/* 
|
| Create a TwitterOauth object with consumer/user tokens. 
|
*/
$connection = new TwitterOAuth(
	$config["twitter"]["twitter_api_key"], 
	$config["twitter"]["twitter_consumer_secret"], 
	$access_token['oauth_token'], 
	$access_token['oauth_token_secret']
);

/* 
| 
| Request access tokens from twitter 
|
*/
$access_token = $connection -> getAccessToken( $_REQUEST['oauth_verifier'] );

/* 
| 
| Save the access tokens. Normally these would be saved in a database for future use. 
|
*/
$_SESSION['access_token'] = $access_token;

/* 
|
| Remove no longer needed request tokens 
|
*/
unset( $_SESSION['oauth_token'] );
unset( $_SESSION['oauth_token_secret'] );

/* 
|
| If HTTP response is 200 continue otherwise send to connect page to retry 
|
*/
if( $connection -> http_code == 200 ) :
  /*
  |
  | The user has been verified and the access tokens can be saved for future use 
  |
  */
  $_SESSION['status'] = 'verified';
  header('Location: ./twitter.updater.php'); // Read more : http://www.php.net/header
else :
  /* 
  |
  | Save HTTP status for error dialog on connnect page.
  |
  */
  header('Location: ./clearsessions.php'); // Read more : http://www.php.net/header
endif;

/* End of file: callback.php */
/* File location: /var/www/php.tuts/local/week8/callback.php */
