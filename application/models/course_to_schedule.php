<?php

class Course_To_Schedule extends Eloquent {

  public static $table = 'courses_to_schedule';

  public static $timestamps = true;

  public static function scan($schedule_id, $file_string){

    /* Return an array called $result with the indices status & message.
       Set $result['status'] equal to 'success' if everything goes as planned.
       Set $result['status'] equal to 'error' if there is an issue.
       If there is an issue, set result['message'] to a string containing the line number
       and description of the issue */

       // For testing purposes
       $result = array("status" => "error", "message" => "course to schedule test");

       return $result;

  }
  
}