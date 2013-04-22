<?php

class Faculty_Preference extends Eloquent {

  public static $table = 'faculty_preferences';

  public static $timestamps = true;


  puplic static function fill_prefs($schedule_id)
  {

    $faculty_members = Faculy_Member::where_schedule_id($schedule_id);
    $courses = Course_To_Schedule::where_schedule_id($schedule_id);

    foreach ($faculty_members as $faculy_member) {
      foreach($courses as $course){
        //Create faculty preference entries
      }
    }


  }

}