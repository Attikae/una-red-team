<?php
//for automated faculty password change

//>>>>>>>>not sure how to pass in admin user variable
$username = $user;

//save random password
$randomPassword = generatePassword;

//automated email send with subject 'New Login Password'
mail($username->email, 'New Login Password', $randomPassword);
 
   /****************************************************************************
  /* @function    generatePassowrd
  /* @author      Ash Karki
  /* @description This segment of code will scan an incoming file(the format of
  /*              which can be found in section A.3 of the specification
  /*              document) to ensure the correct format is found.
  /* @input       N/A
  /* @output      This function will pass back the random password of length 9
  /*              and store it in the variable $randomPassword                    	 
  ****************************************************************************/
 function generatePassword($length=9, $strength=0) {
	$vowels = 'aeuy';
	$consonants = 'bdghjmnpqrstvz';
	if ($strength & 1) {
		$consonants .= 'BDGHJLMNPQRSTVWXZ';
	}
	if ($strength & 2) {
		$vowels .= "AEUY";
	}
	if ($strength & 4) {
		$consonants .= '23456789';
	}
	if ($strength & 8) {
		$consonants .= ',?!_';
	}
 
	$password = '';
	$alt = time() % 2;
	for ($i = 0; $i < $length; $i++) {
		if ($alt == 1) {
			$password .= $consonants[(rand() % strlen($consonants))];
			$alt = 0;
		} else {
			$password .= $vowels[(rand() % strlen($vowels))];
			$alt = 1;
		}
	}
	return $password;
}
 
?>