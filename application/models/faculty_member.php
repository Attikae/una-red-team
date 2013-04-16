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

      // delete old records
      //Faculty_Member::where_schedule_id($schedule_id)->delete();

       // For testing purposes
       $result = array("status" => "error", "message" => "faculty member test");

       return $result;

  }


  public static function get_text($schedule_id)
  {

    $entries = Faculty_Member::where_schedule_id($schedule_id)->order_by("id", "asc")->get();
    $text = "";
    $first_entry = true;

    foreach ($entries as $entry)
    {
      $user = User::find($entry->user_id);

      if($first_entry != true){
        $text .= "\n";
      }

      $text .= $entry->last_name . ", " . $entry->first_name . " " . $entry->years_of_service . " "
               . $user->email . " " . $entry->hours . "\n";

      $first_entry = false;
    }

    return $text;
  }
  
}