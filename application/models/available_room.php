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
        // Separate each line of the file into an array
        $line_array = mb_split("\n", $file_string);
        
        $success = true;
        $result = array("status" => "", "message" => "");
        
        for ($i = 0; $i < count($line_array); $i++)
        {
            // Separate each word of a line into a multi-dimensional array
            // $i -> line number
            $word_array[$i] = mb_split(" ", $line_array[$i]);

            if(count($word_array[$i]) == 4)
            {
            
                //Correct classroom type is C, L, or B
                if(($word_array[$i][0] != 'C') && ($word_array[$i][0] != 'L') && 
                   ($word_array[$i][0] != 'B'))
                {
                    $success = false;
                    $result['status'] = "error";
                    $result['message'] = $result['message'] . 
                    "Incorrect type of room on line " . ($i+1) . ".\n";
                }
                
                //Size of room has to be between 1 and 100.
                if(($word_array[$i][1] > 100) || ($word_array[$i][1] < 1))
                {
                    $success = false;
                    $result['status'] = "error";
                    $result['message'] = $result['message'] . 
                    "Incorrect size of room on line " . ($i+1) . ".\n";
                }
                
                //Building name must contain all alphabetic characters
                if((!mb_ereg_match('^[A-Z]+$', $word_array[$i][2])) || 
                   (strlen($word_array[$i][2]) > 6) || 
                   (strlen($word_array[$i][2])< 2))
                {
                    $success = false;
                    $result['status'] = "error";
                    $result['message'] = $result['message'] . 
                    "Incorrect building name on line " . ($i+1) . ".\n"; 
                }

                //Room number can be 1 to 3 digits
                if((!mb_ereg_match('^\d{1,3}$', $word_array[$i][3])) || 
                   (strlen($word_array[$i][3]) < 1) || 
                   (strlen($word_array[$i][3]) > 3))
                {
                    $success = false;
                    $result['status'] = "error";
                    $result['message'] = $result['message'] . 
                    "Incorrect room number on line " . ($i+1) . ".\n";
                }
                
            }
            else
            {
                $success = false;
                $result['status'] = "error";
                $result['message'] = $result['message'] . 
                "Incorrect amount of arguments on line " . ($i+1) . ".\n";
            }
        
     
        }

        // Check for duplicate entries throughout the entries
        for($l_count = 0; $l_count < count($word_array); $l_count++)
        {
            if(count($word_array[$l_count]) == 4)
            {
                $temp1 = $word_array[$l_count][2];
                $temp2 = $word_array[$l_count][3];
                for($w_count = $l_count + 1; $w_count < count($word_array); 
                    $w_count++)
                {
                    if(count($word_array[$w_count]) == 4)
                    {
                        if($temp1 == $word_array[$w_count][2] && 
                            $temp2 == $word_array[$w_count][3])
                        {
                            $success = FALSE;
                            $result["status"] = "error";
                            $result["message"] = $result["message"] . 
                            "Duplicate entry found on line: " . 
                            ($w_count + 1) . "\n";
                        }
                    }
                }
            }
        }
        
        
        // Input all entries into the database if there are no errors found
        if ($success == true)
        {
            $result['status'] = "success";
            
            // Delete old records
            Available_Room::where_schedule_id($schedule_id)->delete();
            
            for($count = 0; $count < count($word_array); $count++)
            {
                $new_room = new Available_Room;
                $new_room->schedule_id = $schedule_id;
                $new_room->type = $word_array[$count][0];
                $new_room->size = $word_array[$count][1];
                $new_room->building = $word_array[$count][2];
                $new_room->room_number = $word_array[$count][3];
                $new_room->save();
            } 
            
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