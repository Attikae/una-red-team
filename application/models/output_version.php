<?php

class Output_Version extends Eloquent {

  public static $table = 'output_versions';

  public static $timestamps = true;


  public static function create_classes_by_class_name($courses){

    usort($courses, function($a, $b)
    {
        return strcmp($a->course, $b->course);
    });

    $html = "";
    $html .= "<div class='by-class-name'>
                <table class='class-name-table'>
                  <thead>
                    <tr>
                      <th>Course Name</th>
                      <th>Section #</th>
                      <th>Course Type</th>
                      <th>Days</th>
                      <th>Start Time</th>
                      <th>End Time</th>
                      <th>Room</th>
                      <th>Instructor</th>
                      <th>Credit Hours</th>
                    </tr>
                  </thead>
                  <tbody>";
                

    foreach ($courses as $course) {
      $html .= Output_Version::generateRow($course);
    } 

    $html .= "</tbody></table></div>";

    return $html;

  }

  public static function create_classes_by_room($courses){
    $html = "";
    $html .= "<div class='by-room'>By Room: Under Construction</div>";

    return $html;
  }

  public static function create_classes_by_faculty($courses){

    usort($courses, function($a, $b)
    {
        return strcmp($a->user_id, $b->user_id);
    });

    $html = "";
    $html .= "<div class='by-faculty'>";

    $prev_id = 0;
    foreach ($courses as $course) {

      if(isset($curr_id))
      {
        $prev_id = $curr_id;
      }
      $curr_id = $course->user_id;

      if($curr_id != $prev_id)
      {

        if($prev_id != 0)
        {
          $html .= "</tbody></table></br></br>";
        }

        $html .= "<table class='faculty-table'>
                    <caption>" . $course->faculty_name . "</caption>" .
                    "<thead>
                      <tr>
                        <th>Course Name</th>
                        <th>Section #</th>
                        <th>Course Type</th>
                        <th>Days</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Room</th>
                        <th>Instructor</th>
                        <th>Credit Hours</th>
                      </tr>
                    </thead>
                    <tbody>";
      }

      $html .= Output_Version::generateRow($course);


      
    }


    $html .= "</tbody></table></div>";

    error_log($html);

    return $html;
  }

