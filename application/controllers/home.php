<?php

class Home_Controller extends Base_Controller {

    /*
    |--------------------------------------------------------------------------
    | The Default Controller
    |--------------------------------------------------------------------------
    |
    | Instead of using RESTful routes and anonymous functions, you might wish
    | to use controllers to organize your application API. You'll love them.
    |
    | This controller responds to URIs beginning with "home", and it also
    | serves as the default controller for the application, meaning it
    | handles requests to the root of the application.
    |
    | You can respond to GET requests to "/home/profile" like so:
    |
    |       public function action_profile()
    |       {
    |           return "This is your profile!";
    |       }
    |
    | Any extra segments are passed to the method as parameters:
    |
    |       public function action_profile($id)
    |       {
    |           return "This is the profile for user {$id}.";
    |       }
    |
    */

    public $restful = true;

    public function get_index()
    {
        return View::make('home.index');
    }

    public function get_login()
    {
        return View::make('home.login');
    }

    public function post_login()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $message = array("error" => "", "username" => $username);
        

        if(($username) && !($password)) //username without password
            $message["error"] = 'Password Required!';
        elseif (!($username) && ($password)) //password without username
            $message["error"] = 'Username Required!';
        elseif(!($username) && !($password)) //no username or password 
            $message["error"] = 'Username and Password required!';
        else
        {       
            $user = User::where_email($username)->first();
            
            if($user) //username found
            {
                if( $user->is_locked == 1)
                {
                    $message["error"] = 'User locked!</br>Maximum login attempts exceeded</br>'
                        . 'Email department administrator to reset your account.';
                }
                else if( $user->password == $password )
                {
                    Session::put('user_id', $user->id);

                    //check faculty or admin
                    if(($user->user_type) == 2 ) // 2 for user_type in database represents faculty
                    {
                        //check bool val for first time login
                        if(($user->login_bool) == '0')
                        {
                            $user->login_bool = 1;
                        
                            //call for initial_login.php page
                            return Redirect::to('home/changepw'); //need this page setup
                        }
                        
                        return Redirect::to('faculty/faculty_index'); //need this page setup
                        
                    }
                    else if ( ($user->user_type == 1) || ($user->user_type == 3) )
                        return Redirect::to('admin/admin_index');
                }
                else 
                {
                    $fail_counter = Session::get("fail_counter");
                    $message["error"] = "Invalid Password!</br>"; 
					
					$fail_counter = $fail_counter + 1;

                    if( $fail_counter == 1)
                    {
                        $message["error"] = "Incorrect Password! You have 2 more login attempt left!";
                    }
                    else if($fail_counter == 2)
                    {
                        $message["error"] = "Incorrect Password! You have 1 more login attemp left";
                    }
                    else if ($fail_counter == 3)
                    {
						if( ($user->user_type == 1) || ($user->user_type == 3) )//if user is admin
						{
							//call for random_pw.php
							$user->is_locked = 1;
                            $user->save();
							$message["error"] = "Admin account locked!</br> New password sent to your email! ";
							//return Redirect::to('random_pw.php');
                            User::random_pw($user->id);
						}
						else
						{
							$user->is_locked = 1;
							$user->save();
						}
                    }

                    Session::put("fail_counter", $fail_counter);

                }
            }
            else
                $message["error"] = 'Username Not Found!';    
                
        }
        return View::make('home.login')->with("message", $message);
    }

    public function get_changepw()
    {
      return View::make('home.changepw');
    }

    public function post_changepw()
    {
        
        $user = User::find(Session::get('user_id')); //this username has to be passed from the login page

        $old_pw = $_POST['old-pw'];
        $new_pw = $_POST['new-pw'];
        $repeat_new_pw = $_POST['rpt-new-pw'];
        

        if($user->password != $old_pw) //check if current password match
            $message["error"] = "Password Invalid!</br>";
         
        else if($new_pw != $repeat_new_pw) //check is new and repeat password match
            $message["error"] = "Password Mismatch!</br>";
        
        else if($old_pw == $new_pw)
            $message["error"] = "New password must be different than current password";
        
        else
        {
            if(strlen($new_pw) < 6)//check for pw less than 6 char
                $message["error"] = "Password must be atleast 6 character long!</br>";
                
            else if (count($new_pw) > 10)//check for pw longer than 10 char
                $message["error"] = "Password cannot be longer than 10 character!</br>";
            
            else if(!ctype_alpha($new_pw[0]))//check if first char is not alpha
                $message["error"] = "Password must begin with alphabet!</br>";
            
            else
            {
                for($count = 0; $count < strlen($new_pw); $count++ )
                {
                    if(ctype_alpha($new_pw[$count]) || (is_numeric($new_pw[$count])) || ($new_pw[$count] == '.') || ($new_pw[$count] == '!') || ($new_pw[$count] == '/') || ($new_pw[$count] == ','))
                        $boolValid = 1; //store 1 if valid characters for pw 
                    else 
	                    $boolValid = 0;//stores 0 if invalid characters for pw
           }
                
                for($loop = 0; $loop < strlen($new_pw); $loop++)
                {
                    if(!(ctype_alpha($new_pw[$loop])) && (!(is_numeric($new_pw[$loop]))) && ($new_pw[$loop] != '.') && ($new_pw[$loop] != '!') && ($new_pw[$loop] != '/') && ($new_pw[$loop] != ','))
                        $symbolBool = 0; //stores 0 if no special valid char is found
                    else 
	                    $symbolBool = 1; //stores 1 if valid char is found

                }
                        
                if($boolValid == 0)
                    $message["error"] = "Password can only contain 0-9 A-Z a-b . , ! ?";
                else if($symbolBool == 0)
                    $message["error"] = "Password must contain atleast one . or , or ! or ?";
                else
                {
                    $user->password = $new_pw;  //set new pw
                    $user->login_bool = 1;
                    $user->save();              //save new pw
                    $message["error"] = "Password changed! Login with new password.";
                        
                    if($user->user_type == 2)  //redirect to admin
                        return Redirect::to('faculty/faculty_index');
                    else if($user->user_type == 1 || $user->user_type == 3)//redirect to faculty
                        return Redirect::to('admin/admin_index');

                    return View::make('home.login')->with("message", $message);
                }
            }
        }

        return View::make('home.changepw')->with("message", $message);
    }



} // End of Class
