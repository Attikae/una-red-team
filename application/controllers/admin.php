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

      // Get schedule data
      $schedule_id = Session::get('schedule_id');
      $schedule = Schedule::find($schedule_id);
      $semester = $schedule->name . " " . $schedule->year;
      Session::put('semester', $semester);

      // Get input file data
      $text['available_rooms'] = Available_Room::get_text($schedule_id);
      $text['class_times'] = Class_Time::get_text($schedule_id);
      $text['conflict_times'] = Conflict_Time::get_text($schedule_id);
      $text['courses_to_schedule'] = Course_To_Schedule::get_text($schedule_id);
      $text['faculty_members'] = Faculty_Member::get_text($schedule_id);
      $text['prerequisites'] = Prerequisite::get_text($schedule_id);

      // Get schedule versions data
      $versions = Output_Version::where_schedule_id($schedule_id)->get();


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

    return View::make('admin.view_semester')
                  ->with('text', $text)
                  ->with('versions', $versions);
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

  public function post_scheduler()
  {
    $schedule_id = Input::get('schedule_id');

    $schedule = Schedule::find($schedule_id)->first();

    if( $schedule->has_available_rooms && $schedule->has_class_times &&
        $schedule->has_courses_to_schedule && $schedule->has_faculty_members)
    {
      $version_id = Scheduler::schedule_driver($schedule_id);

      $version = Output_Version::find($version_id);

      $html = "<tr><td><span id='". $version->id ."' class='version-lnk'>"
      . $version->name
      . "</span></td><td><span class='delete-version-lnk'>delete</span></td></tr>";

      $result = array("status" => "success",
                      "message" => "Scheduler called!",
                      "html" => $html);
    }
    else
    {
      $message = "Error!\n";

      if (! $schedule->has_available_rooms)
      {
        $message .= "Missing Available Rooms Input\n";
      }

      if (! $schedule->has_class_times)
      {
        $message .= "Missing Class Times Input\n";
      }

      if(! $schedule->has_courses_to_schedule)
      {
        $message .= "Missing Courses To Schedule Input\n";
      }

      if(! $schedule->has_faculty_members)
      {
        $message .= "Missing Faculty Members Input\n";
      }

      $result = array("status" => "success",
                      "message" => $message);
    }

    
/*
    $time_list = Scheduler::get_time_list($schedule_id);

    ob_start();
    var_dump($time_list);
    $contents = ob_get_contents();
    ob_end_clean();

    error_log( $contents );
 */ 
    //$result = array("status" => "error", "message" => "It works!");

    echo json_encode($result);
  }

  public function post_fill_prefs()
  {

    $schedule_id = Input::get('schedule_id');

    $result = Faculty_Preference::fill_prefs($schedule_id);

    echo json_encode($result);

  }

  public function post_display_output()
  {


    // for priority flag 0 is by seniority and 1 is by pref submission
    $schedule_id = Input::get('schedule_id');
    $output_version_id = Input::get('output_version_id');

    $courses_0 = Scheduled_Course::where_output_version_id($output_version_id)
                                        ->where_priority_flag('0')->get();
    $courses_1 = Scheduled_Course::where_output_version_id($output_version_id)
                                        ->where_priority_flag('1')->get();


    $class_name_html_0 = Output_Version::create_classes_by_class_name($courses_0);
    $room_html_0 = Output_Version::create_classes_by_room($courses_0);
    $faculty_html_0 = Output_Version::create_classes_by_faculty($courses_0);
    $time_html_0 = Output_Version::create_classes_by_time($courses_0);
    $seniority = $class_name_html_0 . $room_html_0 . $faculty_html_0 . $time_html_0;

    $class_name_html_1 = Output_Version::create_classes_by_class_name($courses_1);
    $room_html_1 = Output_Version::create_classes_by_room($courses_1);
    $faculty_html_1 = Output_Version::create_classes_by_faculty($courses_1);
    $time_html_1 = Output_Version::create_classes_by_time($courses_1);
    $submission = $class_name_html_1 . $room_html_1 . $faculty_html_1 . $time_html_1;


    echo json_encode(array("seniority" => $seniority, "submission" => $submission));

  }


  public function post_delete_version()
  {

    $version_id = Input::get('version_id');

    Output_Version::find($version_id)->delete();

    Scheduled_Course::where_output_version_id($version_id)->delete();

  }

}
