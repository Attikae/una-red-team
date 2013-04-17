<?php

class Faculty_Controller extends Base_Controller
{
    public $restful = true;

    public function get_faculty_index()
    {
        $query = Schedule::order_by('created_at', 'desc')->get();

        return View::make('faculty.faculty_index', array('schedules' => $query));
    }

    public function post_faculty_index()
    {
        $schedule_id = Input::get('semester-select');

        Session::put('schedule_id', $schedule_id);

        if($schedule_id == "default")
        {
            return Redirect::to_action('faculty@faculty_index');
        }
        else
        {
            return Redirect::to_action('faculty@faculty_view_semester');
        }
    }

    public function get_faculty_view_semester()
    {
        if (Session::has('schedule_id')){

            // Get schedule data
            $schedule_id = Session::get('schedule_id');
            $schedule = Schedule::find($schedule_id);
            $semester = $schedule->name . " " . $schedule->year;
            Session::put('semester', $semester);

            // Get schedule versions data
            $versions = Output_Version::where_schedule_id($schedule_id)->get();

        } 
        else{
            $semester = "No semester";
            $text['available_rooms'] = "";
            $text['class_times'] = "";
            $text['conflict_times'] = "";
            $text['courses_to_schedule'] = "";
            $text['faculty_members'] = "";
            $text['prerequisites'] = "";
        }

        return View::make('faculty.faculty_view_semester')
                      ->with('versions', $versions);
    }

}
