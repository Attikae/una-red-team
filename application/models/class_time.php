<?php

class Class_Time extends Eloquent {

    public static $table = 'class_times';

    public static $timestamps = true;
	
    /**************************************************************************
    /* @function    scan
    /* @author      Jason Smith
    /* @description This segment of code will scan an incoming file(the format
    /*              of which can be found in section A.1 of the specification
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
  	
        /***********************************************************************
        /* @function    is_correct_time
        /* @author      Jason Smith
        /* @description This segment of code will make sure that the given time
	    /*              follows the format hh:mm with the correct times given in
	    /*              in section A under General Input File Constraints.  
        /* @input       $time -> the given time taken from the particular place
	    /*              in the file.
        /* @output      Returns a boolean called $succes indicating whether the
	    /*              time was correct or not.
        ***********************************************************************/
        function is_correct_time($time)
        {
    	    $success = TRUE;
	  
            if(strlen($time) == 5)
	        {
	  	        if($time[2] != ":")
		        {
		            $success = FALSE;
		        }
				
				// Checking for examples such as: 09:00
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
		  
		        // Checking for examples such as: 10:00
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
        //**********************************************************************
        //* End of is_correct_time function
        //**********************************************************************

  
        $file_stream = $file_string;
        $read_success = TRUE;

        mb_regex_encoding('UTF-8');
        mb_internal_encoding('UTF-8');

        $line_array = array();
        $word_array = array();
        $result = array("status" => "", "message" => "");
		
        $store = array();
	    $time_array = array();

        // Separate each line of the file into an array
        $line_array = mb_split('\n', $file_stream);
  
        for($count = 0; $count < count($line_array); $count++)
        {
            // Separate each word of a line into a multi-dimensional array
            // $count -> line number
            $word_array[$count] = mb_split(" ", $line_array[$count]);

            // Check for correct number of arguments for each line (>1)
            if(count($word_array[$count]) < 2)
            {
                $read_success = FALSE;
                $result["status"] = "error";
                $result["message"] = $result["message"] . 
                "Incorrect amount of field arguments on line: " . 
                ($count + 1) . "\n";
            }

			else 
	        {
	        	
	            // Check for correct number of minutes
                if($word_array[$count][0] < 50 || $word_array[$count][0] > 300)
	            {
	                $read_success = FALSE;
	                $result["status"] = "error";
	                $result["message"] = $result["message"] .
	                "Incorrect entry for duration on line: " . ($count + 1) .
	                "\n";
	            }

	            $days = " ";
	            $time = " ";
	            $temp_word = array();
	            $temp_word = $word_array[$count][1];
	            $temp_count = 0;
	            $temp_count_2 = 0;
	  
	            // Separating days and times & checking for a '/' 
	            while($temp_count < strlen($temp_word) && 
                        $temp_word[$temp_count] != "/")
	            {
	                $days[$temp_count] = $temp_word[$temp_count];
		            $temp_count++;
	            }

	            if($temp_count == strlen($temp_word))
	            {
	                $read_success = FALSE;
		            $result["status"] = "error";
		            $result["message"] = $result["message"] .
		            "Missing a '/' on line: " . ($count + 1) .
		            "\n";
	            }
	  
	            $temp_count++;
	  
	            while($temp_count < strlen($temp_word))
	            {
	                $time[$temp_count_2] = $temp_word[$temp_count];
		            $temp_count++;
		            $temp_count_2++;
	            }  

                // Initializing $store[$count], letter indicators & 
                // $time_array[$count]
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
		
		        $time_array[$count] = "";
	  
	            // Checking for correct days 
	            if(!ctype_alpha($days))
	            {
	                $read_success = FALSE;
			        $result["status"] = "error";
			        $result["message"] = $result["message"] .
			        "Incorrect day entry on line: " . ($count + 1) .
			        "\n";
	            }
		
		        else
		        {
	                for($day_count = 0; $day_count < strlen($days) && 
                        strlen($days) <= 6; $day_count++)
	                {
		  
	                    if($days[$day_count] == "M")
		                {
		  	                if($m == 1)
		  	                {
		  	                    $read_success = FALSE;
				                $result["status"] = "error";
				                $result["message"] = $result["message"] .
				                "Incorrect day entry on line: " . ($count + 1) .
				                "\n";
		  	                }
			  
			                else 
			                {
			  	                $store[$count][0] = 1;
			                    $m = 1;  
			                }
		                }
		  
		                else if($days[$day_count] == "T")
		                {
		                    if($t == 1)
		                    {
		                        $read_success = FALSE;
				                $result["status"] = "error";
				                $result["message"] = $result["message"] .
				                "Incorrect day entry on line: " . ($count + 1) .
				                "\n";
		                    }
			  
			                else
			                {
			  	                $store[$count][1] = 1;
			                    $t = 1;  
			                }
		                }
		  
		                else if($days[$day_count] == "W")
		                {
		                    if($w == 1)
		                    {
		                        $read_success = FALSE;
				                $result["status"] = "error";
				                $result["message"] = $result["message"] .
				                "Incorrect day entry on line: " . ($count + 1) .
				                "\n";
		                    }
			  
			                else
			                {
			  	                $store[$count][2] = 1;
			                    $w = 1;  
			                }
		                }
		  
		                else if($days[$day_count] == "R")
		                {
		                    if($r == 1)
		                    {
		                        $read_success = FALSE;
				                $result["status"] = "error";
				                $result["message"] = $result["message"] .
				                "Incorrect day entry on line: " . ($count + 1) .
				                "\n";
		                    }
			  
			                else
			                {
			  	                $store[$count][3] = 1;
			                    $r = 1;  
			                }
		                }
		  
		                else if($days[$day_count] == "F")
		                {
		                    if($f == 1)
		                    {
		                        $read_success = FALSE;
				                $result["status"] = "error";
				                $result["message"] = $result["message"] .
				                "Incorrect day entry on line: " . ($count + 1) .
				                "\n";
		                    }
			  
			                else
			                {
			  	                $store[$count][4] = 1;
			                    $f = 1;  
			                }
		                }
		  
		                else if($days[$day_count] == "S")
		                {
		                    if($s == 1)
		                    {
		                        $read_success = FALSE;
				                $result["status"] = "error";
				                $result["message"] = $result["message"] .
				                "Incorrect day entry on line: " . ($count + 1) .
				                "\n";
		                    }
			  
			                else
			                {
			  	                $store[$count][5] = 1;
			                    $s = 1;  
			                }
		                }
		  
                        else
                        {
                            $read_success = FALSE;
			                $result["status"] = "error";
			                $result["message"] = $result["message"] .
			                "Incorrect day entry on line: " . ($count + 1) .
			                "\n";	
                        }
	                }
	            }
	  
	            // Checking for correct time
	            // This time was attached to the days before separation
	            $success = is_correct_time($time);
	  
	            if($success == FALSE)
	            {
	    	        $read_success = FALSE;
	                $result["status"] = "error";
		            $result["message"] = $result["message"] .
		            "Incorrect time entry on line: " . ($count + 1) .
		            "\n";
	            }
		
		        else 
		        {
	                $time_array[$count] = $time;
		        }
	  
	            // Checking for correct times
	            // These times were by themselves
	            for($i = 2; $i < count($word_array[$count]); $i++)
	            {
	                $success = is_correct_time($word_array[$count][$i]);

		            if($success == FALSE)
		            {
		    	        $read_success = FALSE;
		                $result["status"] = "error";
			            $result["message"] = $result["message"] .
			            "Incorrect time entry on line: " . ($count + 1) .
			            "\n";
		            }
	            }
            }
        }

        // Input all entries into the database if there are no errors found
        if($read_success == TRUE)
        {
        	// delete old records
            Class_Time::where_schedule_id($schedule_id)->delete();
			// For testing purposes
			
            for($count = 0; $count < count($line_array); $count++)
            {
                for($wordCount = 1; $wordCount < count($word_array[$count]); 
                    $wordCount++)
                {
                    $new_time = new Class_Time;
                    $new_time->schedule_id = $schedule_id;
                    $new_time->duration = $word_array[$count][0];
                
				    if($wordCount == 1)
				    {
				        $new_time->starting_time = $time_array[$count];
                        $new_time->monday = $store[$count][0];
                        $new_time->tuesday = $store[$count][1];
                        $new_time->wednesday = $store[$count][2];
                        $new_time->thursday = $store[$count][3];
                        $new_time->friday = $store[$count][4];
                        $new_time->saturday = $store[$count][5];
				    }
                
				    else
				    {
                        $new_time->starting_time = $word_array[$count][$wordCount];
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

        $entries = Class_Time::where_schedule_id($schedule_id)->order_by("id", 
                                "asc")->get();
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

                $text .= $entry->duration . " " . $cur_days_string . "/" . 
                substr($entry->starting_time, 0, 5);

                $prev_days_string = $cur_days_string;
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
    public static function get_days_string($entry)
    {

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
    //*************************************************************************
    //* End of get_days_string function
    //*************************************************************************	
}