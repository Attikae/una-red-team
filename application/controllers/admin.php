<?php

class Admin_Controller extends Base_Controller
{

  public $restful = true;

	public function get_index()
	{
		return View::make('admin.admin_index');
  }

  public function get_add_semester()
  {
    return View::make('admin.add_semester');
  }


}
