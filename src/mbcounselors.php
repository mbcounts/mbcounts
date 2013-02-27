<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Spencer
 * Date: 1/29/13
 * Time: 7:34 PM
 * To change this template use File | Settings | File Templates.
 *
 * Main page:
 *
 * Purpose: to help people find a merit badge counselor
 *
 * ability to filter
 * - location: how close the counselor is to the searcher
 *      - implies the need to enter address of searcher
 *      - so is this the first thing entered?
 *
 */

$con = mysql_connect("localhost","root","");
if (!$con)
{
    die('Could not connect: ' . mysql_error());
}
else{
    echo('connected');
}

mysql_select_db("mbcounts", $con);

$result = mysql_query("SELECT * FROM meritbadge");

while($row = mysql_fetch_array($result))
{
    echo $row['id'] . " " . $row['name'];
    echo "<br />";
}


echo mysql_close($con);


