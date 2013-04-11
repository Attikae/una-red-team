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

        error_log("new");
        $correct = true;
        $lineArray = mb_split("\n", $file_string);      //will hold an array of strings separted by newlines
        $store = array();       //will be used to store database entries
        $result = array("status" => "", "message" => "");
        error_log ("here in 1"); 
         
        for ($i = 0; $i < count($lineArray) ; $i++)
        {
            //Grab each string separated by spaces
            
            $wordArray[$i] = mb_split (" ", $lineArray[$i]);
            error_log ("here in 2");
            
            for ($j = 0; $j < count($wordArray[$i]) ; $j++)
            {
                error_log("here in 77"); 
                if ( $j == 0)
                {
                    $store [$i][0] = $wordArray[$i][0];
                    error_log ("here in 3"); 
                    //________________________________________________________
                    // CHECKING FOR CORRECT COURSE NAME
                    //________________________________________________________
                    
                    if (!mb_ereg_match('^[A-Z]{2,5}\d{3}[A-Z]{0,2}$', $wordArray[$i][0]))
                    {    
                        error_log("here in 4");
                        $correct = false; 
                        $result['status'] = 'error';
                        $result['message'] = $result['message'] . '\nError with course name on line' . $i . '.';
                       
                    }
                }
                else{
                    $time = " ";                    //will hold the time for storage into database
                    $days = array();                    //will hold the days for storage into database
                    $tempword[0] = $wordArray[$i][$j]; //will hold day(s)/time combinations
                    $tempcount = 0;                 //counter
                    $count = 0;                     //counter
                    
                    while ($tempword[0][$tempcount] != "/" && $tempcount < strlen($tempword[0]) )
                    {
                        $days[$tempcount] = $tempword[0][$tempcount];    
                        $tempcount++;
                    }
         
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
                    //CHECKING FOR CORRECT TIME
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
                                        $result["message"] = $result["message"] . "\nError with time on line " . $i . '.';
                                        $correct = false;
                                    }
                                } 
                            } 
                            else{
                                $result["status"] = "error";
                                $result["message"] = $result["message"] . "\nError with time on line " . $i . '.';
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
                                        $result["message"] = $result["message"] . "\nError with time on line " . $i . '.';
                                        $correct = false;
                                    }

                                    elseif ( $time[1] == 8 && $time[3] == 3)
                                    {
                                        $result["status"] = "error";
                                        $result["message"] = $result["message"] . "\nError with time on line " . $i . '.';
                                        $correct = false;
                                    }
                                }  
                            }
                        }

                        // False if the time does not fall between 10:00 and 18:00

                        elseif($time[0] == 2){
                            $result["status"] = "error";
                            $result["message"] = $result["message"] . "\nError with time on line " . $i . '.';
                            $correct = false;
                        }  
                  
                        $store[$i][1] = $time;
                    }

                    // False if the length of the string is not equal to 5
                    else{
                        $result["status"] = "error";
                        $result["message"] = $result["message"] . "\nError with time on line " . $i . '.';
                        $correct = false;
                    }
                    //______________________________________________________________________
                    //   END OF CHECKING FOR CORRECT TIME
                    //_______________________________________________________________________
                    
                    $store[$i][2] = 0; 
                    $store[$i][3] = 0;
                    $store[$i][4] = 0;
                    $store[$i][5] = 0;
                    $store[$i][6] = 0;
                    $store[$i][7] = 0;                
                    
                    //______________________________________________________________________
                    //CHECKING FOR CORRECT DAYS
                    //_______________________________________________________________________
                        for ( $dayCount = 0; $dayCount < count($days) && count($days) <= 6 ; $dayCount++){
                            if ($days[$dayCount] == 'M' || $days[$dayCount] == "T" || $days[$dayCount] == "W" || $days[$dayCount] == "R" || $days[$dayCount]== "F" || $days[$dayCount] == "S"){

                                if ($days[$dayCount] == "M"){
                                    $store[$i][2] = 1;
                                }
                                elseif ($days[$dayCount] == "T"){
                                    $store[$i][3] = 1;
                                }
                                elseif ($days[$dayCount] == "W"){
                                    $store[$i][4] = 1;
                                }
                                elseif ($days[$dayCount] == "R"){
                                    $store[$i][5] = 1;
                                }
                                elseif ($days[$dayCount] == "F"){
                                    $store[$i][6] = 1;
                                }
                                elseif ($days[$dayCount] == "S"){
                                    $store[$i][7] = 1;
                                }
                            }
                            else{
                                $result["status"] = "error";
                                $result["message"] = $result["message"] . "\nError with days on line " . $i . '.';
                                $correct = false;
                            }
                        
                        
                        }          
                    }
            }
        }
                
        if($correct == TRUE)
        {
            $result['status'] = "success";
       
            for($count = 0; $count < count($store); $count++)
            {
                error_log("ISCORRECT");
                $new_conflictTime = new Conflict_Time;
                $new_conflictTime->schedule_id = $schedule_id;
                $new_conflictTime->course = $store[$count][0];
                $new_conflictTime->start_time = $store[$count][1];
                $new_conflictTime->monday = $store[$count][2];
                $new_conflictTime->tuesday = $store[$count][3];
                $new_conflictTime->wednesday = $store[$count][4];
                $new_conflictTime->thursday = $store[$count][5];
                $new_conflictTime->friday = $store[$count][6];
                $new_conflictTime->saturday = $store[$count][7];
                $new_conflictTime->save();
            } 
            
        }

        return $result;  
    }
  
}