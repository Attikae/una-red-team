<?php


$host = "localhost"; 	//localhost
$host_user = "root"; 	//localhost user
$host_pw = "";       	//localhost password
$db_name = "login";     //database name variable
$table_name = "users";  //table name variable

//make connection to the databse
mysql_connect("$host", "$host_user", "$host_pw") or die("Failed Connection to HOST!!");
mysql_select_db("$db_name") or die("Failed Connection to DB");

//username and password coming from the form
$username = $_POST['username'];
$password = $_POST['password'];

//if username and password exists
if($username && $password)
{
	//select the row where username and password matches
	$sql = "SELECT * FROM $table_name WHERE username = '$username' and password = '$password'";
	$result = mysql_query($sql);

	//there should be one identical match where 
	//the number of rows should be equal to 1
    if(mysql_num_rows($result) == 1)
	{
		//successful login
		//check if faculty or admin
		$sql = "SELECT last_name FROM $table_name WHERE lastname = 'Karki'";
		$result = mysql_query($sql);
		$count = mysql_num_rows($result);
		if($count > 0)
			echo"$user_id";
		
		echo "Youre in!";
		exit();
	}
	else //could not find the username and password match
		echo "Invalid login info!";
	
}
else //if username and password was not entered
{
	echo 'Enter username and password!';
}




/*
	


	*/
	

?>