// Set to true if you wish to run a continous update stream from the server
var run_status_feed = false;

jQuery(document).ready( function() {
	get_statuses( 5 );
});

// Get the latest statuses
var get_statuses = function( limit ) {
	jQuery.getJSON( "twitter.updater.php", {"ajax" : true, "limit" : limit, "method" : "show"}, function( data ) {
		if( data.error == "" ) {
			// Status data was returned
			var statuses = data.statuses;
			jQuery( "#twitter_stream" ).html("");
			jQuery.each( statuses, function( index, msg ) {
				var txt = msg.text
					// Change the text links to active links with a target of new window
					.replace(/(https?:\/\/[-a-z0-9._~:\/?#@!$&\'()*+,;=%]+)/ig, '<a href="$1" target="_blank"  title="$1">$1</a>')
					// Change references to other Tweeters to links
					.replace(/@+([_A-Za-z0-9-]+)/ig, '<a href="http://twitter.com/$1" target="_blank" title="$1 on Twitter">@$1</a>')
					// Change hashtags to active links
					.replace(/#+([_A-Za-z0-9-]+)/ig, '<a href="http://search.twitter.com/search?q=$1" target="_blank" title="Search for $1 on Twitter">#$1</a>');
				var li = jQuery( "<li />", { id: msg.id, html: txt } ).appendTo( "#twitter_stream" );
			});
		} else {
			// Oh no, something went wrong
			jQuery( "#error" )
				.stop()
				.html( "Something went wrong :(<br />Error details: " + data.error )
				.fadeIn( 1200 );
		}
	});
	
	if( run_status_feed == true ) {
		setTimeout( "get_statuses(" + limit + ");", 2500 );
	}
}

// Submit your status update
var submit_msg = function() {
	// Get the message that the user is submitting
	var twit_msg = jQuery( "#twitter_msg" ).val();
	
	// Reset the error dialog
	jQuery( "#error" )
		.stop()
		.html("")
		.fadeOut( 200 );
		
	// Check maximum allowed character length (could be extended to display character count as user types ;))
	if( twit_msg.length > 140 ) { 
		jQuery( "#error" )
			.stop()
			.html( "Sorry, but the length of your message exceeds 140 characters" )
			.fadeIn( 1200 );
	}
	
	// For more info, goto http://api.jquery.com/jQuery.getJSON/
	jQuery.getJSON( "twitter.updater.php", {"ajax" : true, "msg" : twit_msg, "method" : "update"}, function( data ) {
		if( data.error == "" ) {
			// Status was successfully set :)
			if( data.feedback.id != "" ) {
				jQuery( "#twitter_msg" ).val("");
				get_statuses( 5 );
			}
		} else {
			// Oh no, something went wrong
			jQuery( "#error" )
				.stop()
				.html( "Something went wrong :(<br />Error details: " + data.error )
				.fadeIn( 1200 );
		}
	});
	
	return false;
}
