<?php

class Admin_Controller extends Base_Controller
{

  public $restful = true;


  /**************************************************************************
  /* @function    get_admin_index
  /* @author      Atticus Wright
  /* @description Handles a get request to the admin index page
  /* @input       none
  /* @output      none
  /*************************************************************************/
	public function get_admin_index()
	{
    $query = Schedule::order_by('created_at', 'desc')->get();

		return View::make('admin.admin_index', array('schedules' => $query));
  }



  /**************************************************************************
  /* @function    post_admin_index
  /* @author      Atticus Wright
  /* @description Handles a post request from the admin index page
  /* @input       none
  /* @output      none
  /*************************************************************************/
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




  /**************************************************************************
  /* @function    get_view_faculty
  /* @author      Atticus Wright
  /* @description Hanles a get request to the view faculty page
  /* @input       none
  /* @output      none
  /*************************************************************************/
  public function get_view_faculty()
  {

    $users = User::where_user_type(2)->get();

    if(empty($users))
    {
      return View::make('admin.view_faculty')->with("message", "No users.");
    }
    else
    {
      return View::make('admin.view_faculty')->with("users", $users);
    }

    
  }


  /**************************************************************************
  /* @function    
  /* @author      
  /* @description This segment of code will 
  /* @input       $
  /* @output      $
  /*************************************************************************/
  public function get_semester_cp()
  {
    return View::make('admin.semester_cp');
  }

  /**************************************************************************
  /* @function    get_add_semester
  /* @author      Atticus Wright
  /* @description Handles a get request to the add semester page
  /* @input       none
  /* @output      none
  /*************************************************************************/
  public function get_add_semester()
  {
    return View::make('admin.add_semester');
  }


  /**************************************************************************
  /* @function    post_add_semester
  /* @author      Atticus Wright
  /* @description Handles a post request from the add semester page. Creates
  /*              a new schedule if the schedule does not already exist. If
  /*              the schedule already exists, redirects the user to the add
  /*              semester page with a message notifying them that the
  /*              schedule already exists.
  /* @input       none
  /* @output      none
  /*************************************************************************/
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


  /**************************************************************************
  /* @function    get_view_semester
  /* @author      Atticus Wright
  /* @description Handles a get request to the admin view semester page. Calls
  /*              the necessary functions to retrieve the text for the
  /*              scheduling data input boxes.
  /* @input       none
  /* @output      none
  /*************************************************************************/
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



