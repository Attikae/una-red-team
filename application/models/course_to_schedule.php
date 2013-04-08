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

    error_log("Count for array is: " . count($lineArray));

    for($count = 0; $count < count($lineArray); $count++)
    {



        $wordArray[$count] = mb_split(' ', $lineArray[$count]);

        error_log("WordArray is: " . $wordArray[$count][0] );

        error_log("Count for word array is: " . count($wordArray[$count]));

        if( count($wordArray[$count]) > 7)
        {
            // Push "Too Many Field Arguments" to error table
            error_log("In word array count check");
            $readSuccess = FALSE;
            $result["status"] = "error";
            $result["message"] = "error 1";
            break;
        }
        if( preg_match('[A-Z]{2,5}\d{3}[A-Z]{0,2}', $wordArray[$count][0]) === 0)
        {
            // Push "Incorrect Class Field" to error table
            error_log("In preg match");
            $readSuccess = FALSE;
            $result["status"] = "error";
            $result["message"] = "error 2";
            break;
        }
        error_log("Past preg match");
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
        error_log("Summing sessions!");
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

    error_log("Out of for loop");
    
    if($readSuccess == TRUE)
    {
        for($count = 0; $count < count($wordArray); $count++)
        {
            $newCourse = new Course;
            $newCourse->course = $wordArray[$count][0];
            $newCourse->day_sections = $wordArray[$count][1];
            $newCourse->night_sections = $wordArray[$count][2];
            $newCourse->internet_sections = $wordArray[$count][3];
            $newCourse->class_size = $wordArray[$count][4];
            $newCourse->room_type = $wordArray[$count][5];
            $newCourse->credit_hours = $wordArray[$count][6];
        }
        $result["status"] = "success";
    }
       


    return $result;

  }
  
}