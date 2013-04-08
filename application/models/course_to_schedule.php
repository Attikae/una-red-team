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
       // Scanner for Courses to Schedule
    $file_stream = $file_string;
    $readSuccess = TRUE;
    $sessionSum = 0;
    
    mb_regex_encoding('UTF-8');
    mb_internal_encoding('UTF-8');
    
    $lineArray = array();
    $wordArray = array();
    $result = array("status" => "", "message" => "");
    
    $lineArray = mb_split('\n', $file_stream);

    for($count = 0; $count < count($lineArray); $count++)
    {

        $wordArray[$count] = mb_split(' ', $lineArray[$count]);

        if( count($wordArray[$count]) > 7)
        {
            // Push "Too Many Field Arguments" to error table
            error_log("In 1st if");
            $readSuccess = FALSE;
            $result["status"] = "error";
            $result["message"] = "error 1";
            break;
        }
        //if( preg_match('[A-Z]{2,5}\d{3}[A-Z]{0,2}', $wordArray[$count][0]) === 0)
        if( ! mb_ereg_match('[A-Z]{2,5}\d{3}[A-Z]{0,2}', $wordArray[$count][0]) )
        {
            // Push "Incorrect Class Field" to error table
            error_log("In 2nd if");
            $readSuccess = FALSE;
            $result["status"] = "error";
            $result["message"] = "error 2";
            break;
        }
        if( ($wordArray[$count][1] > 100) || ($wordArray[$count][2] > 100) ||
            ($wordArray[$count][3] > 100))
        {
            // Push "Too Many Sessions" to error table
            error_log("In 3rd if ");
            $readSuccess = FALSE;
            $result["status"] = "error";
            $result["message"] = "error 3";
            break;
        }
        $sessionSum = $wordArray[$count][1] + $wordArray[$count][2] + $wordArray[$count][3];
        if($sessionSum > 100)
        {
            // Push "Too Many Sessions" to error table
            error_log("In  4th if");
            $readSuccess = FALSE;
            $result["status"] = "error";
            $result["message"] = "error 4";
            break;
        }
        if(($wordArray[$count][4] == 0) || ($wordArray[$count][4] > 100))
        {
            // Push "Incorrect Class Size" to error table
            error_log("In 5th if");
            $readSuccess = FALSE;
            $result["status"] = "error";
            $result["message"] = "error 5";
            break;
        }
        if(($wordArray[$count][5] != 'C') && ($wordArray[$count][5] != 'L'))
        {
            $result['message'] = "Incorrect Room Type on line: " + $count;
            error_log("In 6th if");
            $readSuccess = FALSE;
            $result["status"] = "error";
            $result["message"] = "error 6";
            break;
        }
        if(($wordArray[$count][6] < 1) || ($wordArray[$count][6] > 12))
        {
            // Push "Incorrect Number of Credit Hours" to error table
            error_log("In 7th if ");
            $readSuccess = FALSE;
            $result["status"] = "error";
            $result["message"] = "error 7";
            break;
        }
    }
    
    if($readSuccess == TRUE)
    {
        for($count = 0; $count < count($wordArray); $count++)
        {
            $new_course = new Course_To_Schedule;
            $new_course->schedule_id = $schedule_id;
            $new_course->course = $wordArray[$count][0];
            $new_course->day_sections = $wordArray[$count][1];
            $new_course->night_sections = $wordArray[$count][2];
            $new_course->internet_sections = $wordArray[$count][3];
            $new_course->class_size = $wordArray[$count][4];
            $new_course->room_type = $wordArray[$count][5];
            $new_course->credit_hours = $wordArray[$count][6];
            $new_course->save();
        } 
        $result["status"] = "success";
    }

    return $result;

  }
  
}