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

        if(($username) && !($password))
            echo'Password Required!';
        elseif (!($username) && ($password))
            echo'Username Required!';
        elseif(!($username) && !($password)) 
            echo'Username and Password required!';
        else
        {       
            $user = user::find($username);
            
            if($user)
            {
                if(($user->password) == $password)
                {
                    
                    //check faculty or admin
                    if(($user->user_type) == 'faculty')
                    {
                        //check bool val for first time login
                        if(($user->login_bool) == '0')
                        {
                            $user->login_bool = 1;
                        
                            //call for initial_login.php page
                            return Redirect::to('faculty/initial_login');
                        }
                        
                        return Rediret::to('faculty/faculty_index');//need this page setup
                        
                    }
                    elseif (($user->user_type) == 'admin')
                        return Redirect::to('admin/admin_index');
                }
                else 
                    echo'Invalid Password!';    
                
            }
            else
                echo'Username Not Found!';    
                

        }
        //return Redirect::to('admin/admin_index');
    }

}