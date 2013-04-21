<?php

class Scheduler {

  public static function schedule_driver($schedule_id, )
  {

    $schedule = Schedule::find($schedule_id);
    $time_string = date('m-d-Y H:i:s');
    $name = $schedule->name . " " $schedule->year . " " . $time_string;

    $output_version = Output_Version::create(array())

  }


}