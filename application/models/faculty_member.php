<?php

class Faculty_Member extends Eloquent {

  public static $table = 'faculty_members';

  public static $timestamps = true;

  public static function scan($schedule_id, $file_string){

    /* Return an array called $result with the indices status & message.
       Set $result['status'] equal to 'success' if everything goes as planned.
       Set $result['status'] equal to 'error' if there is an issue.
       If there is an issue, set result['message'] to a string containing the line number
       and description of the issue */

       // For testing purposes
       $result = array("status" => "error", "message" => "faculty member test");

       return $result;

  }
  
}