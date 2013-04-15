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

    Session::put('schedule_id', $schedule_id);

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
      return Redirect::to_action('admin@view_semester');
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

      Session::put('schedule_id', $schedule->id);

      return Redirect::to_action('admin@view_semester');
    }
    else
    {
      return View::make('admin.add_semester')->with('message', 'Schedule already exists!');
    }

    
  }

  public function get_view_semester()
  {

    if (Session::has('schedule_id')){
      $schedule_id = Session::get('schedule_id');
      $schedule = Schedule::find($schedule_id);
      $semester = $schedule->name . " " . $schedule->year;
      Session::put('semester', $semester);

      $text['available_rooms'] = Available_Room::get_text($schedule_id);
      $text['class_times'] = Class_Time::get_text($schedule_id);
      $text['conflict_times'] = Conflict_Time::get_text($schedule_id);
      $text['courses_to_schedule'] = Course_To_Schedule::get_text($schedule_id);
      $text['faculty_members'] = Faculty_Member::get_text($schedule_id);
      $text['prerequisites'] = Prerequisite::get_text($schedule_id);


    } 
    else{
      $semester = "No semester";
      $text['available_rooms'] = "";
      $text['class_times'] = "";
      $text['conflict_times'] = "";
      $text['courses_to_schedule'] = "";
      $text['faculty_members'] = "";
      $text['prerequisites'] = "";
    }

    return View::make('admin.view_semester')->with('text', $text);
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
          $contents = File::get( $file['tmp_name'] );

          echo "<div id='file-contents'>" . $contents . "</div>";
          echo "<div id='input-type'>" . $inputType . "</div>";
        } 
      }
      else
      {
        echo "<div id='file-contents'>invalid_file</div>";
      }  

  }

  public function post_scan()
  {

    $file_type = Input::get('file_type');
    $file_string = Input::get('file_string');
    $schedule_id = Input::get('schedule_id');

    switch($file_type)
    {
      case "class_times" :
        $result = Class_Time::scan($schedule_id, $file_string);
        break;
      case "available_rooms" :
        $result = Available_Room::scan($schedule_id, $file_string);
        break;
      case "courses_to_schedule" :
        $result = Course_To_Schedule::scan($schedule_id, $file_string);
        break;
      case "conflict_times" :
        $result = Conflict_Time::scan($schedule_id, $file_string);
        break;
      case "prerequisites" :
        $result = Prerequisite::scan($schedule_id, $file_string);
        break;
      case "faculty_members" :
        $result = Faculty_Member::scan($schedule_id, $file_string);
        break;
    }

    echo json_encode($result);

  }
}
