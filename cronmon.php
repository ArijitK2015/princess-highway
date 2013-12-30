<?php
error_reporting(E_ALL);

//================================================================
/*
Magento Cron script. GNU/GPL
oliver.higgins@gmail.com
*/

//insert your database info here
if (preg_match("/www\.princesshighway\.com\.au/", $_SERVER['SERVER_NAME'])) {
    $server = '116.118.248.137';
    $user   = 'go_mage_user';
    $pass   = 'tQtYgrzzk';
    $db     = 'gormansh_mage_1-7-0-2';
}
else {
    // your dev config here
    $server = 'localhost';
    $user   = 'shopprinc_user';
    $pass   = 'shopprinc_password';
    $db     = 'shopprinc_1_8_1_0';
}

//end data input
//================================================================

$tz = date_default_timezone_get();
$dtz = new DateTimeZone($tz); 
$now = new DateTime('now', $dtz);
$os = $dtz->getOffset($now);

echo sprintf("<h1>Magento Cron Schedule</h1><h2>%s: for %s@%s</h2>", $_SERVER['SERVER_NAME'], $user, $server);
echo sprintf("%s [%s]: %s", $tz, $os, date('l jS \of F Y h:i:s A', $now->format('U')));

//================================================================
//pending jobs

mysql_connect($server,$user,$pass);
@mysql_select_db($db) or die("Unable to select database");

//$query="SELECT * FROM cron_schedule" ;
$query='SELECT * FROM `cron_schedule` WHERE `status` ="pending" ORDER BY `scheduled_at` DESC' ;

$result = mysql_query($query);
$num = mysql_numrows($result);

echo sprintf("<h2>Jobs Pending [%d]</h2>", $num);
echo '<table border="1" width="980"><tbody>';
echo "<tr><th>schedule_id</th><th>job_code</th><th>status</th><th>created_at</th><th>scheduled_at</th>";
//echo "<th>executed_at</th><th>finished_at</th></tr>";
$format = "Y-m-d H:i:s";
$i=0;
while ($i < $num) {
    $schedule_id = mysql_result($result,$i,"schedule_id");                                      
    $job_code = mysql_result($result,$i,"job_code");
    $status = mysql_result($result,$i,"status");

    $created_at = mysql_result($result,$i,"created_at");
    $scheduled_at = mysql_result($result,$i,"scheduled_at");
    $executed_at = mysql_result($result,$i,"executed_at");
    $finished_at = mysql_result($result,$i,"finished_at");

    // convert dates
    $created_at = date($format, strtotime($created_at) + $os);
    $scheduled_at = date($format, strtotime($scheduled_at) + $os);

    //output html
    echo "<tr>";
    echo "<td>".$schedule_id."</td>";
    echo '<td>'.$job_code."</td>"; 
    echo '<td style="color: red;">'.$status."</td>"; 

    echo sprintf("<td>%s</td>", $created_at);
    echo sprintf("<td>%s</td>", $scheduled_at); 
    //echo "<td>".$executed_at."</td>"; 
    //echo "<td>".$finished_at."</td>"; 
    echo "</tr>";                 
    $i++;
}
echo "</tbody></table><hr>";
//================================================================
//Succsessful jobs

mysql_connect($server,$user,$pass);
@mysql_select_db($db) or die("Unable to select database");

//$query="SELECT * FROM cron_schedule" ;
$query = 'SELECT * FROM `cron_schedule` WHERE `status` ="success" ORDER BY `executed_at` DESC' ;
$result = mysql_query($query);
$num = mysql_numrows($result);

echo sprintf("<h2>Jobs Succsessful [%d]</h2>", $num);
echo '<table border="1" width="980"><tbody>';
echo "<tr><th>schedule_id</th><th>job_code</th><th>status</th><th>created_at</th><th>scheduled_at</th>";
echo "<th>executed_at</th><th>finished_at</th></tr>";

$i=0;
while ($i < $num) {

    $schedule_id =mysql_result($result,$i,"schedule_id");                                      
    $job_code = mysql_result($result,$i,"job_code");
    $status = mysql_result ($result,$i,"status");

    $created_at = mysql_result ($result,$i,"created_at");
    $scheduled_at = mysql_result ($result,$i,"scheduled_at");
    $executed_at = mysql_result ($result,$i,"executed_at");
    $finished_at = mysql_result ($result,$i,"finished_at");

    // convert dates
    $created_at = date($format, strtotime($created_at) + $os);
    $scheduled_at = date($format, strtotime($scheduled_at) + $os);
    $executed_at = date($format, strtotime($executed_at) + $os);
    $finished_at = date($format, strtotime($finished_at) + $os);

    //output html
    echo "<tr>";
    echo "<td>".$schedule_id."</td>";
    echo "<td>".$job_code."</td>"; 
    echo "<td>".$status."</td>"; 

    echo sprintf("<td>%s</td>", $created_at);
    echo sprintf("<td>%s</td>", $scheduled_at); 
    echo sprintf("<td>%s</td>", $executed_at); 
    echo sprintf("<td>%s</td>", $finished_at); 
    echo "</tr>";                 
    $i++;
}
echo "</tbody></table>";
//================================================================
?>