  public static function create_classes_by_time($courses){

    usort($courses, function($a, $b)
    {
        return strcmp($a->user_id, $b->user_id);
    });


    $html = "";
    $html .= "<div class='by-time'>";

    $html_array = array();
    for($i = 7; $i < 19; $i++){

      if($i > 12)
      {
        $time = "" . ( $i - 12 ) . ":00 p.m.";
      }
      else
      {
        $time = "" . $i . ":00 a.m.";
      }

      $html_array[$i] = "<table class='time-table'>
                    <caption>" . $time . "</caption>" .
                    "<thead>
                      <tr>
                        <th>Course Name</th>
                        <th>Section #</th>
                        <th>Course Type</th>
                        <th>Days</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Room</th>
                        <th>Instructor</th>
                        <th>Credit Hours</th>
                      </tr>
                    </thead>
                    <tbody>";
    }

    $prev_id = 0;
    foreach ($courses as $course) {

      $start_offset = 0;
      $end_offset = 0;

      Scheduler::get_start_end_offsets($course->start_time, $course->duration,
                                        $start_offset, $end_offset);

      if( ($start_offset >= 60 && $start_offset < 120) || 
          ($end_offset >= 60 && $end_offset < 120) ||
          (60 > $start_offset && 60 < $end_offset) )
      {
        $html_array[7] .= "" . Output_Version::generateRow($course);
      }

      if( ($start_offset >= 120 && $start_offset < 180) || 
          ($end_offset >= 120 && $end_offset < 180) ||
          (120 > $start_offset && 120 < $end_offset) )
      {
        $html_array[8] .= "" . Output_Version::generateRow($course);
      }

      if( ($start_offset >= 180 && $start_offset < 240) || 
          ($end_offset >= 180 && $end_offset < 240) ||
          (180 > $start_offset && 180 < $end_offset) )
      {
        $html_array[9] .= "" . Output_Version::generateRow($course);
      }

      if( ($start_offset >= 240 && $start_offset < 300) || 
          ($end_offset >= 240 && $end_offset < 300) ||
          (240 > $start_offset && 240 < $end_offset) )
      {
        $html_array[10] .= "" . Output_Version::generateRow($course);
      }

      if( ($start_offset >= 300 && $start_offset < 360) || 
          ($end_offset >= 300 && $end_offset < 360) ||
          (300 > $start_offset && 300 < $end_offset) )
      {
        $html_array[11] .= "" . Output_Version::generateRow($course);
      }

      if( ($start_offset >= 360 && $start_offset < 420) || 
          ($end_offset >= 360 && $end_offset < 420) ||
          (360 > $start_offset && 360 < $end_offset) )
      {
        $html_array[12] .= "" . Output_Version::generateRow($course);
      }

      if( ($start_offset >= 420 && $start_offset < 480) || 
          ($end_offset >= 420 && $end_offset < 480) ||
          (420 > $start_offset && 420 < $end_offset) )
      {
        $html_array[13] .= "" . Output_Version::generateRow($course);
      }

      if( ($start_offset >= 480 && $start_offset < 540) || 
          ($end_offset >= 480 && $end_offset < 540) ||
          (480 > $start_offset && 480 < $end_offset) )
      {
        $html_array[14] .= "" . Output_Version::generateRow($course);
      }

      if( ($start_offset >= 540 && $start_offset < 600) || 
          ($end_offset >= 540 && $end_offset < 600) ||
          (540 > $start_offset && 540 < $end_offset) )
      {
        $html_array[15] .= "" . Output_Version::generateRow($course);
      }

      if( ($start_offset >= 600 && $start_offset < 660) || 
          ($end_offset >= 600 && $end_offset < 660) ||
          (600 > $start_offset && 600 < $end_offset) )
      {
        $html_array[16] .= "" . Output_Version::generateRow($course);
      }

      if( ($start_offset >= 660 && $start_offset < 720) || 
          ($end_offset >= 660 && $end_offset < 720) ||
          (660 > $start_offset && 660 < $end_offset) )
      {
        $html_array[17] .= "" . Output_Version::generateRow($course);
      }

      if( ($start_offset >= 720 && $start_offset < 800) || 
          ($end_offset >= 720 && $end_offset < 800) ||
          (720 > $start_offset && 720 < $end_offset) )
      {
        $html_array[18] .= "" . Output_Version::generateRow($course);
      }

    }

    for($i = 7; $i < 19; $i++) {
      //$html_array[$i] .= "</tbdoy></table></br></br>";


      $html .= "" . $html_array[$i] . "</tbody></table></br></br>";
    }



    $html .= "</div>";

    //error_log($html);

    return $html;
  }


  public static function generateRow($course)
  {

    $start_formatted = "";
    $end_formatted = "";
    Output_Version::formatTimes($course->start_time, $course->duration,
                                $start_formatted, $end_formatted);

    $room = "";
    if ($course->building != "")
    {
      $room = $course->building . " " . $course->room_number;
    }

    $html = "<tr>" .
              "<td>" . $course->course . "</td>" .
              "<td>" . $course->section_number . "</td>" .
              "<td>" . $course->course_type . "</td>" .
              "<td>" . General::get_days_string($course) . "</td>" .
              "<td>" . $start_formatted . "</td>" .
              "<td>" . $end_formatted . "</td>" .
              "<td>" . $room . "</td>" .
              "<td>" . $course->faculty_name . "</td>" .
              "<td>" . $course->credit_hours . "</td>" .
            "</tr>";
    return $html;
  }

  public static function formatTimes($start_time, $duration,
                                     &$start_formatted, &$end_formatted)
  {

    if($start_time == "00:00:00")
    {
      $start_formatted = "";
      $end_formatted = "";
    }
    else
    {
      $timestamp = strtotime($start_time);
      $start_formatted = date('g:i a', $timestamp);
      $timestamp += ($duration * 60);
      $end_formatted = date('g:i a', $timestamp);     
    }




  }
}