<?php

class Scheduler {

  public static function schedule_driver($schedule_id )
  {

    $schedule = Schedule::find($schedule_id);
    $time_string = date('m-d-Y H:i:s');
    $name = $schedule->name . " " . $schedule->year . " " . $time_string;

    $output_version = Output_Version::create(array());

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
      $faculty_list[$i] = new Faculty_Blob;

      $faculty_list[$i]->faculty_id = $x->id;
      $faculty_list[$i]->hours = $x->hours;

      $course_list = Scheduler::get_course_list( $schedule_id );

      $j = 0;

      foreach( $course_list as $y )
      {
        $prefs = Faculty_Preference::where_faculty_id($x->id)
                     ->where_schedule_id($schedule_id)
                     ->where_course_id($y[0]->id)->first();

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

    return $course_list;
  }

}
