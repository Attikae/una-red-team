<?php

class Conflict_Time extends Eloquent {

    public static $table = 'conflict_times';

    public static $timestamps = true;
	
    /**************************************************************************
    /* @function    scan
    /* @author      Jordan Shook
    /* @description This segment of code will scan an incoming file(the format
    /*              of which can be found in section A.4 of the specification
    /*              document) to ensure the correct format is found.
    /* @input       $schedule_id -> the identification number of the schedule
    /*              currently being created. This value is used when inputing 
    /*              to and extracting from the database.
    /*              @file_string -> the string that holds the information to be
    /*              processed by the scanner.
    /* @output      Returns an array called $result with the indices status & 
    /*              message. Set $result['status'] equal to 'success' if 
    /*              everything goes as planned. Set $result['status'] equal to 
    /*              'error' if there is an issue. If there is an issue, set 
    /*              result['message'] to a string containing the line number 
    /*              and description of the issue.
    **************************************************************************/

    public static function scan($schedule_id, $file_string){

        $correct = true;
        $empty_line = false;
		
        // Separate each line of the file into an array
        $line_array = mb_split("\n", $file_string);
		
		// Used to store database entries
        $store = array();
        $result = array("status" => "", "message" => "");
        $store_count = 0; 
         
        for ($i = 0; $i < count($line_array) ; $i++)
        {
            //Grab each string separated by spaces   
            $word_array[$i] = mb_split(" ", $line_array[$i]);
            if (count($word_array[$i]) > 1 )
            {
            
                for ($j = 0; $j < count($word_array[$i]) ; $j++)
                {
                
                    if ( $j == 0)
                    {
                        $store[$store_count][0] = $word_array[$i][0];
                    
                        // Checking for correct course name
                        if (!mb_ereg_match('^[A-Z]{2,5}\d{3}[A-Z]{0,2}$', 
                            $word_array[$i][0]))
                        {    
                         
                            $correct = false; 
                            $result['status'] = 'error';
                            $result['message'] = $result['message'] .
                            "\nError with course name on line " . ($i+1) . '.';
							
                            break; 
                        }
                        
                    }
                    
                    else
                    {
						//will hold the time for storage into database
                        $time = " ";                        
                        //will hold the days for storage into database
                        $days = array();                    
                        //will hold day(s)/time combinations
                        $temp_word[0] = $word_array[$i][$j]; 
                        //counters                         
                        $temp_count = 0;                     
                        $count = 0;                         
                        
                        
                        
                        // If the combination has a slash it will enter into this
                        // loop
                        if (!mb_ereg_match('^[^:]*$', $temp_word[0]) &&
                         !mb_ereg_match('^[^/]*$', $temp_word[0])  )
                        {
                            
							error_log("in the slash loop");	
                                                                  
                            while ($temp_word[0][$temp_count] != "/" 
                            && $temp_count < strlen($temp_word[0]) )
                            {
                            	error_log("in while");
                                // Gather all the days
                                $days[$temp_count] = $temp_word[0][$temp_count];    
                                $temp_count++; 
                            }
                            
                            // Skip over the slash
                            $temp_count++;
                            $count = 0; 
                            
                            while ($temp_count < strlen($temp_word[0]))
                            {
                            
                                //Will separate the times from days 
                                
                                $time[$count] = $temp_word[0][$temp_count];
                                $temp_count++;
                                $count++;
                            
                            }
                            
                            // Checking for correct time
                            // ***Time has to be between 07:00 and 18:30
                            if (strlen($time) == 5 )
                            {
                                // ex: 09:00
                                if ($time[0] == 0 )
                                {
                                
                                    if ( $time[1] >= 7 && $time[1] <= 9)
                                    {
                         
                                        if (($time[3] + $time[4]) == 0 || 
                                            ($time[3] + $time[4]) == 3 )
                                        {
                                        
                                            if ($time[4] == 3 && $time[1] == 8) 
                                            {
                                                $result["status"] = "error";
                                                $result["message"] = 
                                                $result["message"]
                                                . "\nError with time on line " . 
                                                 ($i+1) . '.';
                                                $correct = false;
                                            }
                                            
                                        }
										else
										{
											$result["status"] = "error";
                                            $result["message"] = $result["message"].
                                            "\nError with time on line " . ($i+1) . 
                                            '.';
                                            $correct = false;
                                        }	
										
                                        
                                    }
                                                     
                                    else
                                    {
                                        $result["status"] = "error";
                                        $result["message"] = $result["message"] .
                                        "\nError with time on line " . ($i+1) . '.';
                                        $correct = false;
                                    } 
                                }
                                
                                // ex: 10:00
                                elseif ($time[0] == 1)
                                {    
                                
                                    if ( $time[1] >= 0 && $time[1] <= 8)
                                    {
                                    
                                        if (($time[3]!= 0 || $time[3]!=3) &&
												$time[4] !=0 )
                                        {
                                        
                                                $result["status"] = "error";
                                                $result["message"] = 
                                                $result["message"] .
                                                "\nError with time on line " . 
                                                ($i+1) . '.';
                                                $correct = false;
                                          
                                            
                                        }
                                        
                                    }
                                  
                                    else
                                    {
                                        $result["status"] = "error";
                                        $result["message"] = $result["message"] .
                                        "\nError with time on line " . ($i+1) . '.';
                                        $correct = false;
                                    }
                                    
                                }

                                // False if the time does not fall between 10:00
                                // and 18:00
                                
                                elseif($time[0] == 2)
                                {
                                    $result["status"] = "error";
                                    $result["message"] = $result["message"] .
                                    "\nError with time on line " . ($i+1) . '.';
                                    $correct = false;
                                }

                                // If it has more than one conflict time, this loop
                                // will be entered. 
                                if ($j > 1)
                                {
                                                                                           
                                    $store_count++; 
                                    $store[$store_count][0] = 
										$store[$store_count-1][0];
                                    $store[$store_count][1] = $time;
                                }
                                
                                else{
                                    $store[$store_count][1] = $time;
                                }
                                
                            }

                            // False if the length of the string is not equal to 5
                            else
                            {
                                $result["status"] = "error";
                                $result["message"] = $result["message"] .
                                "\nError with time on line " . ($i+1) . '.';
                                $correct = false;
                            }
                            
							$m = 0;
							$t = 0;
							$w = 0;
							$r = 0;
							$f = 0;
							$s = 0;
							
                            // Setting the tables value days to 0.
                            $store[$store_count][2] = 0; 
                            $store[$store_count][3] = 0;
                            $store[$store_count][4] = 0;
                            $store[$store_count][5] = 0;
                            $store[$store_count][6] = 0;
                            $store[$store_count][7] = 0;                
                            

                                // Checking for correct days
                                for ( $day_count = 0; $day_count < count($days) &&
                                    count($days) <= 6 ; $day_count++)
                                {
                                    if ($days[$day_count] == 'M' || 
										$days[$day_count] == "T" ||
                                        $days[$day_count] == "W" ||
                                        $days[$day_count] == "R" ||
                                        $days[$day_count]== "F" ||
                                        $days[$day_count] == "S")
                                    {

                                        if ($days[$day_count] == "M"  && $m == 0)
                                        {
                                            $store[$store_count][2] = 1;
											$m = 1;
                                        }
                                        
                                        elseif ($days[$day_count] == "T"  && $t == 0)
                                        {
                                            $store[$store_count][3] = 1;
											$t = 1;
                                        }
                                        
                                        elseif ($days[$day_count] == "W"  && $w == 0)
                                        {
                                            $store[$store_count][4] = 1;
											$w = 1;
                                        }
                                        
                                        elseif ($days[$day_count] == "R"  && $r == 0)
                                        {
                                            $store[$store_count][5] = 1;
											$r = 1;
                                        }
                                        
                                        elseif ($days[$day_count] == "F"  && $f == 0)
                                        {
                                            $store[$store_count][6] = 1;
											$f = 1;
                                        }
                                        
                                        elseif ($days[$day_count] == "S"  && $s == 0)
                                        {
                                            $store[$store_count][7] = 1;
											$s = 1;
                                        }
										
										else 
										{
										    $result["status"] = "error";
											$result["message"] = $result["message"] .
											"\nDuplicate day entries on line " .
											($i + 1) . '.';
											$correct = false;
										}
                                    }
                                    
                                    // Not a correct day
                                    else
                                    {
                                        $result["status"] = "error";
                                        $result["message"] = $result["message"] .
                                        "\nError with days on line " . ($i+1) . '.';
                                        $correct = false;
                                    }
                                
                                
                                } 
                        }
                        
                        // False if missing a slash
                        else
                        {
                            
                            $result["status"] = "error";
                            $result["message"] = $result["message"] .
                            "\nMissing slash or colon on line " . ($i+1) . '.';
                            $correct = false;
							error_log("no slash");
                            break; 
                        }
                    }                
                }
            }
            
            else
            {
                $result["status"] = "error";
                $result["message"] = $result["message"] .
                "\nPlease delete empty line on line number " . ($i+1) . '.';
                $correct = false;
                $empty_line = true;
                //break;
            }
            error_log("before store count"); 
            if($empty_line == false)
                $store_count++;
        }

        error_log("Pass conflict scanne first for");
    
        $duplicate = false;
        
        for($i = 0; $i < count($store) && $duplicate == false 
        && count($store[$i]) == 8; $i++)
        {
            $temp = $store[$i];
                   
            for ($j = 0; $j < count($store) && $duplicate == false 
            && count($temp) == 8; $j++)
            {
            
                if ( $j != $i)
                {
                
                    if ($store[$j][0] == $temp[0] && $store[$j][1] == $temp[1] &&
                        $store[$j][2] == $temp[2] && $store[$j][3] == $temp[3] &&
                        $store[$j][4] == $temp[4] && $store[$j][5] == $temp[5] &&
                        $store[$j][6] == $temp[6] && $store[$j][7] == $temp[7])
                    {
                        $correct = FALSE;
                        $duplicate = true;
                        $result['status'] = "error";
                        $result['message'] =  $result['message']  .
                        "\nPlease remove duplicates" . '. ';
                    }
                    
                }
            
            }
            
        }
        
        

                
        if($correct == TRUE)
        {
        
            $result['status'] = "success";
			error_log("correct");
            // delete old records
            Conflict_Time::where_schedule_id($schedule_id)->delete();

            for($count = 0; $count < count($store); $count++)
            {
            
                //inserting new records into table
                $fields = array("schedule_id" => $schedule_id,
                                "course" => $store[$count][0],
                                "start_time" => $store[$count][1],
                                "monday" => $store[$count][2],
                                "tuesday" => $store[$count][3],
                                "wednesday" => $store[$count][4],
                                "thursday" => $store[$count][5],
                                "friday" => $store[$count][6],
                                "saturday" => $store[$count][7]);
                Conflict_Time::insert($fields);
            }

            $schedule = Schedule::find($schedule_id);
            $schedule->has_conflict_times = 1;
            $schedule->save();
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
    /*              text of the class time database entries.
    /* @input       $schedule_id -> the identification number of the schedule
    /*              currently being created. This value is used when inputing
    /*              to and extracting from the database.
    /* @output      $text -> A string of the information for an entry.
    /*************************************************************************/
    public static function get_text($schedule_id)
    {

        $entries = Conflict_Time::where_schedule_id($schedule_id)
        ->order_by("id", "asc")->get();
        $text = "";
        $prev_course = "";

        foreach ($entries as $entry)
        {
            $cur_course = $entry->course;
            $days_string = Conflict_Time::get_days_string($entry);
            if($cur_course == $prev_course)
            {
                $text .= " " . $days_string . "/" . 
                        substr($entry->start_time, 0, 5);
            }
            else
            {
                if($prev_course != "")
                {
                    $text .= "\n";
                }

                $text .= $entry->course . " " . $days_string . "/" . 
                            substr($entry->start_time, 0, 5);
                $prev_course = $cur_course;
            }

        }

        return $text;
    }
    //*************************************************************************
    //* End of get_text function
    //*************************************************************************
  
    /**************************************************************************
    /* @function    get_days_string
    /* @author      Atticus Wright
    /* @description This segment of code will
    /* @input       $entry ->
    /* @output      $result ->
    /*************************************************************************/
    public static function get_days_string($entry){

        $result = "";

        if($entry->monday)
        {
          $result .= "M";
        }

        if($entry->tuesday)
        {
          $result .= "T";
        }

        if($entry->wednesday)
        {
          $result .= "W";
        }

        if($entry->thursday)
        {
          $result .= "R";
        }

        if($entry->friday)
        {
          $result .= "F";
        }

        if($entry->saturday)
        {
          $result .= "S";
        }

        return $result;
    }
  
}