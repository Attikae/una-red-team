<?php

class User extends Eloquent
{
    
	public static $timestamps = true;


	public static function random_pw($user_id)
	{

		$user = User::find($user_id);

		//save random password
		$randomPassword = User::generatePassword();
		$user->password = $randomPassword;

		$headers = "From: red-team-support@gmail.com" . "\r\n";

		//automated email send with subject 'New Login Password'
		mail($user->email, 'New Login Password', $randomPassword, $headers);

	}

	private static function generatePassword($length = 20)
	{
		$chars =  'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.
            '0123456789``.,!?';

		$str = '';
		$max = strlen($chars) - 1;

		for ($i=0; $i < $length; $i++)
			$str .= $chars[rand(0, $max)];

		echo $str;	
		return $str;
	}
    
}

