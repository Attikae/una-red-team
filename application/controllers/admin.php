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
                                        ->where_priority_flag('0')
                                        ->where('section_number', '!=', 'X')->get();

    $courses_1 = Scheduled_Course::where_output_version_id($output_version_id)
                                        ->where_priority_flag('1')
                                        ->where('section_number', '!=', 'X')->get();


    $not_scheduled_0 = Scheduled_Course::where_output_version_id($output_version_id)
                                        ->where_priority_flag('0')
                                        ->where_section_number('X')->get();

    $not_scheduled_1 = Scheduled_Course::where_output_version_id($output_version_id)
                                        ->where_priority_flag('1')
                                        ->where_section_number('X')->get();


    $faculty = Faculty_Member::where_schedule_id(0)
                                ->where_output_version_id($output_version_id)->get();

    $rooms = Available_Room::where_schedule_id(0)
                                ->where_output_version_id($output_version_id)->get();

    $class_name_html_0 = Output_Version::create_classes_by_class_name($courses_0);
    $room_html_0 = Output_Version::create_classes_by_room_tables($output_version_id, 0);
    $faculty_html_0 = Output_Version::create_classes_by_faculty($courses_0);
    $time_html_0 = Output_Version::create_classes_by_time($courses_0);
    $not_scheduled_html_0 = Output_Version::create_not_scheduled($not_scheduled_0);
    $seniority = $class_name_html_0 . $room_html_0 . $faculty_html_0 . 
                 $time_html_0 . $not_scheduled_html_0;

    $class_name_html_1 = Output_Version::create_classes_by_class_name($courses_1);
    $room_html_1 = Output_Version::create_classes_by_room_tables($output_version_id, 1);
    $faculty_html_1 = Output_Version::create_classes_by_faculty($courses_1);
    $time_html_1 = Output_Version::create_classes_by_time($courses_1);
    $not_scheduled_html_1 = Output_Version::create_not_scheduled($not_scheduled_1);
    $submission = $class_name_html_1 . $room_html_1 . $faculty_html_1 .
                  $time_html_1 . $not_scheduled_html_1;

    $seniority_class_blocks = Output_Version::get_class_blocks_data($courses_0);
    $submission_class_blocks = Output_Version::get_class_blocks_data($courses_1);

    $faculty_data = Output_Version::get_faculty_data($faculty);
    $rooms_data = Output_Version::get_rooms_data($rooms);

    echo json_encode(array("seniority" => $seniority,
                           "submission" => $submission,
                           "classBlocks0" => $seniority_class_blocks,
                           "classBlocks1" => $submission_class_blocks,
                           "faculty" => $faculty_data,
                           "rooms" => $rooms_data,
                           "outputVersionId" => $output_version_id));

  }


  public function post_delete_version()
  {

    $version_id = Input::get('version_id');

    Output_Version::find($version_id)->delete();

    Scheduled_Course::where_output_version_id($version_id)->delete();

  }

  public function post_edit_course()
  {

    $schedule_id = Input::get("schedule_id");
    $output_version_id = Input::get("output_version_id");
    $priority = Input::get("priority");
    $scheduled_course_id = Input::get("course_id");
    $class_size = Input::get("class_size");
    $course_type = Input::get("course_type");
    $start_hour = Input::get("start_hour");
    $start_minute = Input::get("start_minute");
    $duration = Input::get("duration");
    $m = Input::get("monday");
    $t = Input::get("tuesday");
    $w = Input::get("wednesday");
    $r = Input::get("thursday");
    $f = Input::get("friday");
    $s = Input::get("saturday");
    $user_id = Input::get("user_id");
    $faculty_name = Input::get("faculty_name");
    $building_and_room = Input::get("room");

    $message = "";
    $status = "";
    $edit = true;


    $schedule = Schedule::find($schedule_id);

    if($schedule->is_published == 1)
    {
      $status = "error";
      $message = "Cannot edit version of already published schedule";
      $edit = false;
    }
    else
    {
      $start_time = $start_hour . ":" . $start_minute . ":00";

      $space_pos = strpos($building_and_room, " ");
      $building = substr($building_and_room, 0, $space_pos);
      $room = substr($building_and_room, $space_pos+1);
      $edit_course_days = $m . $t . $w . $r . $f . $s;
      $edit_start_offset = 0;
      $edit_end_offset = 0;

      Scheduler::get_start_end_offsets($start_time, $duration,
                                          $edit_start_offset, $edit_end_offset);


      $query_room = Available_Room::where_schedule_id(0)
                              ->where_output_version_id($output_version_id)
                              ->where_building($building)
                              ->where_room_number($room)->first();
    }


    //Calculate faculty hours and do faculty check.
    if($edit == true)
    {           
          
      if($query_room->type != "B" && $query_room->type != $course_type)
      {
        $edit = false;
        $status = "error";
        $message .= "Room type of " . $query_room->type . " does not match" .
                    " course type of " . $course_type . "!\n";
      }

      if($class_size > $query_room->size)
      {
        $edit = false;
        $status = "error";
        $message .= "Room size of " . $query_room->size . " insufficient for" .
                    " class size of " . $class_size . "!\n";
      }
    }


    if($edit == true)
    {

      $courses = Scheduled_Course::where_output_version_id($output_version_id)
                                  ->where_priority_flag($priority)
                                  ->where_building($building)
                                  ->where_room_number($room)->get();

      if(! empty($courses))
      {
        foreach ($courses as $course) {
          if($course->id != $scheduled_course_id)
          {
            $days = $course->monday . $course->tuesday . $course->wednesday .
                    $course->thursday . $course->friday . $course->saturday;

            $start_offset = 0;
            $end_offset = 0;

            Scheduler::get_start_end_offsets($course->start_time,
                                             $course->duration,
                                             $start_offset, 
                                             $end_offset);

            $is_conflict = Scheduler::is_intersected( $edit_course_days, 
                                                      $days, 
                                                      $edit_start_offset,
                                                      $edit_end_offset, 
                                                      $start_offset, 
                                                      $end_offset);

            if($is_conflict == true)
            {
              $edit = false;
              $status = "error";
              $message .= "Specified start time conflicts with " . $course->course .
                           "-" . $course->section_number;
              break;
            }
          }

        }
      }
    }


    if($edit == true)
    {

      $course_to_edit = Scheduled_Course::find($scheduled_course_id);

      $course_to_edit->user_id = $user_id;
      $course_to_edit->faculty_name = $faculty_name;
      $course_to_edit->start_time = $start_time;
      $course_to_edit->building = $building;
      $course_to_edit->room_number = $room;
      $course_to_edit->monday = $m;
      $course_to_edit->tuesday = $t;
      $course_to_edit->wednesday = $w;
      $course_to_edit->thursday = $r;
      $course_to_edit->friday = $f;
      $course_to_edit->saturday = $s;
      $course_to_edit->duration = $duration;
      $course_to_edit->save();

      $status = "success";
      $message = "Course successfully edited!";

    }

    echo json_encode(array("status" => $status,
                           "message" => $message));

  }


  public function post_update_container()
  {


    $output_version_id = Input::get("output_version_id");
    $priority_flag = Input::get("priority_flag");

    $courses = Scheduled_Course::where_output_version_id($output_version_id)
                                        ->where_priority_flag($priority_flag)
                                        ->where('section_number', '!=', 'X')->get();

    $not_scheduled = Scheduled_Course::where_output_version_id($output_version_id)
                                        ->where_priority_flag($priority_flag)
                                        ->where_section_number('X')->get();


    $class_name_html = Output_Version::create_classes_by_class_name($courses);
    $room_html = Output_Version::create_classes_by_room_tables($output_version_id,
                                                               $priority_flag);
    $faculty_html = Output_Version::create_classes_by_faculty($courses);
    $time_html = Output_Version::create_classes_by_time($courses);
    $not_scheduled_html = Output_Version::create_not_scheduled($not_scheduled);
    $html = $class_name_html . $room_html . $faculty_html . $time_html .
            $not_scheduled_html;


    $class_blocks = Output_Version::get_class_blocks_data($courses);


    echo json_encode(array("html" => $html,
                           "classBlocks" => $class_blocks));

  }


  public function post_publish_schedule()
  {

    $schedule_id = Input::get('schedule_id');
    $output_version_id = Input::get('output_version_id');
    $priority = Input::get('priority');

    $schedule = Schedule::find($schedule_id);

    if($schedule->is_published == 1)
    {
      $message = "Schedule has already been published!";
    }
    else
    { 
      $schedule->is_published = 1;
      $schedule->published_version_id = $output_version_id;
      $schedule->published_priority = $priority;
      $schedule->save();

      $message = "Schedule successfully published!";
    }

    echo json_encode(array("message" => $message));
    

  }

}
