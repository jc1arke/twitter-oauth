<?php

/*
|-------------------------------------------------------------------
| CLEAR TWITTER SESSION DATA
|-------------------------------------------------------------------
|
| File used to clear all sessions and redirect to the update page
|
*/

/*
|
| Load and clear sessions
|
*/
session_start();
session_destroy();

/*
|
| Redirect to the update page
|
*/
header( 'Location: ./connect.php' );

/* End of file: clearsessions.php */
/* File location: /var/www/php.tuts.local/week8/clearsessions.php */
