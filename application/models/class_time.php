<?php

class Class_Time extends Eloquent {

  public static $table = 'class_times';

  public static $timestamps = true;

  public static function scan($schedule_id, $file_string){

      /* Return an array called $result with the indices status & message.
       Set $result['status'] equal to 'success' if everything goes as planned.
       Set $result['status'] equal to 'error' if there is an issue.
       If there is an issue, set result['message'] to a string containing the line number
       and description of the issue */

       // For testing purposes
       $result = array("status" => "error", "message" => "class time test");

       return $result;

  }
  
  
  //-------------------------------------------------
  // Name: isCorrectMinutes
  // parameters: token string
  // return value(s): bool ; true if correct, 
  // false otherwise
  //-------------------------------------------------
	  
  
  function isCorrectMinutes($s){
      $result["status"] = true;
      $minutes = "     ";
      $j = 0;

      if (strlen($s) > 3){
          $result["status"] = false;
          $result["message"] = "Minutes exceed maximum!";
      }

      for ($i = 0; $i < strlen($s); $i++){
          $minutes[$j] = $s[$i];
          $j++;
      }
	
      if ($minutes < 50 || $minutes > 300){
          $result["status"] = false;
          $result["message"] = "Minutes are below the minimum or are greater than the maximum!";
      }
    
      return $result;
  }

  //-------------------------------------------------
  // Name: isCorrectDay
  // parameters: token string
  // return value(s): bool ; true if correct, 
  // false otherwise
  //-------------------------------------------------

  function isCorrectDay($s){
      $correct = true;

      for ($i = 0; $i < strlen($s) && $i <= 7; $i++){
	      if ($i > 6 || $s[$i] != "M" && $s[$i] != "T" && $s[$i] != "W" && $s[$i] != "R" && $s[$i]	!= "F" && $s[$i] != "S" && $s[$i] != "/"){
	          $correct = false;
			  print "The days do not follow specifications!";
          }
		
		  for ($j = 0; $j < $i; $j++){
		      if ($s[$j] == $s[$i]){
		          $correct = false;
				  $j = $i;
                  print "A day has been inserted twice!";
              }
		  }
	    
	      if ($s[$i] == "/"){
	          isCorrectTime1($s, $i);
              $i = 8;
          }
      }

      return $correct;
  }

  //-------------------------------------------------
  // Name: isCorrectTime1
  // parameters: token string, length in string
  // return value(s): bool ; true if correct, 
  // false otherwise
  //-------------------------------------------------

  function isCorrectTime1($s, $i){
      $correct = true;

      if (strlen($s) == $i + 5){
          if ($s[$i] == 0 ){
        
              if ( $s[$i + 1] >= 7 && $s[$i + 1] <= 9){

                  if ( ($s[$i + 3] + $s[$i + 4]) == 0 || ($s[$i + 3] + $s[$i + 4]) == 3 ){
                      if ($s[$i + 4] == 3 || $s[$i + 1] == 8){
                          $correct = false;
                      }
                  } 
              } 
              else{
                  $correct = false;
              }
          }
         
          if ($s[$i] == 1){
        
              if ( $s[$i + 1] >= 0 && $s[$i + 1] <= 8){
                  if (($s[$i + 3] + $s[$i + 4]) == 0 || ($s[$i + 3] + $s[$i + 4]) == 3 ){
                      if ($s[$i + 4] == 3 || $s[$i + 1] == 8){
                          $correct = false;
                      } 
                  }  
              } 
              else{
                  $correct = false;
              }  
          }
      }

      else{
          $correct = false;
      }
    
      return $correct;
  }

  //-------------------------------------------------
  // Name: isCorrectTime
  // parameters: token string
  // return value(s): bool ; true if correct, 
  // false otherwise
  //-------------------------------------------------

  function isCorrectTime($s){
      $correct = true;

      if((!is_numeric($s[0])) && (!is_numeric($s[1])) && (!is_numeric($s[3])) && (!is_numeric($s[4])) && ($s[2] != ":")){
    	  $correct = false;
		  print "wrong format";
      }
	
	  if(strlen($s) != 5){
	      $correct = false;
		  print "2";
      }
	
	  else{
		
		  if($s[0] == 0){
			
		      if($s[1] >=7 && $s[1] <= 9){
			    
			      if($s[3] != 0 && $s[3] != 3){
			    	  $correct = false;
					  print "3";
			      }
				
				  if($s[4] != 0){
					  $correct = false;
					  print "4";
                  }
              }
			
			  else{
				  $correct = false;
			  }
		  }
		
		  else if($s[0] == 1){
			
			  if($s[1] >= 0 && $s[1] <= 9){
				
				  if($s[3] != 0 && $s[3] != 3){
			    	  $correct = false;
					  print "3";
			      }
				
				  if($s[4] != 0){
					  $correct = false;
					  print "4";
                  }
			  }
			
			  else{
				  $correct = false;
			  }
		  }
		
		  else if($s[0] == 2){
			
			  if($s[1] >= 0 && $s[1] <= 4){
				
				  if($s[3] != 0 && $s[3] != 3){
			    	  $correct = false;
					  print "3";
			      }
				
				  if($s[4] != 0){
					  $correct = false;
					  print "4";
                  }
			  }
			
			  else{
				  $correct = false;
			  }
		  }
		
		  else{
			  $correct = false;
		  }
	  }
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