<?php

class Scheduler {

  public static function schedule_driver($schedule_id, )
  {

    $schedule = Schedule::find($schedule_id);
    $time_string = date('m-d-Y H:i:s');
    $name = $schedule->name . " " $schedule->year . " " . $time_string;

    $output_version = Output_Version::create(array())

  }

  public static function get_faculty_list( $schedule_id, $priority_bool )
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
      $faculty_list[$i]->faculty_id = $x->id;
      $faculty_list[$i]->hours = $x->hours;

      $prefs = Faculty_Preference::where_faculty_id($x->id)
                   ->where_schedule_id($schedule_id)->get();

      $course_list = get_course_list( $schedule_id );

      $j = 0;

      foreach( $prefs as $y )
      {
        $faculty_list[$i]->sections[$j] = 
      }

      $i = $i + 1;
    }
  }
}

  public static function get_course_list( $schedule_id )
  {
    $courses = Course_To_Schedule::where_schedule_id($schedule_id)->get();

    $course_list;

    $i = 0;
    foreach( $courses as $x )
    {
      $course_list[$i][0] = $x;
      $course_list[$i][1] = filter_var( $x->course, FILTER_SANITIZE_NUMBER_INT);
      $i = $i + 1;
    }

    return $course_list;
  }
