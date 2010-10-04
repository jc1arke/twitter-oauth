<?php

/*
|-------------------------------------------------------------------
| CONNECT TO TWITTER
|-------------------------------------------------------------------
|
| Check if consumer token is set and if so send the user to get a
| request token
|
*/

session_start();

/*
|
| Exit with error message if the twitter_api_key or twitter_consumer_secret is not defined
|
*/
require_once dirname( __FILE__ ) . "/config.php";
if( $config["twitter"]["twitter_api_key"] === '' || $config["twitter"]["twitter_consumer_secret"] === '' ) :
	echo "You need a consumer key and secret to test. Get one free from <a href='http://dev.twitter.com/apps'>http://dev.twitter.com/apps</a>";
	exit(0);
endif;

/*
|
| Display the Twitter connect link
|
*/
$content = "<a href='./redirect.php'><img src='./images/Sign-in-with-Twitter-darker.png'></a>";

/*
|
| Display the HTML interface
|
*/
include dirname( __FILE__ ) . "/html.inc";

/* End of file: connect.php */
/* File location: /var/www/php.tuts.local/week8/connect.php */
