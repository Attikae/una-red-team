<?php

class Faculty_Controller extends Base_Controller
{
  public $restful = true;

  public function get_faculty_index()
  {
    $query = Schedule::order_by('created_at', 'desc')->get();

    return View::make('faculty.faculty_index', array('schedules' => $query));
  }

  public function post_faculty_index()
  {
    $schedule_id = Input::get('semester-select');

    Session::put('faculty_schedule_id', $schedule_id);

    if($schedule_id == "default")
    {
      return Redirect::to_action('faculty@faculty_index');
    }
    else
    {
      return Redirect::to_action('faculty@faculty_view_semester');
    }
  }

  public function get_faculty_view_semester()
  {
    if (Session::has('faculty_schedule_id')){

            // Get schedule data
      $schedule_id = Session::get('faculty_schedule_id');
      $schedule = Schedule::find($schedule_id);
      $semester = $schedule->name . " " . $schedule->year;
      Session::put('semester', $semester);

      $courses = Course_To_Schedule::where_schedule_id($schedule_id)->get();


    } 
    else{
      $semester = "No semester";
      $courses = "";
    }

    return View::make('faculty.faculty_view_semester')->with('courses', $courses);
  }

  public function post_submit_prefs()
  {

    $schedule_id = Session::get('faculty_schedule_id');
    $faculty_id = Session::get('user_id');

    // delete old preferences
    Faculty_Preference::where_schedule_id_and_faculty_id($schedule_id, $faculty_id)->delete();
    $prefs_data = $_REQUEST['prefs_array'];

    error_log("Before for loop");
    for($i = 0; $i < count($prefs_data); $i++)
    {

      $pref = new Faculty_Preference;
      $pref->schedule_id = $schedule_id;
      $pref->user_id = $faculty_id;
      $pref->course_id = $prefs_data[$i][0];
      $pref->early_morning = $prefs_data[$i][1];
      $pref->mid_day = $prefs_data[$i][2];
      $pref->late_afternoon = $prefs_data[$i][3];
      $pref->day_sections = $prefs_data[$i][4];
      $pref->evening_sections = $prefs_data[$i][5];
      $pref->internet_sections = $prefs_data[$i][6];
      $pref->save();
    }
  
  }

}

