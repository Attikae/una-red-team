<?php

class Faculty_Controller extends Base_Controller
{
    public $restful = true;

    public function get_faculty_index()
    {
      $query = Schedule::order_by('created_at', 'desc')->get();

      return View::make('faculty.faculty_index', array('schedules' => $query));
    }



}
