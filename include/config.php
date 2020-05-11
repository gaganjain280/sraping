<?php
$hostname="localhost"; //local server name default localhost
$username="root";  //mysql username default is root.
$password="";       //blank if no password is set for mysql.
$database="scrap";  //database name which you created
$conn=mysql_connect($hostname,$username,$password);
if(! $conn)
{
die('Connection Failed'.mysql_error());
}

mysql_select_db($database,$conn);
?>