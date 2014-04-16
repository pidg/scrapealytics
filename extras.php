<?php

/*
	These are various quick + dirty functions I've written to make sense of tweets.

	Reports:
	month_report($tweets_array, $which_graphs_to_draw, $graph_width, $start_month, $end_month);
	e.g.
		month_report($tweets, array("tweets","interactions","interactions per tweet"), 1000, "Apr-2013", "Mar-2014");


*/

function month_table($array_of_months, $startmonth, $endmonth)
{

// Makes a basic table using the month report data

echo "<table border=1>\n";
echo "<tr><td><strong>Month</strong></td>";
echo "<td><strong>Tweets sent</strong></td>";
echo "<td><strong>Faves</strong></td>";
echo "<td><strong>Replies</strong></td>";
echo "<td><strong>Retweets</strong></td>";
echo "<td><strong>(Total)</strong></td>";
echo "<td><strong>Interactions per tweet</strong></td></tr>\n";

$flag=0;
	foreach ( $array_of_months as $month=>$value )
	{
		$month_tweets=0;
		$month_faves=0;
		$month_replies=0;
		$month_retweets=0;

		if ( $month == $endmonth ) $flag=1;	// because the array is in reverse order
		if ( $month == $startmonth ) $flag=0;

		foreach ( $value as $tweet )
		{
			$month_tweets++;
			$month_faves+=$tweet["favourites"];
			$month_replies+=$tweet["replies"];
			$month_retweets+=$tweet["retweets"];
		}

		if ( $flag )
		{
			echo "<tr>\n";
			$month_interactions = $month_faves + $month_replies + $month_retweets;
			$ints_per_tweet = $month_interactions / $month_tweets;
			echo "<td>$month</td><td>$month_tweets</td><td>$month_faves</td><td>$month_replies</td><td>$month_retweets</td><td>$month_interactions</td><td>$ints_per_tweet</td></tr>\n";
		}

	}
echo "</table>\n";

}

function month_graph($id, $array_of_months, $graphwidth, $charttype, $plotwhat, $startmonth, $endmonth)
{

// Generates javascript for use with Google Charts

/* $plotwhat can be:

	"tweets"
	"favourites"
	"replies"
	"retweets"
	"interactions"
	"interactions per tweet"

	$charttype can be "bar" or "line"

*/

	echo "var data$id = google.visualization.arrayToDataTable([\n";
	echo "['x', '" . ucfirst($plotwhat) . "'],\n";

	$flag=0;
	foreach ( array_reverse($array_of_months) as $month=>$value )
	{
		$month_tweets=0;
		$month_faves=0;
		$month_replies=0;
		$month_retweets=0;

		if ( "$month" == $startmonth ) $flag=1;

			foreach ( $value as $tweet )
			{
				$month_tweets++;
				$month_faves+=$tweet["favourites"];
				$month_replies+=$tweet["replies"];
				$month_retweets+=$tweet["retweets"];
			}

			$month_interactions = $month_faves + $month_replies + $month_retweets;
			$ints_per_tweet = $month_interactions / $month_tweets;

			switch($plotwhat)
			{
			case "tweets": $plot = $month_tweets; break;
			case "favourites": $plot = $month_faves; break;
			case "replies": $plot = $month_replies; break;
			case "retweets": $plot = $month_retweets; break;
			case "interactions": $plot = $month_interactions; break;
			case "interactions per tweet": $plot = $ints_per_tweet; break;
			default: $plot = 1;
			}

	          if ( $flag ) echo "['$month',   $plot],\n";

		if ( "$month" == $endmonth ) $flag = 0;

	}

	echo "     ]);\n";

?>        new google.visualization.<?php echo ucfirst($charttype); ?>Chart(document.getElementById('vis<?php echo $id; ?>')).
          draw(data<?php echo $id; ?>, {curveType: "function",
            width: <?php echo $graphwidth; ?>, height: 400,
            vAxis: {maxValue: 10}}
          );
<?php

}




function month_report($tweets, $graphs, $graphwidth, $startmonth, $endmonth)
{

	/*

		This creates a report, including graphs for each thing you specify in $graphs (an array).

		e.g.
		month_report($tweets, array("tweets","interactions","interactions per tweet"), 1000, "Apr-2013", "Mar-2014");

	*/

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <script type="text/javascript" src="//www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load('visualization', '1', {packages: ['corechart']});
    </script>
    <script type="text/javascript">

      function drawVisualization()
      {

<?php
      $months = report_by_month($tweets);

      for( $n=0; $n < count($graphs); $n++ )
      {
      	month_graph( $n, $months, $graphwidth, "line", $graphs[$n], $startmonth, $endmonth);
      }

?>
      }
      google.setOnLoadCallback(drawVisualization);
    </script>
  </head>

  <body style="font-family: Arial;border: 0 none;">

<?php month_table(report_by_month($tweets), $startmonth, $endmonth) ; ?>

	<?php
		for ($n=0; $n<count($graphs); $n++)
		{
			echo "<div id=\"vis$n\" style=\"width: $graphwidth px; height: 400px;\"></div>\n";
		}
    	?>
  </body>

</html>
<?php

}