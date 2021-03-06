<?php

/*
|-------------------------------------------------------------------
| TWITTER OAUTH LIBRARY
|-------------------------------------------------------------------
|
| This is the Twitter OAuth Library developed by John Clarke to be used
| for authentication to Twitter via its OAuth REST API.
| Credits : 
| * Abraham Williams (abraham@abrah.am http://abrah.am)
|   Initial development of PHP OAuth implementation for Twitter
|
*/

/*
|
| Load the OAuth Library (can be found at http://oauth.net/)
|
*/
require_once dirname( __FILE__ ) . "/OAuth.php";

class TwitterOAuth
{
	/*
	|
	| Public class variables
	|
	*/
	public $http_code; 														// Contains the last HTTP status code returned
	public $url; 																	// Contains the last API call
	public $host = "https://api.twitter.com/1/";	// Setup the API root URL
	public $timeout = 30;													// Set default timeout
	public $connect_timeout = 30;									// Set connect timeout
	public $ssl_verify_peer = FALSE;							// Verify SSL Certificate
	public $format = "json";											// Set the response format
	public $decode_json = TRUE;										// Decode returned JSON data
	public $http_info;														// Contains the last HTTP headers returned
	public $user_agent = "TwitterOAuth v0.3.0";		// Set the UserAgent
	
	/*
	|
	| Setup API URLs
	|
	*/
	function accessTokenURL()		{ return "https://api.twitter.com/oauth/access_token"; }
	function authenticateURL()	{ return "https://twitter.com/oauth/authenticate"; }
	function authorizeURL()			{ return "https://twitter.com/oatuh/authorize"; }
	function requestTokenURL()	{ return "https://api.twitter.com/oauth/request_token"; }
	
	/*
	|
	| Debugging Helpers
	|
	*/
	function lastStatusCode()		{ return $this -> http_code; }
	function lastAPICall()			{ return $this -> url; }
	
	/*
	|
	| Overridden constructor for the class
	| Used to construct the TwitterOAuth object
	|
	*/
	function __construct( $consumer_key, $consumer_secret, $oauth_token = NULL, $oauth_token_secret = NULL )
	{
		$this -> sha1_method = new OAuthSignatureMethod_HMAC_SHA1();
		$this -> consumer = new OAuthConsumer( $consumer_key, $consumer_secret );
		if( ! empty( $oauth_token ) && ! empty( $oauth_token_secret ) ) :
			$this -> token = new OAuthConsumer( $oauth_token, $oauth_token_secret );
		else :
			$this -> token = NULL;
		endif;
	}
	
	/*
	|
	| Get a request_token from Twitter
	| @return a key/value pair array containing oauth_token and oath_token_secret
	|
	*/
	function getRequestToken( $oauth_callback = NULL )
	{
		$parameters = array();
		if( ! empty( $oauth_callback ) ) :
			$parameters["oauth_callback"] = $oauth_callback;
		endif;
		$request = $this -> oAuthRequest( $this -> requestTokenURL(), 'GET', $parameters );
		$token = OAuthUtil::parse_parameters( $request );
		$this -> token = new OAuthConsumer( $token['oauth_token'], $token['oauth_token_secret'] );
		return $token;
	}
	
	/*
	|
	| Get the authorize URL
	| @return string
	|
	*/
	function getAuthorizeURL( $token, $sign_in_with_twitter = TRUE )
	{
		if( is_array( $token ) ) : // Read more : http://www.php.net/is_array
			$token = $token['oauth_token'];
		endif;
		if( empty( $sign_in_with_twitter ) ) :
			return $this -> authorizeURL() . "?oauth_token={$token}";
		else :
			return $this -> authenticateURL() . "?oauth_token={$token}";
		endif;
	}
	
	/*
	|
	| Exchange request token and secret for an access token and secret to sign API calls
	| @return array { "oauth_token" => "the-access-token", "oauth_token_secret" => "the-access-secret", "user_id" => "9999999", "screen_name" => "the-user-name" }
	|
	*/
	function getAccessToken( $oauth_verifier = FALSE )
	{
		$parameters = array();
		if( ! empty( $oauth_verifier ) ) :
			$parameters["oauth_verifier"] = $oauth_verifier;
		endif;
		$request = $this -> oAuthRequest( $this -> accessTokenURL(), 'GET', $parameters );
		$token = OAuthUtil::parse_parameters( $request );
		$this -> token = new OAuthConsumer( $token['oauth_token'], $token['oauth_token_secret'] );
		return $token;
	}
	
