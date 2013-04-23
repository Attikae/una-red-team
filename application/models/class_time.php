<?php

class Class_Time extends Eloquent {

  public static $table = 'class_times';

  public static $timestamps = true; 

  public static function scan($schedule_id, $file_string){
  	
	
		    //_______________________________________________________________________
  // Name: isCorrectTime
  // Parameters: string containing t a time
  // Return Value(s): readSuccess; TRUE if time is correct
  //							   FALSE if it is not
  // Purpose: checks a time to make sure it follows the format hh:mm
  //_______________________________________________________________________
  
    function isCorrectTime($time)
    {
    	$success = TRUE;
	  
        if(strlen($time) == 5)
	    {
	  	    if($time[2] != ":")
		    {
		        $success = FALSE;
		    }
		  
		  //______________________
		  //ex: 09:00
		  //_____________________
		    if($time[0] == 0)
		    {
		        if($time[1] >= 7 && $time[1] <= 9)
		        {
		            if($time[3] != 0 && $time[3] != 3)
		            {
		                $success = FALSE;
		            }
				  
				    if($time[4] != 0)
				    {
				        $success = FALSE;
				    }
		        }
			  
			    else
			    {
			        $success = FALSE;
			    }
		    }
		  
		  //____________________________
		  //ex: 10:00
		  //____________________________
		  
		    else if($time[0] == 1)
		    {
		        if($time[1] >= 0 && $time[1] <= 8)
			    {
			        if($time[3] != 0 && $time[3] != 3)
				    {
				        $success = FALSE;
				    }
				  
				    if($time[4] != 0)
				    {
				        $success = FALSE;
				    }
				  
				    if($time[1] == 8 && $time[3] != 0)
				    {
				        $success = FALSE;
				    }
			    }
			  
                else
                {
                    $success = FALSE;
                }
		    }
		  
  		    else
  		    {
  		        $success = FALSE;
  		    }
	    }
	  
	    else
	    {
	        $success = FALSE;
	    }
	  
	    return $success;
    }

      /* Return an array called $result with the indices status & message.
       Set $result['status'] equal to 'success' if everything goes as planned.
       Set $result['status'] equal to 'error' if there is an issue.
       If there is an issue, set result['message'] to a string containing the line number
       and description of the issue */


       // delete old records
       //Class_Time::where_schedule_id($schedule_id)->delete();

       // For testing purposes
  
  
    $file_stream = $file_string;
    $readSuccess = TRUE;
    $lineArray = array();
    $wordArray = array();
    $result = array("status" => "", "message" => "");
    $store = array();
	$timeArray = array();
  
    $lineArray = mb_split('\n', $file_stream);
  
    for($count = 0; $count < count($lineArray); $count++)
    {

        $wordArray[$count] = mb_split(" ", $lineArray[$count]);

      //___________________________________________________________________
      //Checking to make sure there are at least two inputs
      //___________________________________________________________________
        if(count($wordArray[$count]) < 2)
        {
            $readSuccess = FALSE;
            $result["status"] = "error";
            $result["message"] = $result["message"] . "Incorrect amount of field arguments on line: " . ($count + 1) . "\n";
			
			break;
        }
	  //___________________________________________________________________
	  //End checking for at least two entries
	  //___________________________________________________________________
	  
	  
	  
	  
	  //___________________________________________________________________
	  //Checking for the correct number of minutes
	  //___________________________________________________________________
	  
        if($wordArray[$count][0] < 50 || $wordArray[$count][0] > 300)
	    {
	        $readSuccess = FALSE;
	        $result["status"] = "error";
	        $result["message"] = $result["message"] . "Incorrect entry for duration on line: " . ($count + 1) . "\n";
	    }
	  //____________________________________________________________________
	  //End of checking for correct number of minutes
	  //____________________________________________________________________
	  
	  
	  
	    $days = " ";
	    $time = " ";
	    $tempWord = array();
	    $tempWord = $wordArray[$count][1];
	    $tempCount = 0;
	    $tempCount2 = 0;
	  
	  
	  //_____________________________________________________________________
	  //Separating the days from the times and checking if there is a '/'
	  //_____________________________________________________________________  
	    while($tempCount < strlen($tempWord) && $tempWord[$tempCount] != "/")
	    {
	        $days[$tempCount] = $tempWord[$tempCount];
		    $tempCount++;
	    }

	    if($tempCount == strlen($tempWord))
	    {
	    	error_log("no /");
	        $readSuccess = FALSE;
		    $result["status"] = "error";
		    $result["message"] = $result["message"] . "Missing a '/' on line: " . ($count + 1) . "\n";
	    }
	  
	    $tempCount++;
	  
	    while($tempCount < strlen($tempWord))
	    {
	        $time[$tempCount2] = $tempWord[$tempCount];
		    $tempCount++;
		    $tempCount2++;
	    }  
	  //____________________________________________________________________
	  //End of separation
	  //____________________________________________________________________
	  

	    for($j = 0; $j < 8; $j++)
	    {
	        $store[$count][$j] = 0;
	    }
		
		$m = 0;
        $t = 0;
        $w = 0;
        $r = 0;
        $f = 0;
        $s = 0;
		
		$timeArray[$count] = "";
	  
	  //____________________________________________________________________
	  //Checking for correct days
	  //____________________________________________________________________  
	    if(!ctype_alpha($days))
	    {
	        $readSuccess = FALSE;
			$result["status"] = "error";
			$result["message"] = $result["message"] . "Incorrect day entry on line: " . ($count + 1) . "\n";
	    }
		
		else
		{
	        for($dayCount = 0; $dayCount < strlen($days) && strlen($days) <= 6; $dayCount++)
	        {
		  
	            if($days[$dayCount] == "M")
		        {
		  	        if($m == 1){
		  	            $readSuccess = FALSE;
				        $result["status"] = "error";
				        $result["message"] = $result["message"] . "Incorrect day entry on line: " . ($count + 1) . "\n";
		  	        }
			  
			        else 
			        {
			  	        $store[$count][0] = 1;
			            $m = 1;  
			        }
		        }
		  
		        else if($days[$dayCount] == "T")
		        {
		            if($t == 1){
		                $readSuccess = FALSE;
				        $result["status"] = "error";
				        $result["message"] = $result["message"] . "Incorrect day entry on line: " . ($count + 1) . "\n";
		            }
			  
			        else
			        {
			  	        $store[$count][1] = 1;
			            $t = 1;  
			        }
		        }
		  
		        else if($days[$dayCount] == "W")
		        {
		            if($w == 1){
		                $readSuccess = FALSE;
				        $result["status"] = "error";
				        $result["message"] = $result["message"] . "Incorrect day entry on line: " . ($count + 1) . "\n";
		            }
			  
			        else
			        {
			  	        $store[$count][2] = 1;
			            $w = 1;  
			        }
		        }
		  
		        else if($days[$dayCount] == "R")
		        {
		            if($r == 1){
		                $readSuccess = FALSE;
				        $result["status"] = "error";
				        $result["message"] = $result["message"] . "Incorrect day entry on line: " . ($count + 1) . "\n";
		            }
			  
			        else
			        {
			  	        $store[$count][3] = 1;
			            $r = 1;  
			        }
		        }
		  
		        else if($days[$dayCount] == "F")
		        {
		            if($f == 1){
		                $readSuccess = FALSE;
				        $result["status"] = "error";
				        $result["message"] = $result["message"] . "Incorrect day entry on line: " . ($count + 1) . "\n";
		            }
			  
			        else
			        {
			  	        $store[$count][4] = 1;
			            $f = 1;  
			        }
		        }
		  
		        else if($days[$dayCount] == "S")
		        {
		            if($s == 1){
		                $readSuccess = FALSE;
				        $result["status"] = "error";
				        $result["message"] = $result["message"] . "Incorrect day entry on line: " . ($count + 1) . "\n";
		            }
			  
			        else
			        {
			  	        $store[$count][5] = 1;
			            $s = 1;  
			        }
		        }
		  
                else
                {
                    $readSuccess = FALSE;
			        $result["status"] = "error";
			        $result["message"] = $result["message"] . "Incorrect day entry on line: " . ($count + 1) . "\n";	
                }
	        }
	    }  
	  //____________________________________________________________________
	  //End of checking for correct days
	  //____________________________________________________________________
	  
	  

	  
	  
	  //____________________________________________________________________
	  //Call to function isCorrectTime to check for the correct time that is
	  //attached to the days
	  //____________________________________________________________________ 
	    $success = isCorrectTime($time);
	  
	    if($success == FALSE)
	    {
	    	$readSuccess = FALSE;
	        $result["status"] = "error";
		    $result["message"] = $result["message"] . "Incorrect time entry on line: " . ($count + 1) . "\n";
	    }
		
		else 
		{
	        $timeArray[$count] = $time;
		} 
	  //_____________________________________________________________________
	  //End of checking for correct time that is attached to the days
	  //_____________________________________________________________________
	  
	  
	  
	  
	    for($i = 2; $i < count($wordArray[$count]); $i++)
	    {
	        $success = isCorrectTime($wordArray[$count][$i]);

		    if($success == FALSE)
		    {
		    	$readSuccess = FALSE;
		        $result["status"] = "error";
			    $result["message"] = $result["message"] . "Incorrect time entry on line: " . ($count + 1) . "\n";
		    }
	    }
    }


    if($readSuccess == TRUE)
    {
    	error_log("in success");
        for($count = 0; $count < count($lineArray); $count++)
        {
            for($wordCount = 1; $wordCount < count($wordArray[$count]); $wordCount++)
            {
                $new_time = new Class_Time;
                $new_time->schedule_id = $schedule_id;
                $new_time->duration = $wordArray[$count][0];
                
				if($wordCount == 1)
				{
				    $new_time->starting_time = $timeArray[$count];
                    $new_time->monday = $store[$count][0];
                    $new_time->tuesday = $store[$count][1];
                    $new_time->wednesday = $store[$count][2];
                    $new_time->thursday = $store[$count][3];
                    $new_time->friday = $store[$count][4];
                    $new_time->saturday = $store[$count][5];
				}
                
				else
				{
                    $new_time->starting_time = $wordArray[$count][$wordCount];
                    $new_time->monday = $store[$count][0];
                    $new_time->tuesday = $store[$count][1];
                    $new_time->wednesday = $store[$count][2];
                    $new_time->thursday = $store[$count][3];
                    $new_time->friday = $store[$count][4];
                    $new_time->saturday = $store[$count][5];
				}
                   
				$new_time->save();
            }
        } 
        $result["status"] = "success";
    }

  return $result;

  }
  




  public static function get_text($schedule_id)
  {

      $entries = Class_Time::where_schedule_id($schedule_id)->order_by("id", "asc")->get();
      $text = "";
      $prev_days_string = "";

      foreach ($entries as $entry)
      {
        $cur_days_string = Class_Time::get_days_string($entry);
        if($cur_days_string == $prev_days_string)
        {
          $text .= " " . substr($entry->starting_time, 0, 5);
        }
        else
        {
          if($prev_days_string != "")
          {
            $text .= "\n";
          }

          $text .= $entry->duration . " " . $cur_days_string . "/ " . substr($entry->starting_time, 0, 5);

          $prev_days_string = $cur_days_string;
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