<?php

class Available_Room extends Eloquent {

    public static $table = 'available_rooms';

    public static $timestamps = true;
    
    /**************************************************************************
    /* @function    scan
    /* @author      CJ Stokes
    /* @description This segment of code will scan an incoming file(the format
    /*              of which can be found in section A.2 of the specification
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
        error_log("1");
        // Separate each line of the file into an array
        $lineArray = mb_split("\n", $file_string);
        
        $success = true;
        $result = array("status" => "", "message" => "");
        
        for ($i = 0; $i < count($lineArray); $i++)
        {
            // Separate each word of a line into a multi-dimensional array
            // $i -> line number
            $wordArray[$i] = mb_split(" ", $lineArray[$i]);
            error_log("2");
            if(count($wordArray[$i]) == 4)
            {
            
                //Correct classroom type is C, L, or B
                error_log("3");
                if(($wordArray[$i][0] != 'C') && ($wordArray[$i][0] != 'L') && 
                   ($wordArray[$i][0] != 'B'))
                {
                    error_log("4");
                    $success = false;
                    $result['status'] = "error";
                    $result['message'] = $result['message'] . 
                    "Incorrect type of room on line " . ($i+1) . ".\n";
                }
                
                //Size of room has to be between 1 and 100.
                error_log("5");
                if(($wordArray[$i][1] > 100) || ($wordArray[$i][1] < 1))
                {
                    error_log("6");
                    $success = false;
                    $result['status'] = "error";
                    $result['message'] = $result['message'] . 
                    "Incorrect size of room on line " . ($i+1) . ".\n";
                }
                
                //Building name must contain all alphabetic characters
                error_log("7");
                if((!mb_ereg_match('^[A-Z]+$', $wordArray[$i][2])) || 
                   (strlen($wordArray[$i][2]) > 6) || 
                   (strlen($wordArray[$i][2])< 2))
                {
                    error_log("8");
                    $success = false;
                    $result['status'] = "error";
                    $result['message'] = $result['message'] . 
                    "Incorrect building name on line " . ($i+1) . ".\n"; 
                }

                //Room number can be 1 to 3 digits
                error_log("9");
                if((!mb_ereg_match('^\d{1,3}$', $wordArray[$i][3])) || 
                   (strlen($wordArray[$i][3]) < 1) || 
                   (strlen($wordArray[$i][3]) > 3))
                {
                    error_log("10");
                    $success = false;
                    $result['status'] = "error";
                    $result['message'] = $result['message'] . 
                    "Incorrect room number on line " . ($i+1) . ".\n";
                }
                
            }
            else
            {
                error_log("11");
                $success = false;
                $result['status'] = "error";
                $result['message'] = $result['message'] . 
                "Incorrect amount of arguments on line " . ($i+1) . ".\n";
                error_log("12");
                
                //break;
            }
        
     
        }
        error_log("13");
  
        // Checks for duplicates within the entries
        // $i -> line number
        // $j -> word number
        /*
        for($i = 0; $i < count($wordArray); $i++)
        {
            $temp = $wordArray[$i];
            if (count ($temp) == 4)
            {

                for ($j = 0; $j < count($wordArray); $j++)
                {
                        
                    if ($j != $i )
                    {
                        error_log("14");
                        if ($temp[2] == $wordArray[$j][2] && 
                            $temp[3] == $wordArray[$j][3])
                        { 
                            $success = false;
                            $result['status'] = "error";
                            $result['message'] =  $result['message'] . 
                            "Duplicate entries on line " . ($i+1) . " and " . 
                            ($j+1) . ".\n";
                        }
                        
                    }
                
                }
            }
            else{
                error_log("blank line"); 
            }
            
        }*/

        for($lCount = 0; $lCount < count($wordArray); $lCount++)
        {
            error_log("14");
            if(count($wordArray[$lCount]) == 4)
            {
                error_log("14.5");
                $temp1 = $wordArray[$lCount][2];
                $temp2 = $wordArray[$lCount][3];
                for($wCount = $lCount + 1; $wCount < count($wordArray); $wCount++)
                {
                    if(count($wordArray[$wCount]) == 4)
                    {
                        if($temp1 == $wordArray[$wCount][2] && $temp2 == $wordArray[$wCount][3])
                        {
                            error_log("14.7");
                            $success = FALSE;
                            $result["status"] = "error";
                            $result["message"] = $result["message"] . 
                            "Duplicate entry found on line: " . ($wCount + 1) . "\n";
                        }
                    }
                }
            }
        }
        
        
        error_log("15");
        // Input all entries into the database if there are no errors found
        if ($success == true)
        {
            $result['status'] = "success";
            
            // Delete old records
            Available_Room::where_schedule_id($schedule_id)->delete();
            
            for($count = 0; $count < count($wordArray); $count++)
            {
                $new_room = new Available_Room;
                $new_room->schedule_id = $schedule_id;
                $new_room->type = $wordArray[$count][0];
                $new_room->size = $wordArray[$count][1];
                $new_room->building = $wordArray[$count][2];
                $new_room->room_number = $wordArray[$count][3];
                $new_room->save();
            } 
            
        }
        error_log("16");
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
        $entries = Available_Room::where_schedule_id($schedule_id)->
                    order_by("id", "asc")->get();
        $text = "";
        $first_entry = true;

        foreach ($entries as $entry)
        {
            if($first_entry != true)
            {
                $text .= "\n";
            }
            $text .= $entry->type . " " . $entry->size . " " . $entry->building
                     . " " . $entry->room_number;

            $first_entry = false;
        }

        return $text;
    }
    //*************************************************************************
    //* End of get_text function
    //*************************************************************************
}