	/*
	|
	| One-time exchange of username and password for access token and secret
	| @return array { "oauth_token" => "the-access-token", "oauth_token_secret" => "the-access-secret", "user_id" => "9999999", "screen_name" => "the-user-name", "x_auth_expires" => "0" }
	|
	*/
	function getXAuthToken( $username, $password )
	{
		$parameters = array();
		$parameters["x_auth_username"] = $username;
		$parameters["x_auth_password"] = $password;
		$parameters["x_auth_mode"] = 'client_auth';
		$request = $this -> oAuthRequest( $this -> accessTokenURL(), 'POST', $parameters );
		$token = OAuthUtil::parse_parameters( $request );
		$this -> token = new OAuthConsumer( $token['oauth_token'], $token['oauth_token_secret'] );
		return $token;
	}
	
	/*
	|
	| GET wrapper for oAuthRequest
	|
	*/
	function get( $url, $parameters = array() )
	{
		$response = $this -> oAuthRequest( $url, 'GET', $parameters );
		if( $this -> format === 'json' && $this -> decode_json ) :
			return json_decode( $response );
		endif;
		return $response;
	}
	
	/*
	|
	| POST wrapper for oAuthRequest
	|
	*/
	function post( $url, $parameters = array() )
	{
		$response = $this -> oAuthRequest( $url, 'POST', $parameters );
		if( $this -> format === 'json' && $this -> decode_json ) :
			return json_decode( $response );
		endif;
		return $response;
	}
	
	/*
	|
	| DELETE wrapper for oAuthRequest
	|
	*/
	function delete( $url, $parameters = array() )
	{
		$response = $this -> oAuthRequest( $url, 'DELETE', $parameters );
		if( $this -> format === 'json' && $this -> decode_json ) :
			return json_decode( $response );
		endif;
		return $response;
	}
	
	/*
	|
	| Format and sign an OAuth / API Request
	|
	*/
	function oAuthRequest( $url, $method, $parameters = array() )
	{
		if( strrpos( $url, 'https://' ) !== 0 && strrpos( $url, 'http://' ) !== 0 ) :
			$url = "{$this->host}{$url}.{$this->format}";
		endif;
		$request = OAuthRequest::from_consumer_and_token( $this -> consumer, $this -> token, $method, $url, $parameters );
		$request -> sign_request( $this -> sha1_method, $this -> consumer, $this -> token );
		switch( $method ) :
			case 'GET'	:	return $this -> http( $request -> to_url(), 'GET' );
										break;
			default			:	return $this -> http( $request -> get_normalized_http_url(), $method, $request -> to_postdata() );
										break;
		endswitch;
	}
	
	/*
	|
	| Make a HTTP request
	| @return API Results
	|
	*/
	function http( $url, $method, $postfields = NULL )
	{
		$this -> http_info = array();
		$ci = curl_init();	// Read more : http://www.php.net/curl
		/*
		|
		| cURL settings
		|
		*/
		curl_setopt( $ci, CURLOPT_USERAGENT, 					$this -> user_agent 			);
		curl_setopt( $ci, CURLOPT_CONNECTTIMEOUT,			$this -> connect_timeout 	);
		curl_setopt( $ci, CURLOPT_TIMEOUT,						$this -> timeout					);
		curl_setopt( $ci, CURLOPT_RETURNTRANSFER,			TRUE											);
		curl_setopt( $ci, CURLOPT_HTTPHEADER,					array("Expect:")					);
		curl_setopt( $ci, CURLOPT_SSL_VERIFYPEER,			$this -> ssl_verify_peer	);
		// curl_setopt( $ci, CURLOPT_HTTPHEADERFUNCTION,	array($this, 'getHeader')	);
		curl_setopt( $ci, CURLOPT_HEADER,							FALSE											);
		
		switch( $method ) :
			case 'POST'		:	curl_setopt( $ci, CURLOPT_POST, TRUE );
											if( ! empty( $postfields ) ) :
												curl_setopt( $ci, CURLOPT_POSTFIELDS, $postfields );
											endif;
											break;
			case 'DELETE'	:	curl_setopt( $ci, CURLOPT_CUSTOMREQUEST, 'DELETE' );
											if( ! empty( $postfields ) ) :
												$url = "{$url}?{$postfields}";
											endif;
											break;
		endswitch;
		
		curl_setopt( $ci, CURLOPT_URL,								$url											);
		$response = curl_exec( $ci );
		$this -> http_code = curl_getinfo( $ci, CURLINFO_HTTP_CODE );
		$this -> http_info = array_merge( $this -> http_info, curl_getinfo( $ci ) );
		$this -> url = $url;
		curl_close( $ci );
		return $response;
	}
	
	/*
	|
	| Get the header info to store
	|
	*/
	function getHeader( $ch, $header )
	{
		$i = strpos( $header, ":" );
		if( ! empty( $i ) ) :
			$key = str_replace( '-', '_', strtolower( substr( $header, 0, $i ) ) );
			$value = trim( substr( $header, $i + 2 ) );
			$this -> http_header[$key] = $value;
		endif;
		return strlen( $header );
	}
}

/* End of file: twitter.oauth.php */
/* File location: /var/www/php.tuts.local/week8/twitter_lib/twitter.oauth.php */
