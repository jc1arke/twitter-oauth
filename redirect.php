<?php

/*
|-------------------------------------------------------------------
| REDIRECT USERS
|-------------------------------------------------------------------
|
| File used to redirect users on the site
|
*/

/*
|
| Start the session handler
|
*/
session_start();

/*
|
| Load the libraries
|
*/
require_once dirname( __FILE__ ) . "/config.php";
require_once dirname( __FILE__ ) . "/twitter_lib/twitter.oauth.php";

/*
|
| Build TwitterOAuth object with client credentials
|
*/
$connection = new TwitterOAuth( $config["twitter"]["twitter_api_key"], $config["twitter"]["twitter_consumer_secret"] );

/*
|
| Get temporary credentials
|
*/
$request_token = $connection -> getRequestToken( $config["twitter"]["twitter_callback_url"] );

/*
|
| Save temporary credentials to session
|
*/
$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

/*
|
| If last connection failed, don't display authorization link
|
*/
switch( $connection -> http_code ) :
	case 200	:	/*
							|
							| Build authorize URL and redirect user to Twitter
							|
							*/
							$url = $connection -> getAuthorizeURL( $token );
							header( 'Location: ' . $url );
							break;
	default		:	/*
							|
							| Show notification if something went wrong...
							|
							*/
							echo 'Could not connect to Twitter. Refresh the page or try again later...';
							break;
endswitch;

/* End of file: redirect.php */
/* File location: /var/www/php.tuts.local/week8/redirect.php */
