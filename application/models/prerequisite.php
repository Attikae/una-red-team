<?php

class Prerequisite extends Eloquent {

  public static $table = 'prerequisites';

  public static $timestamps = true;

  public static function scan($schedule_id, $file_string){

    /* Return an array called $result with the indices status & message.
       Set $result['status'] equal to 'success' if everything goes as planned.
       Set $result['status'] equal to 'error' if there is an issue.
       If there is an issue, set result['message'] to a string containing the line number
       and description of the issue */

    // For testing purposes
    $file_stream = $file_string;
    $readSuccess = TRUE;
    
    mb_regex_encoding('UTF-8');
    mb_internal_encoding('UTF-8');
    
    $lineArray = array();
    $wordArray = array();
    $result = array("status" => "", "message" => "");
    
    $lineArray = mb_split('\n', $file_stream);
    
    for($count = 0; $count < count($lineArray); $count++)
    {
        $wordArray[$count] = mb_split(' ', $lineArray[$count]);
        
        if(count($wordArray[$count]) < 2)
        {
            $readSuccess = FALSE;
            $result["status"] = "error";
            $result["message"] = "Incorrect amount of field arguments on 
                                    line: " . $count;
            break;
        }
        
        for($wordCount = 0; $wordCount < count($lineArray[$count]); $wordCount++)
        {
            if(!mb_ereg_match('^[A-Z]{2,5}\d{3}[A-Z]{0,2}$', $wordArray[$count][$wordCount]))
            {
                $readSuccess = FALSE;
                $result["status"] = "error";
                $result["message"] = "Incorrect class field on line: " . $count;
                break;
            }
        }
    }
    
    if($readSuccess == TRUE)
    {
        for($lineCount = 0; $lineCount < count($lineArray); $lineCount++)
        {
            for($wordCount = 0; $wordCount < count($lineArray[$lineCount]); $wordCount++)
            {
                $new_prereq = new Prerequisite;
                $new_prereq->schedule_id = $schedule_id;
                
                $new_prereq->course = $wordArray[$lineCount][0];
                
                $new_prereq->prereq = $wordArray[$lineCount][$wordCount];
                
                $new_prereq->save();
            }
            
        }
        $result["status"] = "success";
    }

    return $result;

  }

    public static function get_text($schedule_id)
    {

        $entries = Prerequisite::where_schedule_id($schedule_id)->order_by("id", "asc")->get();
        $text = "";
        $prev_course = "";

        foreach ($entries as $entry)
        {
            $cur_course = $entry->course;
            if($cur_course == $prev_course)
            {
                $text .= " " . $entry->prereq;
            }
            else
            {
                if($prev_course != "")
                {
                    $text .= "\n";
                }

                $text .= $entry->course . " " . $entry->prereq;
                $prev_course = $cur_course;
            }

        }

        return $text;
    }
  
}