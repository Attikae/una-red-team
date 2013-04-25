<?php

class Output_Version extends Eloquent {

  public static $table = 'output_versions';

  public static $timestamps = true;


  public static function create_classes_by_class_name($courses){

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

      $html .= "<tr>" .
                  "<td>" . $course->course . "</td>" .
                  "<td>" . $course->section_number . "</td>" .
                  "<td>" . $course->course_type . "</td>" .
                  "<td>" . General::get_days_string($course) . "</td>" .
                  "<td>" . $course->start_time . "</td>" .
                  "<td>" . $course->end_time . "</td>" .
                  "<td>" . $course->building . " " . $course->room_number . "</td>" .
                  "<td>" . $course->faculty_name . "</td>" .
                  "<td>" . $course->credit_hours . "</td>" .
                "</tr>";

      
    } 

    $html .= "</tbody></table></div>";

    error_log("Html is: ");
    error_log($html);

    return $html;

  }

  public static function create_classes_by_room($courses){
    $html = "";
    $html .= "<div class='by-room'>By Room</div>";

    return $html;
  }

  public static function create_classes_by_faculty($courses){
    $html = "";
    $html .= "<div class='by-faculty'>By Faculty</div>";

    return $html;
  }

  public static function create_classes_by_time($courses){
    $html = "";
    $html .= "<div class='by-time'>By Time</div>";

    return $html;
  }

}