  /**************************************************************************
  /* @function    post_file_upload
  /* @author      Atticus Wright
  /* @description This function handles the uploading of text files for
  /*              scanner input
  /* @input       none
  /* @output      none
  /*************************************************************************/
  public function post_file_upload()
  {
      $file = Input::file('fileToUpload');
      $inputType = Input::get('input-type');

      // Check to make sure the file is the appropriate type
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


  /**************************************************************************
  /* @function    post_scane
  /* @author      Atticus Wright
  /* @description This segment of code handles the calls to the different
  /*              scanners
  /* @input       $
  /* @output      $result containg the status of the scanner call
  /*************************************************************************/
  public function post_scan()
  {

    $file_type = Input::get('file_type');
    $file_string = Input::get('file_string');
    $schedule_id = Input::get('schedule_id');

    // Call the appropriate scanner based on the file type
    // designator (not indicative of an actual file type)
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


  /**************************************************************************
  /* @function    post_scheduler
  /* @author      Atticus Wright
  /* @description This segment of code handles a call to the scheduler. It
  /*              checks for the necessary input files before it will allow
  /*              the scheduler to be called.
  /* @input       $
  /* @output      $status indicating whether the scheduler succeeded or failed
  /*              $message containing more detailed info about the status
  /*************************************************************************/
  public function post_scheduler()
  {
    $schedule_id = Input::get('schedule_id');

    $schedule = Schedule::find($schedule_id)->first();

    // Make sure the necessary input has been filled
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
      // Generate an error message depending on what has not been filled
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



  /**************************************************************************
  /* @function    fill prefs
  /* @author      Atticus Wright
  /* @description This segment of handles a call to the fill_prefs function.             
  /* @input       $
  /* @output      $result containing the status of the call
  /*************************************************************************/
  public function post_fill_prefs()
  {

    $schedule_id = Input::get('schedule_id');

    $result = Faculty_Preference::fill_prefs($schedule_id);

    echo json_encode($result);

  }



  /**************************************************************************
  /* @function    post_display output
  /* @author      Atticus Wright
  /* @description This segment of code calls the necessary functions for 
  /*              generating the html and needed data for viewing a 
  /*              schedule and passes that html and data back to an
  /*              ajax call
  /* @input       $
  /* @output      $seniority containing the html for the seniority container
  /*              $submission containing the html for the by preference
  /*              submission container
  /*              $classBlocks0 containing the class block divs for the
  /*              seniority container
  /*              $classBlocks1 containing the class block divs for the
  /*              by preference submission container
  /*              $faculty containing the faculty available for the schedule
  /*              version
  /*              $rooms containing the rooms avialable for the schedule 
  /*              version
  /*              $outputVersionId containing the output version id
  /*************************************************************************/
  public function post_display_output()
  {

    // for priority flag 0 is by seniority and 1 is by pref submission
    $schedule_id = Input::get('schedule_id');
    $output_version_id = Input::get('output_version_id');

    // Get the scheduled courses for each priority type
    $courses_0 = Scheduled_Course::where_output_version_id($output_version_id)
                                        ->where_priority_flag('0')
                                        ->where('section_number', '!=', 'X')->get();

    $courses_1 = Scheduled_Course::where_output_version_id($output_version_id)
                                        ->where_priority_flag('1')
                                        ->where('section_number', '!=', 'X')->get();

    // Get the unscheduled courses for each priority type                                  
    $not_scheduled_0 = Scheduled_Course::where_output_version_id($output_version_id)
                                        ->where_priority_flag('0')
                                        ->where_section_number('X')->get();

    $not_scheduled_1 = Scheduled_Course::where_output_version_id($output_version_id)
                                        ->where_priority_flag('1')
                                        ->where_section_number('X')->get();

    // Get the faculty and rooms used to create the schedule version
    $faculty = Faculty_Member::where_schedule_id(0)
                                ->where_output_version_id($output_version_id)->get();

    $rooms = Available_Room::where_schedule_id(0)
                                ->where_output_version_id($output_version_id)->get();

    // Get the html for the by seniority priority type                        
    $class_name_html_0 = Output_Version::create_classes_by_class_name($courses_0);
    $room_html_0 = Output_Version::create_classes_by_room_tables($output_version_id, 0);
    $faculty_html_0 = Output_Version::create_classes_by_faculty($courses_0);
    $time_html_0 = Output_Version::create_classes_by_time($courses_0);
    $not_scheduled_html_0 = Output_Version::create_not_scheduled($not_scheduled_0);
    $seniority = $class_name_html_0 . $room_html_0 . $faculty_html_0 . 
                 $time_html_0 . $not_scheduled_html_0;

    // Get the html for the by preference submission priority type
    $class_name_html_1 = Output_Version::create_classes_by_class_name($courses_1);
    $room_html_1 = Output_Version::create_classes_by_room_tables($output_version_id, 1);
    $faculty_html_1 = Output_Version::create_classes_by_faculty($courses_1);
    $time_html_1 = Output_Version::create_classes_by_time($courses_1);
    $not_scheduled_html_1 = Output_Version::create_not_scheduled($not_scheduled_1);
    $submission = $class_name_html_1 . $room_html_1 . $faculty_html_1 .
                  $time_html_1 . $not_scheduled_html_1;

    // Get the data for creating the class blocks divs
    $seniority_class_blocks = Output_Version::get_class_blocks_data($courses_0);
    $submission_class_blocks = Output_Version::get_class_blocks_data($courses_1);

    // Get the data for creating the faculty and room select options
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



  /**************************************************************************
  /* @function    post_delete_version
  /* @author      Atticus Wright
  /* @description This segment of code will delete an output version and
  /*              the scheduled courses associated wiht it
  /* @input       $
  /* @output      $
  /*************************************************************************/
  public function post_delete_version()
  {

    $version_id = Input::get('version_id');

    Output_Version::find($version_id)->delete();

    Scheduled_Course::where_output_version_id($version_id)->delete();

  }



  /**************************************************************************
  /* @function    post_edit_course
  /* @author      Atticus Wright
  /* @description This segment of code ensures that an editing or scheduling
  /*              action on a course is valid. If valid, it edits the course.
  /*              If not, it returns an error message.
  /* @output      $status detailing whether edting failed or succeeded
  /*              $message giving more detail about the status
  /*              $priority detailing whether a course for the seniority or
  /*              preference sumbission containers was edited
  /*************************************************************************/
  public function post_edit_course()
  {

    $schedule_id = Input::get("schedule_id");
    $schedule = Schedule::find($schedule_id);

    if($schedule->is_published == 1)
    {
      $status = "error";
      $message = "Cannot edit version of already published schedule";
      $edit = false;
    }
    else
    {
      $to_edit_course_id = Input::get("course_id");
      $action = Input::get("action");
      $output_version_id = Input::get("output_version_id");

      // Check whether we are editing a course or scheduling an
      // unscheduled course
      if($action == 'edit')
      {
        error_log("action was edit");
        $priority = Input::get("priority");
        $class_size = Input::get("class_size");
        $course_type = Input::get("course_type");

      }
      else if($action == 'schedule')
      {
        $to_schedule_course = Scheduled_Course::find($to_edit_course_id);
        $priority = $to_schedule_course->priority_flag;
        $class_size = $to_schedule_course->class_size;
        $course_type = $to_schedule_course->course_type;

        // Get courses with the same name
        $section_courses = Scheduled_Course::where_output_version_id($output_version_id)
                              ->where_priority_flag($priority)
                              ->where_course($to_schedule_course->course)
                              ->where('section_number', '!=', 'X')
                              ->get();

              
        // Generate the appropriate section number for the new course
        if(empty($section_courses))
        {
          $section_number = "01";
        }
        else
        {
          $sections = array();
          $i = 0;
          foreach ($section_courses as $value) {
            $sections[$i] = intval($value->section_number);
            $i++;
          }
          $section_number = max($sections) + 1;
          if($section_number < 10)
          {
            $section_number = "0" . $section_number;
          }
        }

      }

      // Grab needed data for editing the course
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


      // Calculate needed values for checking for a conflict
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
      
      // Make sure the selected room is valid
      if($query_room->type != "B" && $query_room->type != $course_type)
      {
        $edit = false;
        $status = "error";
        $message .= "Room type of " . $query_room->type . " does not match" .
                    " course type of " . $course_type . "!\n";
      }

      // Make sure the selected room size is valid
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
        // Check for conflicts with already scheduled courses
        foreach ($courses as $course) {
          if($course->id != $to_edit_course_id)
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

      //Edit the course if no conflicts existed
      $course_to_edit = Scheduled_Course::find($to_edit_course_id);

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

      if($action == "schedule")
      {
        $course_to_edit->section_number = $section_number;
      }

      $course_to_edit->save();

      $status = "success";
      $message = "Course successfully edited!";

    }


    echo json_encode(array("status" => $status,
                           "message" => $message,
                           "priority" =>$priority));

  }


  


  /**************************************************************************
  /* @function    post_update_container
  /* @author      Atticus Wright
  /* @description This segment of code will update the scheduling container
  /*              for which a course has been editing or scheduled.
  /* @input       $
  /* @output      $html containing the updated html
  /*              $classBlocks containing the updated class Blocks
  /*************************************************************************/
  public function post_update_container()
  {

    // See comments on post_display_output for insight into how this
    // function works


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



  /**************************************************************************
  /* @function    post_published_schedule
  /* @author      Atticus Wright
  /* @description This segment of code will handles the publishing of a
  /*              schedule and ensure that the schedule has not already
  /*              been published
  /* @input       $
  /* @output      $message detailing whether the schedule was published
  /*              or has already been published
  /*************************************************************************/
  public function post_publish_schedule()
  {

    $schedule_id = Input::get('schedule_id');
    $output_version_id = Input::get('output_version_id');
    $priority = Input::get('priority');

    $schedule = Schedule::find($schedule_id);

    // make sure the schedule is not already published
    if($schedule->is_published == 1)
    {
      $message = "Schedule has already been published!";
    }
    else
    { 
      // If not, set it to published in the database
      // and store the version id and priority type
      // of the schedule version being published
      $schedule->is_published = 1;
      $schedule->published_version_id = $output_version_id;
      $schedule->published_priority = $priority;
      $schedule->save();

      $message = "Schedule successfully published!";
    }

    echo json_encode(array("message" => $message));
    

  }

  /**************************************************************************
  /* @function    post_delete_user
  /* @author      Atticus Wright
  /* @description This segment of code will unlock a user account
  /* @input       $
  /* @output      $
  /*************************************************************************/
  public function post_unlock_user()
  {

    $user_id = Input::get("user_id");

    $user = User::find($user_id);
    $user->is_locked = 0;
    $user->save();

  }

  /**************************************************************************
  /* @function    post_delete_user
  /* @author      Atticus Wright
  /* @description This segment of code will delete a user account
  /* @input       $
  /* @output      $
  /*************************************************************************/
  public function post_delete_user()
  {

    $user_id = Input::get("user_id");

    User::find($user_id)->delete();
  }

}
