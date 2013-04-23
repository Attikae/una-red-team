<?php

class Faculty_Member extends Eloquent {

    public static $table = 'faculty_members';

    public static $timestamps = true;

    /**************************************************************************
    /* @function    scan
    /* @author      Jason Smith
    /* @description This segment of code will scan an incoming file(the format
    /*              of which can be found in section A.6 of the specification
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

      // delete old records
      //Faculty_Member::where_schedule_id($schedule_id)->delete();

       // For testing purposes
  
        $file_stream = $file_string;
        $readSuccess = TRUE;
		
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

            // Check for correct number of arguments for each line (5)
            if(count($wordArray[$count]) != 5)
	        {
		        $readSuccess = FALSE;
	            $result["status"] = "error";
		        $result["message"] = $result["message"] .
		        "Incorrect amount of field arguments on line: " . ($count + 1) . 
		        "\n";
	        }
			
			else 
			{
			    // Check for correct last name and comma
	            if(!mb_ereg_match('\A[A-Za-z]+,\z', $wordArray[$count][0]))
	            {
	                $readSuccess = FALSE;
		            $result["status"] = "error";
		            $result["message"] = $result["message"] .
		            "Incorrect entry for last name or missing comma on line: " . ($count + 1) .
		            "\n";
	            }
	  
	            // Check for correct first name
	            if(!ctype_alpha($wordArray[$count][1]))
	            {
	                $readSuccess = FALSE;
		            $result["status"] = "error";
		            $result["message"] = $result["message"] .
		            "Incorrect entry for the first name on line: " . ($count + 1) .
		            "\n";
	            }
	  
	            // Checking the total number of characters for the name
	            // See section A.6 Constraints for more details
	            if((strlen($wordArray[$count][0]) + strlen($wordArray[$count][1])) > 24
	  		        || strlen($wordArray[$count][0]) < 1 || strlen($wordArray[$count][1]) < 1)
	            {
	                $readSuccess = FALSE;
		            $result["status"] = "error";
		            $result["message"] = $result["message"] .
		            "Incorrect length for the name on line: " . ($count + 1) .
		            "\n";
	            }
	  
	            // Check for Years of Service
	            if(!is_numeric($wordArray[$count][2]) || $wordArray[$count][2] < 0
	                || $wordArray[$count][2] > 60)
	            {
	                $readSuccess = FALSE;
		            $result["status"] = "error";
		            $result["message"] = $result["message"] .
		            "Incorrect entry for years of service on line: " . ($count + 1) .
		            "\n";
	            }
	  
	  
	            $emailArray = array();					    //temporary storage
	            $emailArray = $wordArray[$count][3];		//will hold the entire email
	            $part1 = " ";								//holds the beginning of the email
	            $part2 = " ";								//holds the end of the email (una.edu)
	            $tempCount1 = 0;
	            $tempCount2 = 0;

                // Separating the email into $part1 and $part2
	            while($tempCount1 < strlen($emailArray) && $emailArray[$tempCount1] != '@')
	            {
	                $part1[$tempCount1] = $emailArray[$tempCount1];
		            $tempCount1++;
	            }
	  
	            if($tempCount1 == strlen($emailArray))
	            {
	                $readSuccess = FALSE;
	                $result["status"] = "error";
		            $result["message"] = $result["message"] . "Incorrect email entry on line: " . ($count + 1) . "\n";
	            }
	  
	            $tempCount1++;
	  
	            while($tempCount1 < strlen($emailArray))
	            {
	                $part2[$tempCount2] = $emailArray[$tempCount1];
		            $tempCount1++;
		            $tempCount2++;
	            }
	  
	            // Validating the email
	            if($part2 != "una.edu")
	            {
	                $readSuccess = FALSE;
	                $result["status"] = "error";
		            $result["message"] = $result["message"] . "Invalid university email entered on line: " . ($count + 1) . "\n";
	            }

	            // Check for Hours to Teach
	            if(count($wordArray[$count]) < 5 || !is_numeric($wordArray[$count][4]) || $wordArray[$count][4] < 0
	                || $wordArray[$count][4] > 18)
	            {
	                $readSuccess = FALSE;
		            $result["status"] = "error";
		            $result["message"] = $result["message"] . "Incorrect input for number of hours to teach on line: " . ($count + 1) . "\n";
	            }
            }
        }

        // Input all entries into the database if there are no errors found
        if($readSuccess == TRUE)
        {
            for($count = 0; $count < count($wordArray); $count++)
            {
                $new_faculty = new Faculty_Member;
                $new_faculty->schedule_id = $schedule_id;
                $new_faculty->last_name = $wordArray[$count][0];
                $new_faculty->first_name = $wordArray[$count][1];
                $new_faculty->years_of_service = $wordArray[$count][2];
                //$new_faculty-> = $wordArray[$count][3];
                $new_faculty->hours = $wordArray[$count][4];
                $new_faculty->save();
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
    /*            to and extracting from into the database.
    /* @output      $text -> A string of the information for an entry.
    /*************************************************************************/
    public static function get_text($schedule_id)
    {

        $entries = Faculty_Member::where_schedule_id($schedule_id)->order_by("id", "asc")->get();
        $text = "";
        $first_entry = true;

        foreach ($entries as $entry)
        {
            $user = User::find($entry->user_id);

            if($first_entry != true){
                $text .= "\n";
            }

            $text .= $entry->last_name . ", " . $entry->first_name . " " . $entry->years_of_service . " "
               . $user->email . " " . $entry->hours . "\n";

            $first_entry = false;
        }

        return $text;
    }
    //*************************************************************************
    //* End of get_text function
    //*************************************************************************
}