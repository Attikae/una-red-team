<?php

class Scheduler {

  public static function schedule_driver($schedule_id )
  {

    $schedule = Schedule::find($schedule_id);
    $time_string = date('m-d-Y H:i:s');
    $name = $schedule->name . " " . $schedule->year . " " . $time_string;

    error_log( $name );
    
    $output_version = Output_Version::create(
      array( "schedule_id" => $schedule_id,
             "name" => $name ) );

    $course_list = Scheduler::get_course_list( $schedule_id );
    $faculty_list = Scheduler::get_faculty_list( $schedule_id, $course_list, 0 );
    $time_list = Scheduler::get_time_list( $schedule_id );

    

    $faculty_list = Scheduler::get_faculty_list( $schedule_id, $course_list, 1 );

  }

  ////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////

  public static function create_scheduled_courses( $schedule_id, 
                                                   $output_id, 
                                                   $course_list, 
                                                   $faculty_list, 
                                                   $time_list )
  {
    foreach( $course_list as $course )
    {
      $sections = Scheduler::get_course_sections( $course->day_sections, 
                                                  $course->night_sections, 
                                                  $course->internet_sections );
    }
  }

  //////////////////////////////////////////////////////////////////////////////// 
  //////////////////////////////////////////////////////////////////////////////// 
  public static function get_faculty_list( $schedule_id, $course_list, $priority_bool )
  {
    // If priority_bool is 0, use seniority
    // If priority_bool is 1, use submission

    $faculty_list;

    if( $priority_bool == 0 )
    {
      $faculty = Faculty_Member::where_schedule_id($schedule_id)
                    ->order_by('years_of_service','desc')->get();
    }
    else
    {
      $faculty = Faculty_Member::where_schedule_id($schedule_id)
                    ->order_by('updated_prefs_at','desc')->get();
    }

    $i = 0;

    foreach( $faculty as $x )
    {
      $faculty_list[$i] = new Faculty_Blob;

      $faculty_list[$i]->faculty_id = $x->id;
      $faculty_list[$i]->hours = $x->hours;

      
      $j = 0;

      foreach( $course_list as $y )
      {
        $prefs = Faculty_Preference::where_faculty_id($x->id)
                     ->where_schedule_id($schedule_id)
                     ->where_course_id($y->id)->first();

        if( $prefs  )
        {
          $faculty_list[$i]->sections[$j][0] = $prefs->day_sections;
          $faculty_list[$i]->sections[$j][1] = $prefs->evening_sections;
          $faculty_list[$i]->sections[$j][2] = $prefs->internet_sections;

          $faculty_list[$i]->day_prefs[$j][0] = $prefs->early_morning;
          $faculty_list[$i]->day_prefs[$j][1] = $prefs->mid_day;
          $faculty_list[$i]->day_prefs[$j][2] = $prefs->late_afternoon;
        }
        else
        {
          // Handle setting everything to zero
          $faculty_list[$i]->sections[$j][0] = 0;
          $faculty_list[$i]->sections[$j][1] = 0;
          $faculty_list[$i]->sections[$j][2] = 0;

          $faculty_list[$i]->day_prefs[$j][0] = 0;
          $faculty_list[$i]->day_prefs[$j][1] = 0;
          $faculty_list[$i]->day_prefs[$j][2] = 0;
        }

        $j++;
      }

      $i++;
    }

    return $faculty_list;
  }

  ////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////

  public static function get_course_list( $schedule_id )
  {
    $courses = Course_To_Schedule::where_schedule_id($schedule_id)->get();

    $course_list;
    $i = 0;
    foreach( $courses as $x )
    {
      $course_list[$i][0] = $x;
      $course_list[$i][1] = filter_var( $x->course, FILTER_SANITIZE_NUMBER_INT);
      $i++;
    }

    $i = 0;
    foreach( $course_list as $x )
    {
      $sortCourse[$i] = $x[1];
      $i++;
    }

    array_multisort( $sortCourse, SORT_DESC, $course_list );

    $min_course_list;

    $i = 0;
    foreach( $course_list as $x )
    {
      $min_course_list[$i] = $x[0];
      $i++;
    }

    return $min_course_list;
  }

  ////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////

  public static function get_time_list( $schedule_id )
  {
    $class_times = Class_Time::where_schedule_id($schedule_id)->get();
    $avail_rooms = Available_Room::where_schedule_id($schedule_id)->get();

    $time_list;

    $i = 0;

    foreach( $class_times as $x )
    {
      $time_list[$i] = new Time_Blob;
      $time_list[$i]->class_time_id = $x->id;
      $time_list[$i]->m = $x->monday;
      $time_list[$i]->t = $x->tuesday;
      $time_list[$i]->w = $x->wednesday;
      $time_list[$i]->r = $x->thursday;
      $time_list[$i]->f = $x->friday;
      $time_list[$i]->s = $x->saturday;

      // calculate start_offset, end_offset, and credit_hours

      $start_time = strtotime( $x->starting_time );
      
      $hour = date( "H", $start_time );
      $minute = date( "i", $start_time );

      $time_list[$i]->start_offset = ($hour-7)*60 + $minute;
      $time_list[$i]->end_offset = $time_list[$i]->start_offset + $x->duration;

      $num_days = $time_list[$i]->m +
                  $time_list[$i]->t +
                  $time_list[$i]->w +
                  $time_list[$i]->r +
                  $time_list[$i]->f +
                  $time_list[$i]->s;

      $time_list[$i]->credit_hours = intval(($x->duration*$num_days)/50);

      $j = 0;

      foreach( $avail_rooms as $y )
      {
        $time_list[$i]->room_blobs[$j] = new Room_Blob;
        $time_list[$i]->room_blobs[$j]->id = $y->id;
        $time_list[$i]->room_blobs[$j]->type = $y->type;
        $time_list[$i]->room_blobs[$j]->size = $y->size;
        $time_list[$i]->room_blobs[$j]->is_taken = false;

        $j++;
      }

      $i++;
    }

    return $time_list;
  }

}
