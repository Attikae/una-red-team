<?php


class Prerequisite extends Eloquent 
{

    public static $table = 'prerequisites';

    public static $timestamps = true;
    /**************************************************************************
    /* @function    scan
    /* @author      CJ Stokes
    /* @description This segment of code will scan an incoming file(the format
    /*              of which can be found in section A.5 of the specification
    /*              document) to ensure the correct format is found.
    /* @input       $schedule_id -> the identification number of the schedule
    /*              currently being created. This value is used when inputing 
    /*              to and extracting from into the database.
    /*              @file_string -> the string that holds the information to be
    /*              processed by the scanner.
    /* @output      Returns an array called $result with the indices status & 
    /*              message. Set $result['status'] equal to 'success' if 
    /*              everything goes as planned. Set $result['status'] equal to 
    /*              'error' if there is an issue. If there is an issue, set 
    /*              result['message'] to a string containing the line number 
    /*              and description of the issue.
    **************************************************************************/
    public static function scan($schedule_id, $file_string)
    {
        $file_stream = $file_string;
        $readSuccess = TRUE;
        
        mb_regex_encoding('UTF-8');
        mb_internal_encoding('UTF-8');
        
        $lineArray = array();
        $wordArray = array();
        $result = array("status" => "", "message" => "");
        
        // Separate each line of the file into an array
        $lineArray = mb_split('\n', $file_stream);

        for($count = 0; $count < count($lineArray); $count++)
        {
            // Separate each word of a line into a multi-dimensional array
            // $count -> line number
            $wordArray[$count] = mb_split(' ', $lineArray[$count]);

            // Check for correct number of arguments for a line
            if(count($wordArray[$count]) < 2)
            {
                $readSuccess = FALSE;
                $result["status"] = "error";
                $result["message"] = $result["message"] . 
                "Incorrect amount of field arguments on line: " . ($count + 1) . 
                "\n";
            }
            else
            {
                // Check each word of the line for correct format
                for($wordCount = 0; $wordCount < count($wordArray[$count]); 
                        $wordCount++)
                {
                    if(!mb_ereg_match('^[A-Z]{2,5}\d{3}[A-Z]{0,2}$', 
                        $wordArray[$count][$wordCount]))
                    {
                        $readSuccess = FALSE;
                        $result["status"] = "error";
                        $result["message"] = $result["message"] . 
                        "Incorrect class field on line: " . ($count + 1) . "\n";
                        break;
                    }
                }
            }
        }
        
        // Checks for duplicates within the entries
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
            Prerequisite::where_schedule_id($schedule_id)->delete();
                
            for($lineCount = 0; $lineCount < count($lineArray); $lineCount++)
            {
                for($wordCount = 1; $wordCount < count($lineArray[$lineCount]); 
                        $wordCount++)
                {
                    $new_prereq = new Prerequisite;
                    $new_prereq->schedule_id = $schedule_id;
                    
                    $new_prereq->course = $wordArray[$lineCount][0];
                    
                    $new_prereq->prereq = $wordArray[$lineCount][$wordCount];
                    
                    $new_prereq->save();
                }
                
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

        $entries = Prerequisite::where_schedule_id($schedule_id)->order_by("id",
                                                    "asc")->get();
        $text = "";
        $prev_course = "";

        foreach ($entries as $entry)
        {
            $cur_course = $entry->course;
            if($cur_course == $prev_course)
            {
                $text .= " " . $entry->prereq;
            }
            else
            {
                if($prev_course != "")
                {
                    $text .= "\n";
                }

                $text .= $entry->course . " " . $entry->prereq;
                $prev_course = $cur_course;
            }

        }

        return $text;
    }
    //*************************************************************************
    //* End of get_text function
    //*************************************************************************
}