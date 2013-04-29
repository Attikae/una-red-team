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






  public static function create_classes_by_room_tables($output_version_id, $priority){
    $html = "";
    $html .= "<div class='by-room'>";

    $html_array = array();

    $rooms = Available_Room::where_output_version_id($output_version_id)->get();

    usort($rooms, function($a, $b)
    {
        return strcmp(($a->building . $a->room_number), ($b->building . $b->room_number));
    });

    for($i = 0; $i < count($rooms); $i++)
    {

      $room_text = $rooms[$i]->building . " " . $rooms[$i]->room_number;
      $room_vertical_text = Output_Version::createVerticalHtml($room_text);
      $room_id = $rooms[$i]->building . "-" . $rooms[$i]->room_number . "-" . $priority;

      $html_array[$i] = "<table id='" . $room_id . "' class='room-table'>" .
                        "<tr><td>Time</td><td></td><td><div class='time-row'>";

      $html_array[$i] .= "<div class='time-label7'>7</div>";

      for($j = 8; $j < 22; $j++)
      {
        if($j > 12)
        {
          $num = $j - 12;
        }
        else
        {
          $num = $j;
        }

        $html_array[$i] .= "<div class='time-label'>" . $num . "</div>";
      }

      $html_array[$i].= "</div></td></tr>" .
                        "<tr><td rowspan='8'>" . $room_vertical_text . "</td></tr>" .
                        "<tr><td>M</td><td><div class='monday-row day-row'></div></td></tr>" .
                        "<tr><td>T</td><td><div class='tuesday-row day-row'></div></td></tr>" .
                        "<tr><td>W</td><td><div class='wednesday-row day-row'></div></td></tr>" .
                        "<tr><td>R</td><td><div class='thursday-row day-row'></div></td></tr>" .
                        "<tr><td>F</td><td><div class='friday-row day-row'></div></td></tr>" .
                        "<tr><td>S</td><td><div class='saturday-row day-row'></div></td></tr>" .
                        "</table></br></br></br>";
    }

    for($i = 0; $i < count($html_array); $i++)
    {
      $html .= $html_array[$i];
    }

    $html .= "</div>";

    return $html;
  }


  public static function get_class_blocks_data($courses){

    $data = array();

    $i = 0;
    foreach ($courses as $course) {

      $course_type = substr($course->section_number, 0);

      if($course_type != 'I' && $course_type != "X")
      {
        $start_formatted = "";
        $end_formatted = "";

        Output_Version::formatTimes($course->start_time, $course->duration,
                                    $start_formatted, $end_formatted);

        $width = intval( ($course->duration / 60) * 68);
        $left = Output_Version::getLeftOffset($course->start_time, $course->duration);
        

        $data[$i]['id'] = $course->id;
        $data[$i]['priorityFlag'] = $course->priority_flag;
        $data[$i]['userId'] = $course->user_id;
        $data[$i]['course'] = $course->course;
        $data[$i]['sectionNumber'] = $course->section_number;
        $data[$i]['classSize'] = $course->class_size;
        $data[$i]['courseType'] = $course->course_type;
        $data[$i]['creditHours'] = $course->credit_hours;
        $data[$i]['startHour'] = substr($course->start_time, 0, 2);
        $data[$i]['startMinute'] = substr($course->start_time, 3, 2);
        $data[$i]['duration'] = $course->duration;
        $data[$i]['building'] = $course->building;
        $data[$i]['roomNumber'] = $course->room_number;
        $data[$i]['room'] = $course->building . " " . $course->room_number;
        $data[$i]['monday'] = $course->monday;
        $data[$i]['tuesday'] = $course->tuesday;
        $data[$i]['wednesday'] = $course->wednesday;
        $data[$i]['thursday'] = $course->thursday;
        $data[$i]['friday'] = $course->friday;
        $data[$i]['saturday'] = $course->saturday;
        $data[$i]['width'] = $width;
        $data[$i]['left'] = $left;
        $data[$i]['timeFormatted'] = $start_formatted . "</br> - " . $end_formatted;
        $data[$i]['facultyName'] = $course->faculty_name;
        $data[$i]['tableId'] = "#" . $course->building . "-" . $course->room_number . "-" . $course->priority_flag;
        $i++;

        } // end if course type
    }// end foreach course

    return $data;
  }


  public static function get_faculty_data($faculty_members){

    $data = array();
    $i = 0;

    foreach ($faculty_members as $faculty) {
      $data[$i]['id'] = $faculty->id;
      $data[$i]['userId'] = $faculty->user_id;
      $data[$i]['facultyName'] = $faculty->last_name . ", " . $faculty->first_name;
      $i++;
    }

    return $data;
  }


  public static function get_rooms_data($rooms){

    $data = array();
    $i = 0;

    foreach ($rooms as $room) {
      $data[$i]['id'] = $room->id;
      $data[$i]['type'] = $room->type;
      $data[$i]['size'] = $room->size;
      $data[$i]['building'] = $room->building;
      $data[$i]['roomNumber'] = $room->room_number;
      $data[$i]['room'] = $room->building . " " . $room->room_number;
      $i++;
    }

    return $data;

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

      if( ($start_offset >= 0 && $start_offset < 60) || 
          ($end_offset >= 0 && $end_offset < 60) ||
          (0 > $start_offset && 0 < $end_offset) )
      {
        $html_array[7] .= "" . Output_Version::generateRow($course);
      }

      if( ($start_offset >= 60 && $start_offset < 120) || 
          ($end_offset >= 60 && $end_offset < 120) ||
          (60 > $start_offset && 60 < $end_offset) )
      {
        $html_array[8] .= "" . Output_Version::generateRow($course);
      }

      if( ($start_offset >= 120 && $start_offset < 180) || 
          ($end_offset >= 120 && $end_offset < 180) ||
          (120 > $start_offset && 120 < $end_offset) )
      {
        $html_array[9] .= "" . Output_Version::generateRow($course);
      }

      if( ($start_offset >= 180 && $start_offset < 240) || 
          ($end_offset >= 180 && $end_offset < 240) ||
          (180 > $start_offset && 180 < $end_offset) )
      {
        $html_array[10] .= "" . Output_Version::generateRow($course);
      }

      if( ($start_offset >= 240 && $start_offset < 300) || 
          ($end_offset >= 240 && $end_offset < 300) ||
          (240 > $start_offset && 240 < $end_offset) )
      {
        $html_array[11] .= "" . Output_Version::generateRow($course);
      }

      if( ($start_offset >= 300 && $start_offset < 360) || 
          ($end_offset >= 300 && $end_offset < 360) ||
          (300 > $start_offset && 300 < $end_offset) )
      {
        $html_array[12] .= "" . Output_Version::generateRow($course);
      }

      if( ($start_offset >= 360 && $start_offset < 420) || 
          ($end_offset >= 360 && $end_offset < 420) ||
          (360 > $start_offset && 360 < $end_offset) )
      {
        $html_array[13] .= "" . Output_Version::generateRow($course);
      }

      if( ($start_offset >= 420 && $start_offset < 480) || 
          ($end_offset >= 420 && $end_offset < 480) ||
          (420 > $start_offset && 420 < $end_offset) )
      {
        $html_array[14] .= "" . Output_Version::generateRow($course);
      }

      if( ($start_offset >= 480 && $start_offset < 540) || 
          ($end_offset >= 480 && $end_offset < 540) ||
          (480 > $start_offset && 480 < $end_offset) )
      {
        $html_array[15] .= "" . Output_Version::generateRow($course);
      }

      if( ($start_offset >= 540 && $start_offset < 600) || 
          ($end_offset >= 540 && $end_offset < 600) ||
          (540 > $start_offset && 540 < $end_offset) )
      {
        $html_array[16] .= "" . Output_Version::generateRow($course);
      }

      if( ($start_offset >= 600 && $start_offset < 660) || 
          ($end_offset >= 600 && $end_offset < 660) ||
          (600 > $start_offset && 600 < $end_offset) )
      {
        $html_array[17] .= "" . Output_Version::generateRow($course);
      }

      if( ($start_offset >= 660 && $start_offset < 720) || 
          ($end_offset >= 660 && $end_offset < 720) ||
          (660 > $start_offset && 660 < $end_offset) )
      {
        $html_array[18] .= "" . Output_Version::generateRow($course);
      }

    }

    for($i = 7; $i < 19; $i++) {
      //$html_array[$i] .= "</tbdoy></table></br></br>";


      $html .= "" . $html_array[$i] . "</tbody></table></br></br>";
    }

    $html .= "</div>";

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

  public static function getLeftOffset($start_time, $duration)
  {
    $timestamp = strtotime($start_time);
    $offset = ( (date('G', $timestamp) - 7) * 70 ) + 62;
    $offset = $offset + ( ( date('i', $timestamp) / 60 ) * 68 );
    return $offset;
  }

  public static function createVerticalHtml($room_text)
  {
    $vertical_text = "";
    for($i = 0; $i < strlen($room_text); $i++)
    {
      $vertical_text .= $room_text[$i] . "</br>"; 
    }

    return $vertical_text;
  }
}