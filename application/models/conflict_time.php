<?php

class Conflict_Time extends Eloquent {

    public static $table = 'conflict_times';

    public static $timestamps = true;

    public static function scan($schedule_id, $file_string){

    /* Return an array called $result with the indices status & message.
       Set $result['status'] equal to 'success' if everything goes as planned.
       Set $result['status'] equal to 'error' if there is an issue.
       If there is an issue, set result['message'] to a string containing the line number
       and description of the issue */

        $correct = true;
        $lineArray = mb_split("\n", $file_string);      //will hold an array of strings separted by newlines
        $store = array();                               //will be used to store database entries
        $result = array("status" => "", "message" => "");
        $storeCount = 0; 
         
        for ($i = 0; $i < count($lineArray) ; $i++)
        {
            //Grab each string separated by spaces
            
            $wordArray[$i] = mb_split (" ", $lineArray[$i]);
            
            for ($j = 0; $j < count($wordArray[$i]) ; $j++)
            {
            
                if ( $j == 0)
                {
                    $store [$storeCount][0] = $wordArray[$i][0];
                
                    //________________________________________________________
                    // CHECKING FOR CORRECT COURSE NAME
                    //________________________________________________________
                    
                    if (!mb_ereg_match('^[A-Z]{2,5}\d{3}[A-Z]{0,2}$', $wordArray[$i][0]))
                    {    
                     
                        $correct = false; 
                        $result['status'] = 'error';
                        $result['message'] = $result['message'] . "\nError with course name on line " . ($i+1) . '.';
                        break;
                       
                    }
                    
                }
                
                else
                {
                    $time = " ";                        //will hold the time for storage into database
                    $days = array();                    //will hold the days for storage into database
                    $tempword[0] = $wordArray[$i][$j];  //will hold day(s)/time combinations
                    $tempcount = 0;                     //counter
                    $count = 0;                         //counter
                    
                    
                    
                    //If the combination has a slash it will enter into this loop
                    if (!mb_ereg_match('^[^:]*$', $tempword[0]))
                    {
                                                              
                        while ($tempword[0][$tempcount] != "/" && $tempcount < strlen($tempword[0]) )
                        {
                            //gather all the days
                            
                            $days[$tempcount] = $tempword[0][$tempcount];    
                            $tempcount++; 
                        }
                        
                        //skip over the slash
                        
                        $tempcount++;
                        $count = 0; 
                        
                        while ($tempcount < strlen($tempword[0]))
                        {
                        
                            //Will separate the times from days 
                            
                            $time[$count] = $tempword[0][$tempcount];
                            $tempcount++;
                            $count++;
                        
                        }
                        
                        //_________________________________________________________________________
                        // CHECKING FOR CORRECT TIME
                        // ***Time has to be between 07:00 and 18:30
                        //_________________________________________________________________________
                        
                        if (strlen($time) == 5 )
                        {
                            //ex 09:00
                           
                            if ($time[0] == 0 )
                            {
                            
                                if ( $time[1] >= 7 && $time[1] <= 9)
                                {
                     
                                    if ( ($time[3] + $time[4]) == 0 || ($time[3] + $time[4]) == 3 )
                                    {
                                    
                                        if ($time[4] == 3 && $time[1] == 8) 
                                        {
                                            $result["status"] = "error";
                                            $result["message"] = $result["message"] . "\nError with time on line " . ($i+1) . '.';
                                            $correct = false;
                                        }
                                        
                                    }
                                    
                                }
                                                 
                                else
                                {
                                    $result["status"] = "error";
                                    $result["message"] = $result["message"] . "\nError with time on line " . ($i+1) . '.';
                                    $correct = false;
                                } 
                            }
                            
                            // ex: 10:00
                             
                            elseif ($time[0] == 1)
                            {    
                            
                                if ( $time[1] >= 0 && $time[1] <= 8)
                                {
                                
                                    if (($time[3] + $time[4]) == 0 || ($time[3] + $time[4]) == 3 )
                                    {
                                    
                                        if ($time[4] == 3 )
                                        {
                                            $result["status"] = "error";
                                            $result["message"] = $result["message"] . "\nError with time on line " . ($i+1) . '.';
                                            $correct = false;
                                        }

                                        elseif ( $time[1] == 8 && $time[3] == 3)
                                        {
                                            $result["status"] = "error";
                                            $result["message"] = $result["message"] . "\nError with time on line " . ($i+1) . '.';
                                            $correct = false;
                                        }
                                        
                                    }
                                    
                                }
                              
                                else
                                {
                                    $result["status"] = "error";
                                    $result["message"] = $result["message"] . "\nError with time on line " . ($i+1) . '.';
                                    $correct = false;
                                }
                                
                            }

                            // False if the time does not fall between 10:00 and 18:00

                            elseif($time[0] == 2)
                            {
                                $result["status"] = "error";
                                $result["message"] = $result["message"] . "\nError with time on line " . ($i+1) . '.';
                                $correct = false;
                            }

                            //If it has more than one conflict time, this loop
                            // will be entered.
                      
                            if ($j > 1)
                            {
                                                                                       
                                $storeCount++; 
                                $store[$storeCount][0] = $store[$storeCount-1][0];
                                $store[$storeCount][1] = $time;
                            }
                            
                            else{
                                $store[$storeCount][1] = $time;
                            }
                            
                        }

                        // False if the length of the string is not equal to 5
                        else
                        {
                            $result["status"] = "error";
                            $result["message"] = $result["message"] . "\nError with time on line " . ($i+1) . '.';
                            $correct = false;
                        }
                        //______________________________________________________________________
                        //   END OF CHECKING FOR CORRECT TIME
                        //_______________________________________________________________________
                        
                        
                        //setting the tables value days to 0.
                        
                        $store[$storeCount][2] = 0; 
                        $store[$storeCount][3] = 0;
                        $store[$storeCount][4] = 0;
                        $store[$storeCount][5] = 0;
                        $store[$storeCount][6] = 0;
                        $store[$storeCount][7] = 0;                
                        
                        //______________________________________________________________________
                        //CHECKING FOR CORRECT DAYS
                        //_______________________________________________________________________
                            for ( $dayCount = 0; $dayCount < count($days) && count($days) <= 6 ; $dayCount++)
                            {
                                if ($days[$dayCount] == 'M' || $days[$dayCount] == "T" || $days[$dayCount] == "W" || $days[$dayCount] == "R" 
                                || $days[$dayCount]== "F" || $days[$dayCount] == "S")
                                {

                                    if ($days[$dayCount] == "M")
                                    {
                                        $store[$storeCount][2] = 1;
                                    }
                                    
                                    elseif ($days[$dayCount] == "T")
                                    {
                                        $store[$storeCount][3] = 1;
                                    }
                                    
                                    elseif ($days[$dayCount] == "W")
                                    {
                                        $store[$storeCount][4] = 1;
                                    }
                                    
                                    elseif ($days[$dayCount] == "R")
                                    {
                                        $store[$storeCount][5] = 1;
                                    }
                                    
                                    elseif ($days[$dayCount] == "F")
                                    {
                                        $store[$storeCount][6] = 1;
                                    }
                                    
                                    elseif ($days[$dayCount] == "S")
                                    {
                                        $store[$storeCount][7] = 1;
                                    }
                                    
                                }
                                
                                //not a correct day
                                else
                                {
                                    $result["status"] = "error";
                                    $result["message"] = $result["message"] . "\nError with days on line " . ($i+1) . '.';
                                    $correct = false;
                                }
                            
                            
                            } 
                    }
                    
                    //False if missing a slash
                    else
                    {
                        $result["status"] = "error";
                        $result["message"] = $result["message"] . "\nMissing slash on line " . ($i+1) . '.';
                        $correct = false;
                        break; 
                    }
                }                
            }
            $storeCount++;
        }
    


                
        if($correct == TRUE)
        {
        
            $result['status'] = "success";

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
        }
        return $result;  
    }

    public static function get_text($schedule_id)
    {

        $entries = Conflict_Time::where_schedule_id($schedule_id)->order_by("id", "asc")->get();
        $text = "";
        $prev_course = "";

        foreach ($entries as $entry)
        {
            $cur_course = $entry->course;
            $days_string = Conflict_Time::get_days_string($entry);
            if($cur_course == $prev_course)
            {
                $text .= " " . $days_string . "/" . substr($entry->start_time, 0, 5);
            }
            else
            {
                if($prev_course != "")
                {
                    $text .= "\n";
                }

                $text .= $entry->course . " " . $days_string . "/" . substr($entry->start_time, 0, 5);
                $prev_course = $cur_course;
            }

        }

        return $text;
    }

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