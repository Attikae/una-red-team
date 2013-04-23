<?php

class Faculty_Preference extends Eloquent {

    public static $table = 'faculty_preferences';

    public static $timestamps = true;


    public static function fill_prefs($schedule_id)
    {

        $faculty_members = Faculty_Member::where_schedule_id($schedule_id);
        $courses = Course_To_Schedule::where_schedule_id($schedule_id);

        foreach ($faculty_members as $faculty_member) {
            
            foreach($courses as $course){
               
                //Create faculty preference entries
                
                $new_pref = new Faculty_Preference;          
                $new_pref->schedule_id = $schedule_id;
                $new_pref->faculty_id = $faculty_member->user_id;
                $new_pref->course_id = $course->id;
                $new_pref->early_morning = rand(0,1);
                $new_pref->mid_day = rand(0,1);
                $new_pref->late_afternoon = rand(0,1);
                
                if ($new_pref->early_morning == 1 || $new_pref->mid_day == 1 ||
                    $new_pref->late_afternoon == 1)
                {
                    error_log("in if"); 
                    $new_pref->day_sections = rand(1,3);
                }
                
                else
                {
                    error_log("in else");
                    $new_pref->day_sections = 0; 
                }
                
                $new_pref->evening_sections = rand(0,3);
                $new_pref->internet_sections = rand (0,3); 
                $new_pref->save();
            
            }


        }
    }

}