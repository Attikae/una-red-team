<?php

user_exists($username)
{
	$connect = mysql_connect("localhost","root","") or die("Connect Failed!");
	mysql_select_db("users") or die(mysql_error("DB doesnot exists"));

	$query = mysql_query("SELECT COUNT('user_id') FROM 'users' WHERE 'username' = '$username'");
	return (mysql_query($query, 0) == 1) ? true : false;
}

?>