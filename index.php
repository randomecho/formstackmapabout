<?php
/**
 * Displays forms under account, shows map if anything is ready to plot
 *
 * @package    Formstack Mapabout
 * @author     Soon Van - randomecho.com
 * @copyright  2014 Soon Van
 * @license    http://opensource.org/licenses/BSD-3-Clause
 */

require_once 'config.php';
require_once 'formstack.php';

if (trim($formstack_token) == '')
{
	echo 'Formstack API token is missing. Edit the configuration before continuing.<br>';
	exit();
}

if (trim($starting_address) == '')
{
	echo 'Missing a starting address. Edit the configuration before continuing.';
	exit();
}

$formstack = new Formstack();

?>
<!DOCTYPE html><html><head><meta charset="utf-8">
<title>Formstack Mapabout</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Formstack addresses on a map">
<meta name="author" content="Soon Van - randomecho.com">
<style>
body, h1, h2, h3, p, ul, ol {
  margin: 0;
  padding: 0;
}

body {
  background: #fff;
  color: #000;
  font-size: 0.9em;
  font-family: sans-serif;
}

section {
  clear: both;
  margin: 1em auto;
  overflow: hidden;
  padding: 0;
  width: 95%;
}

a {
  text-decoration: none;
}

h1, h2, h3, p {
  margin: 0 0 1em 0;
}

h1 {
  font-size: 1.5em;
}

h2 {
  font-size: 1.3em;
}

ul {
  margin: 0 0 1em 2em;
}

li {
  margin: 0 0 0.5em 0;
}

table {
  border-collapse: collapse;
  border-spacing: 0;
  width: 100%;
}

th {
  text-align: left;
}

th, td {
  padding: 0.5em 1em 0.5em 0.3em;
}

tr:nth-child(2n) {
  background: #f0f0f0;
}

.infobox {
  border: 1px solid #555;
  border-radius: 5px;
  margin: 1em auto;
  padding: 1em;
  width: 80%;
}

.address-list {
  float: left;
  margin: 0 1em;
  padding: 0;
  width: 20%;
}

#map-canvas {
  float: left;
  margin: 0;
  padding: 0;
  width: 75%;
  height: 450px;
}

.ghost {
  display: none;
}

.current {
  font-weight: bold;
}
</style>
</head><body>
<?php

if (isset($_GET['id']))
{
	$form_name = $formstack->get_form($_GET['id']);
	$locations = $formstack->get_addresses($_GET['id']);

	if (isset($form_name->name))
	{
?>
<section>
<h2><?php echo $form_name->name ?></h2>
<?php
		if ($locations !== false)
		{
			echo '<div class="address-list">';
			echo '<ul>';

			foreach ($locations as $location)
			{
				echo '<li>'.$location['fullname'].'<br>'.$location['address'].'</li>';
				$waypoints[] = $location['address'];
			}

			echo '</ul>';
			echo '<p>Above address order listing does not currently match up with route markers on right.</p>';
			echo '</div>';
			$waypoints = json_encode($waypoints);

			echo '<div id="map-canvas"></div>';
			echo '<script src="//code.jquery.com/jquery-1.10.2.min.js"></script>';
			echo '<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&amp;sensor=false"></script>';
			echo '<script src="mapabout.js"></script>';
			echo '<script>';
			echo 'google.maps.event.addDomListener(window, "load", initialize("'.$starting_address.'"));';
			echo 'calcRoute("'.$starting_address.'", '.$waypoints.');';
			echo '</script>';
		}
		else
		{
			echo '<p>No addresses were captured with this form.</p>';
		}
?>
</section>
<?php
	}
}

?>

<div class="infobox">
<?php

$forms = $formstack->get_form();

if (isset($forms->forms))
{
	echo '<table>';
	echo '<tr>';
	echo '<th>Name</th>';
	echo '<th>Submissions</th>';
	echo '<th>Last submitted</th>';
	echo '<th>&nbsp;</th>';
	echo '</tr>';

	foreach ($forms->forms as $info)
	{
		$css_current = (isset($_GET['id']) && $info->id == $_GET['id']) ? 'class="current"' : '';

		echo '<tr '.$css_current.'>';
		echo '<td><a href="./index.php?id='.$info->id.'">'.$info->name.'</a></td>';
		echo '<td>'.$info->submissions.'</td>';
		echo '<td>'.$info->last_submission_time.'</td>';
		echo '<td><a href="'.$info->url.'">View form</a></td>';
		echo '</tr>';
	}

	echo '</table>';
}

?>
</div>

</body></html>