<?php

class Faculty_Preference extends Eloquent {

    public static $table = 'faculty_preferences';

    public static $timestamps = true;

    /**************************************************************************
    /* @function    fill_prefs
    /* @author      Jordan Shook and Atticus Wright
    /* @description This segment of code will automatically fill the faculty
    /*              preferences for a schedule for testing purposes
    /* @input       $
    /* @output      $message containing the status of the function call
    /*************************************************************************/
    public static function fill_prefs($schedule_id)
    {
        Faculty_Preference::where_schedule_id($schedule_id)->delete();

        $faculty_members = Faculty_Member::where_schedule_id($schedule_id)->get();
        $courses = Course_To_Schedule::where_schedule_id($schedule_id)->get();

        if ( (!empty($faculty_members)) && (!empty($courses)) )
        {
            foreach ($faculty_members as $faculty_member) {
                
                foreach($courses as $course){
                   
                    //Create faculty preference entries
                    
                    $new_pref = new Faculty_Preference;          
                    $new_pref->schedule_id = $schedule_id;
                    $new_pref->user_id = $faculty_member->user_id;
                    $new_pref->course_id = $course->id;

                    if ($course->day_sections > 0)
                    {
                        $new_pref->early_morning = rand(0,1);
                        $new_pref->mid_day = rand(0,1);
                        $new_pref->late_afternoon = rand(0,1);
                    }
                    else
                    {
                        $new_pref->early_morning = 0;
                        $new_pref->mid_day = 0;
                        $new_pref->late_afternoon = 0;
                    }

                    if ($new_pref->early_morning == 1 || $new_pref->mid_day == 1 ||
                        $new_pref->late_afternoon == 1)
                    {
                        $new_pref->day_sections = rand(1, $course->day_sections);
                    }
                    else
                    {
                        $new_pref->day_sections = 0; 
                    }

                    if($course->night_sections > 0)
                    {
                        $new_pref->evening_sections = rand(0,$course->night_sections);  
                    }

                    if($course->internet_sections > 0)
                    {
                        $new_pref->internet_sections = rand(0,$course->internet_sections); 
                    }

                    $new_pref->save();
                
                } // end foreach course

                Faculty_Member::where_id($faculty_member->id)
                             ->update(array('updated_prefs_at' => DB::raw('NOW()')));
                            

            } // end foreach faculty member
            return array("message" => "Facutly prefs filled!");

        } // end if
        else
        {
            return array("message" => "Error: Must have faculty member and courses input to fill prefs!");
        }
    }

}