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


  private static function generatePassword($length=9, $strength=0) {
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
      $consonants .= '@#$%';
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
    
}

