<?php

//>>>>>>>not sure how to pass in admin user variable
$username = $user; //this username has to be passed from the login page

$old_pw = $_POST['old-pw'];
$new_pw = $_POST['new-pw'];
$repeat_new_pw = $_POST['rpt-new-pw'];

if($username->password != $old_pw) //check if current password match
    $message["error"] = "Password Invalid!</br>"; 
else if($new_pw != $repeat_new_pw) //check is new and repeat password match
	$message["error"] = "Password Mismatch!</br>"
else
{
	if(count($new_pw) < 6)//check for pw less than 6 char
		$message["error"] = "Password must be atleast 6 character long!</br>";
	else if (count($new_pw) > 10)//check for pw longer than 10 char
		$message["error"] = "Password cannot be longer than 10 character!</br>";
	else if(!ctype_alpha(substr($new_pw, 0)))//check if first char is not alpha
		$message["error"] = "Password must begin with alphabet!</br>";
	else
	{
		if(!preg_match('/[^0-9A-Za-z!.,?]/', $new_pw))//check if all the char are valid char
			$message["error"] = "Password can only contain . , ! ? 0-9 a-z A-Z'</br>";	
		else if(!preg_match('/.,?!', $new_pw))//check is pw do not contain at least one valid symbols
			$message["error"] = "Password must contain atleast one , or . or ! or ?"
		else
		{
			$username->password = $new_pw;  //set new pw
			$username->save();				//save new pw
			echo"Password changed!";
				
			if($username->user_type == 2)  //redirect to admin
				return Redirect::to('admin/admin_index');
			else if($username->user_type == 3)//redirect to faculty
				return Redirect::to('faculty/faculty_index');
		}
	}
}

?>