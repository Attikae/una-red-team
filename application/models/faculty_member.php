<?php

class Faculty_Member extends Eloquent {

  public static $table = 'faculty_members';

  public static $timestamps = true;

  public static function scan($schedule_id, $file_string){

    /* Return an array called $result with the indices status & message.
       Set $result['status'] equal to 'success' if everything goes as planned.
       Set $result['status'] equal to 'error' if there is an issue.
       If there is an issue, set result['message'] to a string containing the line number
       and description of the issue */

      // delete old records
      //Faculty_Member::where_schedule_id($schedule_id)->delete();

       // For testing purposes
  
  $file_stream = $file_string;
  $readSuccess = TRUE;
  
  //$lineArray = array();				//will hold the input separated by new lines
  $wordArray = array();					//will hold the lineArray entries separated by spaces
  $result = array("status" => "", "message" => "");
  
  $lineArray = mb_split('\n', $file_stream);
  
  for($count = 0; $count < count($lineArray); $count++)
  {
  	
      $wordArray[$count] = mb_split(' ', $lineArray);
	  
	  
	  
	  //________________________________________________________________
	  //Checking to make sure there are the right number of entries (5)
	  //________________________________________________________________
      if(count($wordArray[$count]) != 5)
	  {
		  $readSuccess = FALSE;
	      $result["status"] = "error";
		  $result["message"] = $result["message"] . "Incorrect amount of field arguments on 
                                    line: " . $count + 1 . "\n";
	  }
	  //________________________________________________________________
	  //End of checking for correct number of entries
	  //________________________________________________________________
	  
	  
	  
	  
	  //________________________________________________________________
	  //Checking for correct input for the last name as well as a
	  //comma at the end of the input
	  //________________________________________________________________
	  if(!mb_ereg_match('\A[A-Za-z]+,\z', $wordArray[$count][0]))
	  {
	      $readSuccess = FALSE;
		  $result["status"] = "error";
		  $result["message"] = $result["message"] . "Incorrect entry for last name or missing comma
		  							  on line: " . $count + 1 . "\n";
	  }
	  //________________________________________________________________
	  //End of check for last name and comma
	  //________________________________________________________________
	  
	  
	  
	  
	  //________________________________________________________________
	  //Checking for correct input for the first name
	  //________________________________________________________________
	  if(!ctype_alpha($wordArray[$count][1]))
	  {
	      $readSuccess = FALSE;
		  $result["status"] = "error";
		  $result["message"] = $result["message"] . "Incorrect entry for the first name on
		  							line: " . $count + 1 . "\n";
	  }
	  //________________________________________________________________
	  //End of check for first name
	  //________________________________________________________________
	  
	  
	  
	  
	  //_______________________________________________________________________________________
	  //Making sure the total number of characters for the name (including the comma and
	  //space) does not exceed 25. It also checks to make sure that either name is not blank.
	  //_______________________________________________________________________________________
	  if(strlen($wordArray[$count][0]) + strlen($wordArray[$count][1]) > 24
	  		|| strlen($wordArray[$count][0]) < 1 || strlen($wordArray[$count][1]) < 1)
	  {
	      $readSuccess = FALSE;
		  $result["status"] = "error";
		  $result["message"] = $result["message"] . "Incorrect length for the name on
		  							line: " . $count + 1 . "\n";
	  }
	  //________________________________________________________________________________________
	  //End of check for name lengths
	  //________________________________________________________________________________________
	  
	  
	  
	  
	  //____________________________________________________________________
	  //Checking to make sure the Years of Service input is within the
	  //correct range (0-60 inclusive)
	  //____________________________________________________________________
	  if($wordArray[$count][2] < 0 || $wordArray[$count][2] > 60)
	  {
	      $readSuccess = FALSE;
		  $result["status"] = "error";
		  $result["message"] = $result["message"] . "Incorrect entry for years of service on
		  							line: " . $count + 1 . "\n";
	  }
	  //____________________________________________________________________
	  //End of Years of Service check
	  //____________________________________________________________________
	  
	  
	  
	  $emailArray = array();					//temporary storage
	  $emailArray = $wordArray[$count][3];		//will hold the entire email
	  $part1 = " ";								//holds the beginning of the email
	  $part2 = " ";								//holds the end of the email (una.edu)
	  $tempCount1 = 0;
	  $tempCount2 = 0;


      //________________________________________________________________________________
      //Separating the email into two parts (part1 = user dependent; part2 = 'una.edu)
      //________________________________________________________________________________
	  while($tempCount1 < strlen($emailArray) && $emailArray[$tempCount1] != '@')
	  {
	      $part1[$tempCount1] = $emailArray[$tempCount1];
		  $tempCount1++;
	  }
	  
	  if($tempCount1 == strlen($emailArray))
	  {
	      $readSuccess = FALSE;
	      $result["status"] = "error";
		  $result["message"] = $result["message"] . "Incorrect email entry on
		  							  line: " . $count + 1 . "\n";
	  }
	  
	  $tempCount1++;
	  
	  while($tempCount1 < strlen($emailArray))
	  {
	      $part2[$tempCount2] = $emailArray[$tempCount1];
		  $tempCount1++;
		  $tempCount2++;
	  }
	  //___________________________________________________________
	  //End of separation
	  //___________________________________________________________
	  
	  
	  
	  
	  //___________________________________________________________
	  //Making sure the email is a valid university email
	  //___________________________________________________________
	  if($part2 != "una.edu")
	  {
	      $readSuccess = FALSE;
	      $result["status"] = "error";
		  $result["message"] = $result["message"] . "Invalid university email entered on
		  							  line: " . $count + 1 . "\n";
	  }
	  //___________________________________________________________
	  //End of validation
	  //___________________________________________________________
	  
	  //print strlen($emailArray);
	  //print "</br>";
	  //print ($emailArray);
	  //print "</br>";
	  //print $i;
	  //print "</br>";
	  
	  /*if($valid[0] != "u" && $valid[1] != "n" && $valid[2] != "a" && $valid[3] != "."
	  	  && $valid[4] != "e" && $valid[5] != "d" && $valid[6] != "u")
	  {
	      $readSucces = FALSE;
		  $result["status"] = "error";
		  $result["message"] = $result["message"] . "Not a valid university email on
		  						  line: " . $count + 1 . "\n";
	  }*/
	  
	  /*for($j = 0; $j < count($lineArray); $j++)
	  {
	      $emailArray = mb_split('@', $wordArray[0][3]);
		  
		  if(!mb_ereg_match('[a-z]', $emailArray[0][0]));
		  {
		  	  print $emailArray[0][0];
		      $readSuccess = FALSE;
			  $result["status"] = "error";
			  $result["message"] = "Incorrect e-mail entry 1.";
			  //$result["message"] = "Incorrect e-mail entry on
			  //						line: " . $count;
			  
			  break;
		  }
		  
		  if($emailArray[0][1] != "una.edu")
		  {
		      $readSuccess = FALSE;
			  $result["status"] = "error";
			  $result["message"] = "Incorrect e-mail entry 2.";
			  //$result["message"] = "Incorrect e-mail entry on
			  //						line: " . $count;
		  }
	  }*/
	  
	  //________________________________________________________________
	  //Checking to make sure the Hours to teach input is within the
	  //correct range (0-18 inclusive)
	  //________________________________________________________________
	  if($wordArray[$count][4] < 0 || $wordArray[$count][4] > 18)
	  {
	      $readSuccess = FALSE;
		  $result["status"] = "error";
		  $result["message"] = $result["message"] . "Incorrect input for number of hours to teach
		  							on line: " . $count + 1 . "\n";
	  }
	  //________________________________________________________________
	  //End of check for Hours to teach
	  //________________________________________________________________

  print $wordArray[$count][0];
  print "</br>";
  print $wordArray[$count][1];
  print "</br>";
  print $wordArray[$count][2];
  print "</br>";
  print $wordArray[$count][3];
  print "</br>";
  print $wordArray[$count][4];
  print "</br>";
	  
  }

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
  
}