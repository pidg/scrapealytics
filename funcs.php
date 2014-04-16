<?php

/*
	A few functions for playing with data copied/pasted from
	analytics.twitter.com.

	Copy and paste data from analytics.twitter.com into a file.

	The data should look like this:
		Creative Scotland reveals ambitious 'crowdsourced' 10-year plan for the country's cultural sector: bbc.co.uk/news/uk-scotla…4 clicks ^T
		9 Apr 2014, 11:59 AM Pacific time
			1	1	0
		Maria Miller quits as culture secretary - bbc.co.uk/news/uk-politi…10 clicks
		9 Apr 2014, 10:34 AM Pacific time
			0	4	0

	Do this:

	include("funcs.php");
	$tweets = convert_to_array ( file_get_contents("pasted.txt") );

	Your tweets are now in an array with the following properties:

	$tweets[0..n]["tweet"] 			the contents of the tweet
	$tweets[0..n]["date"] 			date of tweet
	$tweets[0..n]["favourites"]  	}
	$tweets[0..n]["replies"]		}	count of interactions
	$tweets[0..n]["retweets"]	}

	You can then do:

	Filters:
	$direct_tweets 	= exclude_replies($tweets);
	$replies_only  	= replies_only($tweets);
	$no_old_skool_rts	= exclude_historic_rts($tweets);	// old-style RT

	Sort tweets into months:
	$months_array = report_by_month($tweets);

*/

function convert_to_array($pasted)
{
	$f = explode("\n", $pasted);

	foreach ( $f as $line )
	{
		if ( !stristr($line, "normal reach") && !stristr($line, "Promoted on ") && !stristr($line, "Promoted between ") && !stristr($line, "Limited delivery") )
		{
			$new[] = $line;
		}
	}

	$i=0;
	for ( $n=0; $n < count($new); $n++ )
	{
		$tweets[$i]["tweet"] = trim($new[$n]);
		$n++;
		$tweets[$i]["date"] = trim($new[$n]);

		// fix for '1 hour ago'
		if ( stristr($tweets[$i]["date"], "ago") )
			$tweets[$i]["date"] = date("j M Y") . ", blah";

		$n++;
		$tweets[$i]["counts"] = trim($new[$n]);
		$i++;
	}

	$i=0;
	foreach ( $tweets as $tweet )
	{
		$counts = explode("\t", $tweet["counts"]);
		$d = explode(",", $tweet["date"]);

		$all[$i]["date"] = $d["0"];
		$all[$i]["tweet"] = $tweet["tweet"];
		$all[$i]["favourites"] = $counts[0];
		$all[$i]["retweets"] = $counts[1];
		$all[$i]["replies"] = $counts[2];

		$i++;
	}

	return $all;
}



function exclude_replies($array_of_tweets)
{
	$i=0;
	foreach ( $array_of_tweets as $tweet )
	{
		if ( substr($tweet["tweet"], 0, 1) != "@" )
		{
			$items[$i] = $tweet;
			$i++;
		}

	}

	return $items;
}


function replies_only($array_of_tweets)
{
	$i=0;
	foreach ( $array_of_tweets as $tweet )
	{
		if ( substr($tweet["tweet"], 0, 1) == "@" )
		{
			$items[$i] = $tweet;
			$i++;
		}
	}

	return $items;
}


function exclude_historic_rts($array_of_tweets)
{
	$i=0;
	foreach ( $array_of_tweets as $tweet )
	{
		if ( substr($tweet["tweet"], 0, 3) != "RT " )
		{
			$items[$i] = $tweet;
			$i++;
		}
	}

	return $items;
}

function report_by_month($array_of_tweets)
{

	$i=0;  $month="blah";
	foreach ($array_of_tweets as $tweet)
	{
		$e = explode ( " ", $tweet["date"] );
		$newmonth = $e[1] . "-" . $e[2];

		if ( $newmonth != $month )
		{
			$i=0;
			$month = $newmonth;
		} else {
			$i++;
		}

		$months[$month][$i]["tweet"] = $tweet["tweet"];
		$months[$month][$i]["favourites"] = $tweet["favourites"];
		$months[$month][$i]["retweets"] = $tweet["retweets"];
		$months[$month][$i]["replies"] = $tweet["replies"];
		$months[$month][$i]["day"] = $e[0];
	}

	return $months;

}

