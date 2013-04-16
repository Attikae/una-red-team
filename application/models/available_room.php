<?php

class Available_Room extends Eloquent {

    public static $table = 'available_rooms';

    public static $timestamps = true;

    public static function scan($schedule_id, $file_string){

        /* Return an array called $result with the indices status & message.
        Set $result['status'] equal to 'success' if everything goes as planned.
        Set $result['status'] equal to 'error' if there is an issue.
        If there is an issue, set result['message'] to a string containing the line number
        and description of the issue */
        
        $lineArray = mb_split("\n", $file_string);
        $success = true;
        $result = array("status" => "", "message" => "");
        
        for ($i = 0; $i < count($lineArray); $i++)
        {
            $wordArray[$i] = mb_split (" ", $lineArray[$i]);
             
            if($wordArray[$i][0] != 'C' && $wordArray[$i][0] != 'L' && $wordArray[$i][0] != 'B'){
            
                $success = false;  
                $result['status'] = "error";
                $result['message'] =   $result['message'] . "\nIncorrect type of room on line " . $i .  ". ";
  
            }
            
            if($wordArray[$i][1] > 100 || $wordArray[$i][1] < 1){
   
                $success = false;
                $result['status'] = "error";
                $result['message'] =   $result['message'] . "\nIncorrect size of room on line " . $i . ". ";
            }

            if(!mb_ereg_match('^[A-Z]+$', $wordArray[$i][2]) || strlen($wordArray[$i][2]) > 6 || strlen($wordArray[$i][2])< 2)
            {
            
                $success = false;
                $result['status'] = "error";
                $result['message'] =  $result['message']  . "\nIncorrect building name on line " . $i . '. '; 
            }

            if(!mb_ereg_match('^[0-9]+$', $wordArray[$i][3]) || strlen($wordArray[$i][3]) < 1 || strlen($wordArray[$i][3]) > 3)
            {
   
                $success = false;
                $result['status'] = "error";
                $result['message'] =  $result['message']  . "\nIncorrect room number on line " . $i . '. '; 
            
            }
     
        }
        
        if ($success == true)
        {
            $result['status'] = "success";

            // delete old records
            Available_Room::where_schedule_id($schedule_id)->delete();
            
            for($count = 0; $count < count($wordArray); $count++)
            {
                $new_room = new Available_Room;
                $new_room->schedule_id = $schedule_id;
                $new_room->type = $wordArray[$count][0];
                $new_room->size = $wordArray[$count][1];
                $new_room->building = $wordArray[$count][2];
                $new_room->room_number = $wordArray[$count][3];
                $new_room->save();
            } 
        }
        
        return $result;
        
    }

    public static function get_text($schedule_id)
    {

        $entries = Available_Room::where_schedule_id($schedule_id)->order_by("id", "asc")->get();
        $text = "";
        $first_entry = true;

        foreach ($entries as $entry)
        {
            if($first_entry != true)
            {
                $text .= "\n";
            }
            $text .= $entry->type . " " . $entry->size . " " . $entry->building . " "
                     . $entry->room_number;

            $first_entry = false;
        }

        return $text;
    }
  
}