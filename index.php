<pre>
<?php

/*
	Scrapealytics! by @tarasyoung

	Quick demo:

	Copy and paste your tweets from analytics.twitter.com into a new text
	file.  The file should look like this:

		Channel 4 in £3m partnership with Arts Council England: theguardian.com/media/2014/apr…9 clicks
		3 hours ago
			0	3	0
		@Hazel_EH_ Welcome to the AMA! Let us know if you need anything. ^T
		14 Apr 2014, 10:15 AM Pacific time
			0	0	1
		The Pricing Toolbox - not worth missing. Coming to London (15 April), Glasgow (23 April) & Notts (7 May): a-m-a.co.uk/pricingtoolbox/8 clicks
		11 Apr 2014, 1:11 PM Pacific time
			1	2	0

	(repeat for every tweet you want to include.)

	Save it as my_pasted_analytics.txt in the same folder as this, then visit index.php in your browser.

*/

include("funcs.php");	/* original functions */

$tweets = convert_to_array( file_get_contents("my_pasted_analytics.txt") );
$tweets = exclude_replies($tweets);
$tweets = exclude_historic_rts($tweets);

print_r( report_by_month($tweets) );


// include("extras.php");	/* some extra functions for drawing graphs/making tables */
// month_report($tweets, array("tweets","interactions","interactions per tweet"), 1000, "Apr-2013", "Mar-2014");


