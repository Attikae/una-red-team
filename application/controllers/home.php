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

    public function get_changepw()
    {
      return View::make('home.changepw');
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
                        $message["error"] = "You have 2 more login attempt left!";
                    }
                    else if($fail_counter == 2)
                    {
                        $message["error"] = "You have 1 more login attemp left";
                    }
                    else if ($fail_counter == 3)
                    {
						if($user->user_type == 3)//if user is admin
						{
							//call for random_pw.php
							$message["error"] = "Admin account locked!</br> New password sent to your email! ";
							return Redirect::to('random_pw.php');
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

}
