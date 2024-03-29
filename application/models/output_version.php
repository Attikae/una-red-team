<?php

class Output_Version extends Eloquent {

  public static $table = 'output_versions';

  public static $timestamps = true;

  /**************************************************************************
  /* @function    create_classes_by_class_name
  /* @author      Atticus Wright
  /* @description This segment of code will generate the html for the classes
  /*              by class name view
  /* @input       $
  /* @output      $html containing the generated html
  /*************************************************************************/
  public static function create_classes_by_class_name($courses){

    // Sort courses by course name
    usort($courses, function($a, $b)
    {
        return strcmp($a->course, $b->course);
    });

    // Generate table header
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
      $html .= Output_Version::generate_row($course);
    } 

    $html .= "</tbody></table></div>";

    return $html;

  }





  /**************************************************************************
  /* @function    create_classes_by_room_tables
  /* @author      Atticus Wright
  /* @description This segment of code will generate html tables for the
  /*              classes by room view
  /* @input       $
  /* @output      $html containing the generated html
  /*************************************************************************/
  public static function create_classes_by_room_tables($output_version_id, $priority){
    $html = "";
    $html .= "<div class='by-room'>";

    $html_array = array();

    $rooms = Available_Room::where_output_version_id($output_version_id)->get();

    // Sort rooms by building and room number combination
    usort($rooms, function($a, $b)
    {
        return strcmp(($a->building . $a->room_number), ($b->building . $b->room_number));
    });

    // Generate a classes by room table for each room
    for($i = 0; $i < count($rooms); $i++)
    {

      $room_text = $rooms[$i]->building . " " . $rooms[$i]->room_number;
      $room_vertical_text = Output_Version::create_vertical_html($room_text);
      $room_id = $rooms[$i]->building . "-" . $rooms[$i]->room_number . "-" . $priority;

      $html_array[$i] = "<table id='" . $room_id . "' class='room-table'>" .
                        "<tr><td>Time</td><td></td><td><div class='time-row'>";

      $html_array[$i] .= "<div class='time-label7'>7</div>";

      // Generate the time numbers for the table
      for($j = 8; $j < 23; $j++)
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

      $html_array[$i] .= "<div class='time-label11'>11</div>";

      $html_array[$i] .= "</div></td></tr>" .
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


  /**************************************************************************
  /* @function    get_class_blocks_data
  /* @author      Atticus Wright
  /* @description This segment of code will generate an multi-dimensional
  /*              array containing data for courses that have been
  /*              scheduled
  /* @input       $
  /* @output      $data containing the generated array
  /*************************************************************************/
  public static function get_class_blocks_data($courses){

    $data = array();

    $i = 0;
    foreach ($courses as $course) {

      $course_type = substr($course->section_number, 0);

      // Make sure the course is not an internet course or
      // unscheduled course
      if($course_type != 'I' && $course_type != "X")
      {
        $start_formatted = "";
        $end_formatted = "";

        Output_Version::format_times($course->start_time, $course->duration,
                                    $start_formatted, $end_formatted);

        $width = intval( ($course->duration / 60) * 70);
        $left = Output_Version::get_left_offset($course->start_time, $course->duration);
        
        // Store data for the course
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


  /**************************************************************************
  /* @function    get_faculty_data
  /* @author      Atticus Wright
  /* @description This segment of code will generate a multi-dimensional
  /*              array containing data for the faculty members associated
  /*              with a schedule output version
  /* @input       $
  /* @output      $
  /*************************************************************************/
  public static function get_faculty_data($faculty_members){

    $data = array();
    $i = 0;

    // Store data associated with each faculty member
    foreach ($faculty_members as $faculty) {
      $data[$i]['id'] = $faculty->id;
      $data[$i]['userId'] = $faculty->user_id;
      $data[$i]['facultyName'] = $faculty->last_name . ", " . $faculty->first_name;
      $i++;
    }

    return $data;
  }


  /**************************************************************************
  /* @function    get_rooms_data
  /* @author      Atticus Wright
  /* @description This segment of code will generate a multi-dimensional
  /*              array containing data for the available rooms associated
  /*              with a schedule output version
  /* @input       $
  /* @output      $
  /*************************************************************************/
  public static function get_rooms_data($rooms){

    $data = array();
    $i = 0;

    // Store data associated with each room
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


  /**************************************************************************
  /* @function    create_classes_by_faculty
  /* @author      Atticus Wright
  /* @description This segment of code will generate the html for the classes
  /*              by faculty view
  /* @input       $
  /* @output      $html containing the generated html
  /*************************************************************************/
  public static function create_classes_by_faculty($courses){

    // Sort courses by faculty user id
    usort($courses, function($a, $b)
    {
        return strcmp($a->user_id, $b->user_id);
    });

    $html = "";
    $html .= "<div class='by-faculty'>";

    $prev_id = 0;

    // Make sure there are courses
    if(! empty($courses))
    {

      // Generate a course listing table for each faculty member
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

        $html .= Output_Version::generate_row($course);
      }
    }
    else
    {
      // Display this message message if no courses were scheduled
      $html .= "No courses scheduled";
    }


    $html .= "</tbody></table></div>";

    return $html;
  }



  /**************************************************************************
  /* @function    create_classes_by_time
  /* @author      Atticus Wright
  /* @description This segment of code will generate the html for the classes
  /*              by time view
  /* @input       $
  /* @output      $html containing the generated html
  /*************************************************************************/
  public static function create_classes_by_time($courses){

    // Sort courses by user id
    usort($courses, function($a, $b)
    {
        return strcmp($a->user_id, $b->user_id);
    });


    $html = "";
    $html .= "<div class='by-time'>";

    // Generate a table for each hour between 7 am and 6 pm
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

    // Display courses in a table if they fall anywhere within the time range
    $prev_id = 0;
    foreach ($courses as $course) {

      $start_offset = 0;
      $end_offset = 0;

      Scheduler::get_start_end_offsets($course->start_time, $course->duration,
                                        $start_offset, $end_offset);

      // 7 to 8 am
      if( ($start_offset >= 0 && $start_offset < 60) || 
          ($end_offset >= 0 && $end_offset < 60) ||
          (0 > $start_offset && 0 < $end_offset) )
      {
        $html_array[7] .= "" . Output_Version::generate_row($course);
      }

      // 8 to 9 am
      if( ($start_offset >= 60 && $start_offset < 120) || 
          ($end_offset >= 60 && $end_offset < 120) ||
          (60 > $start_offset && 60 < $end_offset) )
      {
        $html_array[8] .= "" . Output_Version::generate_row($course);
      }

      // 9 to 10 am
      if( ($start_offset >= 120 && $start_offset < 180) || 
          ($end_offset >= 120 && $end_offset < 180) ||
          (120 > $start_offset && 120 < $end_offset) )
      {
        $html_array[9] .= "" . Output_Version::generate_row($course);
      }

      // 10 to 11 am
      if( ($start_offset >= 180 && $start_offset < 240) || 
          ($end_offset >= 180 && $end_offset < 240) ||
          (180 > $start_offset && 180 < $end_offset) )
      {
        $html_array[10] .= "" . Output_Version::generate_row($course);
      }

      // 11 to 12 pm
      if( ($start_offset >= 240 && $start_offset < 300) || 
          ($end_offset >= 240 && $end_offset < 300) ||
          (240 > $start_offset && 240 < $end_offset) )
      {
        $html_array[11] .= "" . Output_Version::generate_row($course);
      }

      // 12 to 1 pm
      if( ($start_offset >= 300 && $start_offset < 360) || 
          ($end_offset >= 300 && $end_offset < 360) ||
          (300 > $start_offset && 300 < $end_offset) )
      {
        $html_array[12] .= "" . Output_Version::generate_row($course);
      }

      // 1 to 2 pm
      if( ($start_offset >= 360 && $start_offset < 420) || 
          ($end_offset >= 360 && $end_offset < 420) ||
          (360 > $start_offset && 360 < $end_offset) )
      {
        $html_array[13] .= "" . Output_Version::generate_row($course);
      }

      // 2 to 3 pm
      if( ($start_offset >= 420 && $start_offset < 480) || 
          ($end_offset >= 420 && $end_offset < 480) ||
          (420 > $start_offset && 420 < $end_offset) )
      {
        $html_array[14] .= "" . Output_Version::generate_row($course);
      }

      // 3 to 4 pm
      if( ($start_offset >= 480 && $start_offset < 540) || 
          ($end_offset >= 480 && $end_offset < 540) ||
          (480 > $start_offset && 480 < $end_offset) )
      {
        $html_array[15] .= "" . Output_Version::generate_row($course);
      }

      // 4 to 5 pm
      if( ($start_offset >= 540 && $start_offset < 600) || 
          ($end_offset >= 540 && $end_offset < 600) ||
          (540 > $start_offset && 540 < $end_offset) )
      {
        $html_array[16] .= "" . Output_Version::generate_row($course);
      }

      // 5 to 6 pm
      if( ($start_offset >= 600 && $start_offset < 660) || 
          ($end_offset >= 600 && $end_offset < 660) ||
          (600 > $start_offset && 600 < $end_offset) )
      {
        $html_array[17] .= "" . Output_Version::generate_row($course);
      }

      // 6 to 7 pm
      if( ($start_offset >= 660 && $start_offset < 720) || 
          ($end_offset >= 660 && $end_offset < 720) ||
          (660 > $start_offset && 660 < $end_offset) )
      {
        $html_array[18] .= "" . Output_Version::generate_row($course);
      }

    }

    // Add genarated html in $html_array to $html
    for($i = 7; $i < 19; $i++) {
      $html .= "" . $html_array[$i] . "</tbody></table></br></br></br></br>";
    }

    $html .= "</div>";

    return $html;
  }


  /**************************************************************************
  /* @function    create_not_scheduled
  /* @author      Atticus Wright
  /* @description This segment of code will generated the html for the
  /*              courses that could not be scheduled view
  /* @input       $
  /* @output      $html containing the generated html
  /*************************************************************************/
  public static function create_not_scheduled($courses)
  {

    // Sort courses by name
    usort($courses, function($a, $b)
    {
        return strcmp($a->course, $b->course);
    });

    // Display table header
    $html = "";
    $html .= "<div class='not-scheduled'>
                <table class='not-scheduled-table'>
                  <thead>
                    <tr>
                      <th>Course Name</th>
                      <th>Course Type</th>
                      <th>Credit Hours</th>
                      <th>Schedule</th>
                    </tr>
                  </thead>
                  <tbody>";
           

    // Generate a table row for each course
    foreach ($courses as $course) {

      if($course->section_number == "X")
        $html .= Output_Version::generate_not_scheduled_row($course);
      } 

    $html .= "</tbody></table></div>";

    return $html;


  }

  /**************************************************************************
  /* @function    generate_row
  /* @author      Atticus Wright
  /* @description This segment of code will generate the row html for a 
  /*              course
  /* @input       $
  /* @output      $html containing the generated html
  /*************************************************************************/
  public static function generate_row($course)
  {

    $start_formatted = "";
    $end_formatted = "";
    Output_Version::format_times($course->start_time, $course->duration,
                                $start_formatted, $end_formatted);

    // Generate building and room combination string
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

  /**************************************************************************
  /* @function    
  /* @author      Atticus Wright
  /* @description This segment of code will generated the row html for 
  /*              an unscheduled course
  /* @input       $
  /* @output      $
  /*************************************************************************/
  public static function generate_not_scheduled_row($course)
  {
    $html = "<tr>" .
              "<td class='not-scheduled-course-name'>" . $course->course . "</td>" .
              "<td>" . $course->course_type . "</td>" .
              "<td>" . $course->credit_hours . "</td>" .
              "<td><button id='" . $course->id . "' class='schedule-btn'>Schedule</button><td>" .
            "</tr>";
    return $html;

  }

  /**************************************************************************
  /* @function    
  /* @author      Atticus Wright
  /* @description This segment of code will generate formatted start and
  /*              and end times for a given start time and duration
  /* @input       $
  /* @output      $start_formatted and $end_formatted containing the
  /*              formatted time strings
  /*************************************************************************/
  public static function format_times($start_time, $duration,
                                     &$start_formatted, &$end_formatted)
  {

    // Format times to display hour, followed by colon,
    // followed by minutes, followed by am or pm
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

  /**************************************************************************
  /* @function    get_left_offset
  /* @author      Atticus Wright
  /* @description This segment of code will calculate the left offset for
  /*              for a class block div
  /* @input       $
  /* @output      $offset containing the offset
  /*************************************************************************/
  public static function get_left_offset($start_time, $duration)
  {
    // Figure out the left offset css value for a given start time
    // and duration of a course
    $timestamp = strtotime($start_time);
    $offset = ( (date('G', $timestamp) - 7) * 70 ) + 62;
    $offset = $offset + ( ( date('i', $timestamp) / 60 ) * 68 );
    return $offset;
  }


  /**************************************************************************
  /* @function    create_vertical_html
  /* @author      Atticus Wright
  /* @description This segment of code will generate the vertical html
  /*              for displaying the room name in the room grid view 
  /* @input       $
  /* @output      $vertical_text containing the vertical html
  /*************************************************************************/
  public static function create_vertical_html($room_text)
  {
    $vertical_text = "";
    for($i = 0; $i < strlen($room_text); $i++)
    {
      $vertical_text .= $room_text[$i] . "</br>"; 
    }

    return $vertical_text;
  }
}