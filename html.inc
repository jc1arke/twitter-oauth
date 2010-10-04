<!doctype html>
<html>
	<head>
		<title>Twitter API Functions via PHP and jQuery</title>
		<script src="http://www.google.com/jsapi?key=INSERT_KEY_HERE" type="text/javascript"></script>
		<script>
			google.load('jquery', '1.4.2'); // Latest version of jQuery
			google.load('jqueryui', '1.8.1'); // Latest version of jQuery UI Extensions
		</script>
		<script src="twitter.updater.js" type="text/javascript"></script>
		<style type="text/css">
		body {
			background-color: #fff;
			margin: 20px;
			font-family: Lucida Grande, Verdana, Sans-serif;
			font-size: 14px;
			color: #4F5155;
		}

		a {
			color: #003399;
			background-color: transparent;
			font-weight: normal;
		}

		h1 {
			color: #444;
			background-color: transparent;
			border-bottom: 1px solid #D0D0D0;
			font-size: 16px;
			font-weight: bold;
			margin: 24px 0 2px 0;
			padding: 5px 0 6px 0;
		}

		code, footer, ul {
			font-family: Monaco, Verdana, Sans-serif;
			font-size: 12px;
			background-color: #f9f9f9;
			border: 1px solid #D0D0D0;
			color: #002166;
			display: block;
			margin: 14px 0 14px 0;
			padding: 12px 10px 12px 10px;
		}
		
		footer {
			margin: 10px 0 10px 0;
			padding: 8px 6px 8px 6px;
		}
		
		footer p {
			font-style: oblique;
		}
		
		#error {
			display: none;
			margin: 10px 0 10px 0;
			padding: 8px 6px 8px 6px;
			background-color: #ffc7c7; 
			border-top: 1px solid #c81216; 
			border-bottom: 1px solid #c81216; 
			color: #c81216;
		}
		
		ul {
			padding-left: 20px;
		}
		</style>
	</head>
	<body>
		<header>
			<h1>Twitter API Functions via PHP and jQuery</h1>
		</header>
		<section id="top_links">
			<p>If you want to start over, click <a href="./clearsessions.php">here</a></p>
		</section>
		<section id="twitter_area">
			<div id="error">
			</div>
			<?php if( isset( $_SESSION['access_token'] ) ) : ?>
			<p>
				<form action="javascript: submit_msg();" method="POST">
					<textarea id="twitter_msg" name="twitter_msg" rows="4" cols="50"></textarea>
					<br />
					<input type="submit" value="Update!" />
				</form>
			</p>
			<p>
				<ul id="twitter_stream">
					<li>Getting new statuses...</li>
				</ul>
			</p>
			<?php endif; ?>
			<p>
				<?php
					if( isset( $menu ) ) :
						echo $menu;
					endif;
				?>
			</p>
			<p>
				<?php
					if( isset( $status_text ) ) :
						echo '<h3>' . $status_text . '</h3>';
					endif;
				?>
			</p>
			<?php if( ! empty( $content ) ) : ?>
			<code>
				<?php
					print_r( $content );
				?>
			</code>
			<?php endif; ?>
		</section>
		<footer>
			<p>"The difference between genius and stupidity is that genius has its limits" - Albert Einstein</p>
		</footer>
	</body>
</html>
