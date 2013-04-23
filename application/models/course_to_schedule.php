<?php
  
class Course_To_Schedule extends Eloquent {

    public static $table = 'courses_to_schedule';

    public static $timestamps = true;
    
  /****************************************************************************
  /* @function    scan
  /* @author      CJ Stokes
  /* @description This segment of code will scan an incoming file(the format of
  /*              which can be found in section A.3 of the specification
  /*              document) to ensure the correct format is found.
  /* @input       $schedule_id -> the identification number of the schedule
  /*              currently being created. This value is used when inputing to 
  /*              and extracting from into the database.
  /*              @file_string -> the string that holds the information to be
  /*              processed by the scanner.
  /* @output      Returns an array called $result with the indices status & 
  /*              message. Set $result['status'] equal to 'success' if 
  /*              everything goes as planned. Set $result['status'] equal to 
  /*              'error' if there is an issue. If there is an issue, set 
  /*              result['message'] to a string containing the line number and
  /*              description of the issue.
  ****************************************************************************/
    public static function scan($schedule_id, $file_string)
    {
        $file_stream = $file_string;
        $readSuccess = TRUE;
        $sessionSum = 0;
        
        mb_regex_encoding('UTF-8');
        mb_internal_encoding('UTF-8');
        
        $lineArray = array();
        $wordArray = array();
        $result = array("status" => "", "message" => "");
        
        // Separate each line of the file into an array
        $lineArray = mb_split('\n', $file_stream);

        //Process each line of the file string
        for($count = 0; $count < count($lineArray); $count++)
        {
            // Separate each word of a line into a multi-dimensional array
            // $count -> line number
            $wordArray[$count] = mb_split(' ', $lineArray[$count]);
            
            // Check for correct number of arguments for a line
            if(count($wordArray[$count]) != 7)
            {
                $readSuccess = FALSE;
                $result["status"] = "error";
                $result["message"] = $result["message"] . 
                "Incorrect amount of field arguments on line: " . 
                ($count + 1) . "\n"; 
            }
            else
            {
                $sessionSum = $wordArray[$count][1] + $wordArray[$count][2] + 
                $wordArray[$count][3];
                
                // Check each field of the array to for correct format
                if(!mb_ereg_match('^[A-Z]{2,5}\d{3}[A-Z]{0,2}$', 
                    $wordArray[$count][0]))
                {
                    $readSuccess = FALSE;
                    $result["status"] = "error";
                    $result["message"] = $result["message"] . 
                    "Incorrect Class Field on line: " . ($count + 1) . "\n";
                }
                elseif( ($wordArray[$count][1] > 100) || 
                        ($wordArray[$count][2] > 100) ||
                        ($wordArray[$count][3] > 100) )
                {
                    $readSuccess = FALSE;
                    $result["status"] = "error";
                    $result["message"] = $result["message"] . 
                    "Too many sessions for one field on line: " . 
                    ($count + 1) . "\n";
                }
                elseif( ($wordArray[$count][1] < 0) || 
                        ($wordArray[$count][2] < 0) ||
                        ($wordArray[$count][3] < 0) )
                {
                    $readSuccess = FALSE;
                    $result["status"] = "error";
                    $result["message"] = $result["message"] . 
                    "Too many sessions for one field on line: " . 
                    ($count + 1) . "\n";
                }
                elseif(!mb_ereg_match('^\d{1,3}$', $wordArray[$count][1]) ||
                        !mb_ereg_match('^\d{1,3}$', $wordArray[$count][2]) ||
                        !mb_ereg_match('^\d{1,3}$', $wordArray[$count][3]))
                {
                    $readSuccess = FALSE;
                    $result["status"] = "error";
                    $result["message"] = $result["message"] . 
                    "Incorrect session field on line: " . ($count + 1) . "\n";
                }
                elseif(($sessionSum > 100) || ($sessionSum < 1))
                {
                    $readSuccess = FALSE;
                    $result["status"] = "error";
                    $result["message"] = $result["message"] . 
                    "Sum of sessions exceeds 100\n";
                }
                elseif( ($wordArray[$count][4] == 0) || 
                        ($wordArray[$count][4] > 100) )
                {
                    $readSuccess = FALSE;
                    $result["status"] = "error";
                    $result["message"] = $result["message"] . 
                    "Incorrect Class Size on line: " . ($count + 1) . "\n";
                }
                elseif( ($wordArray[$count][5] != 'C') && 
                        ($wordArray[$count][5] != 'L') )
                {
                    $readSuccess = FALSE;
                    $result["status"] = "error";
                    $result['message'] = $result["message"] . 
                    "Incorrect Room Type on line: " . ($count + 1) . "\n";
                }
                elseif( ($wordArray[$count][6] < 1) || 
                        ($wordArray[$count][6] > 12) )
                {
                    $readSuccess = FALSE;
                    $result["status"] = "error";
                    $result["message"] = $result["message"] . 
                    "Incorrect Number of Credit Hours on line: " . 
                    ($count + 1) . "\n";
                }
                else
                {
                    for($itemCount = 0; $itemCount < count($wordArray[$count]); 
                    $itemCount++)
                    {
                        if($wordArray[$count][$itemCount] == NULL)
                        {
                            $readSuccess = FALSE;
                            $result["status"] = "error";
                            $result["message"] = $result["message"] . 
                            "Incorrect Number of field arguments on line: " . 
                            ($count + 1) . "\n";
                        }
                    }
                }
            }
        }
        
        // Check for duplicates within the entries
        for($lCount = 0; $lCount < count($wordArray); $lCount++)
        {
            $temp = $wordArray[$lCount][0];
            for($wCount = $lCount + 1; $wCount < count($wordArray); $wCount++)
            {
                if($temp == $wordArray[$wCount][0])
                {
                    $readSuccess = FALSE;
                    $result["status"] = "error";
                    $result["message"] = $result["message"] . 
                    "Duplicate entry found on line: " . ($wCount + 1) . "\n";
                }
            }
        }

        // Input all entries into the database if there are no errors found
        if($readSuccess == TRUE)
        {
            // Delete old records
            Course_To_Schedule::where_schedule_id($schedule_id)->delete();

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
    //*************************************************************************
    //* End of scan function
    //*************************************************************************

    /**************************************************************************
    /* @function    get_text
    /* @author      Atticus Wright
    /* @description This segment of code will retreive the contents of the 
    /*              values of the prerequisite database entries.
    /* @input       $schedule_id -> the identification number of the schedule
    /*              currently being created. This value is used when inputing
    /*              to and extracting from the database.
    /* @output      $text -> A string of the information for an entry.
    /*************************************************************************/
    public static function get_text($schedule_id)
    {

        $entries = Course_To_Schedule::where_schedule_id($schedule_id)->
                    order_by("id", "asc")->get();
        $text = "";
        $first_entry = true;

        foreach ($entries as $entry)
        {
            if($first_entry != true)
            {
                $text .= "\n";
            }
            $text .= $entry->course . " " . $entry->day_sections . " " . 
                     $entry->night_sections . " "
                     . $entry->internet_sections . " " . $entry->class_size . 
                     " " . $entry->room_type . " " . $entry->credit_hours;

            $first_entry = false;
        }

        return $text;
    }
    //*************************************************************************
    //* End of get_text function
    //*************************************************************************
}