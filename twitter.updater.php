<?php
/*
|-------------------------------------------------------------------
| TWITTER UPDATE SCRIPT (PHP SECTION)
|-------------------------------------------------------------------
|
| Script used to present the user with a interface which he/she could
| use to make upates to their respective Twitter stream via jQuery
|
*/

/*
|
| Start the session handler
|
*/
session_start(); // Read more http://www.php.net/session

/*
|
| Include the configuration file as well as the Twitter Library
|
*/
include_once dirname(__FILE__) . "/config.php"; // Read more : http://www.php.net/dirname
include_once dirname(__FILE__) . "/twitter_lib/twitter.oauth.php";

/*
|
| If access tokens are not available redirect to connect page. 
|
*/
if( ! isset( $_SESSION['access_token'] ) || ! isset( $_SESSION['access_token']['oauth_token'] ) || ! isset( $_SESSION['access_token']['oauth_token_secret'] ) ) :
    header('Location: ./clearsessions.php'); // Read more : http://www.php.net/header
endif;

/* 
|
| Get user access tokens out of the session. 
|
*/
$access_token = $_SESSION['access_token'];

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
| Check for user submissions
|
*/
if( isset( $_GET["ajax"] ) && $_GET["ajax"] == TRUE ) :
	header("Content-Type: application/json");
	$result = array("error" => "");
	switch( $_GET["method"] ) :
		case "update"	:	$content = $connection -> post( 'statuses/update', array("status" => $_GET["msg"] ) );
										$result["feedback"] = $content;
										break;
		case "show"		:	$content = $connection -> get( 'statuses/user_timeline', array("count" => $_GET["limit"]) ); // return the specified amount of status updates (MAX: 200)
										if( ! empty( $content ) ) :
											foreach( $content as $status_update ) :
												$r = array(
													"id" => $status_update -> id,
													"text" => $status_update -> text
												);
												$result["statuses"][] = $r;
												unset( $r );
											endforeach;
										else :
											$result["error"] = "Unable to retrieve statuses :(";
										endif;
	endswitch;
	echo json_encode( $result );
	exit(0);
endif;

/* 
| 
| If method is set change API call made. Test is called by default. 
|
*/
// $content = $connection -> get( 'account/verify_credentials' );
$content = $connection -> get( 'help/test' ) == "ok" ? "Connected to <a href='http://twitter.com/' target='_blank'>Twitter</a>" : "Um, something is up..."; // Verify if we are connected and getting data from Twitter

/*
|
| Display the HTML interface
|
*/
include dirname( __FILE__ ) . "/html.inc";

/* End of file: twitter.updater.php */
/* File location: /var/www/php.tuts.local/week8/twitter.updater.php */
