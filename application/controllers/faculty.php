<?php

class Faculty_Controller extends Base_Controller
{
    public function get_faculty_index()
    {
        return View::make('faculty.faculty_index');
    }
}
