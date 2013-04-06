<?php

class Admin_Controller extends Base_Controller
{

  public $restful = true;

	public function get_admin_index()
	{
    $query = Schedule::order_by('created_at', 'desc')->get();

		return View::make('admin.admin_index', array('schedules' => $query));
  }

  public function post_admin_index()
  {

    $schedule_id = Input::get('semester-select');

    if($schedule_id == "new")
    {
      return Redirect::to_action('admin@add_semester');
    }
    else if ($schedule_id == "default")
    {
      return Redirect::to_action('admin@admin_index');
    }
    else
    {
      return Redirect::to_action('admin@view_semester')->with('schedule_id', $schedule_id);
    }

  }

  public function get_view_faculty()
  {
    return View::make('admin.view_faculty');
  }



  public function get_semester_cp()
  {
    return View::make('admin.semester_cp');
  }


  public function get_add_semester()
  {
    return View::make('admin.add_semester');
  }

  public function post_add_semester()
  {

    $name = Input::get('select-season');
    $year = Input::get('select-year');

    $query = Schedule::where_name_and_year($name, $year)->first();

    if($query == NULL){

      $schedule = Schedule::create(array('name' => $name,
                                         'year' => $year ));

      return Redirect::to_action('admin@view_semester')->with('schedule_id', $schedule->id);
    }
    else
    {
      return View::make('admin.add_semester')->with('message', 'Schedule already exists!');
    }

    
  }

  public function get_view_semester()
  {

    if (Session::get('schedule_id')){
      $schedule = Schedule::find(Session::get('schedule_id'));

      $semester = $schedule->name . " " . $schedule->year;
    } 
    else{
      $semester = "No semester";
    }

    return View::make('admin.view_semester')->with('semester', $semester);
  }


  public function post_file_upload()
  {
      $file = Input::file('fileToUpload');
      $inputType = Input::get('input-type');


      if ( $file['type'] == "text/plain" )
      {
        if ($file['error'] > 0)
        {
          echo "<div id='file-contents'>error</div>";
        }
        else
        {
          $contents = File::get( $file['tmp_name']  );

          echo "<div id='file-contents'>" . $contents . "</div>";
          echo "<div id='input-type'>" . $inputType . "</div>";
        } 
      }
      else
      {
        echo "<div id='file-contents'>invalid_file</div>";
      }  

  }


}
