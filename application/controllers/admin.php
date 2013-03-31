<?php

class Admin_Controller extends Base_Controller
{

  public $restful = true;

	public function get_admin_index()
	{
    $query = Schedule::order_by('created_at', 'desc')->get();

		return View::make('admin.admin_index', array('schedules' => $query));
  }

  public function post_admin_index()
  {

    $schedule_id = Input::get('semester-select');

    if($schedule_id == "new")
    {
      return Redirect::to_action('admin@add_semester');
    }
    else if ($schedule_id == "default")
    {
      return Redirect::to_action('admin@admin_index');
    }
    else
    {
      return Redirect::to_action('admin@view_semester')->with('schedule_id', $schedule_id);
    }

  }

  public function get_view_faculty()
  {
    return View::make('admin.view_faculty');
  }

  public function get_semester_cp()
  {
    return View::make('admin.semester_cp');
  }


  public function get_add_semester()
  {
    return View::make('admin.add_semester');
  }

  public function get_view_semester()
  {

    if (Session::get('schedule_id')){
      $schedule = Schedule::find(Session::get('schedule_id'));

      $semester = $schedule->name . " " . $schedule->year;
    } 
    else{
      $semester = "No semester";
    }

    return View::make('admin.view_semester')->with('semester', $semester);
  }

}
