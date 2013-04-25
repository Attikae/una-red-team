<?php

/*
 *  File: scheduler.php
 *  Authors: P. Clark, A. Wright
 */

class Scheduler {

  public static function schedule_driver($schedule_id )
  {

    $schedule = Schedule::find($schedule_id);
    $time_string = date('m-d-Y H:i:s');
    $name = $schedule->name . " " . $schedule->year . " " . $time_string;

    $output_version = Output_Version::create( array( 
                            "schedule_id" => $schedule_id,
                            "name" => $name ) );

    $course_list = Scheduler::get_course_list( $schedule_id );
    $faculty_list = Scheduler::get_faculty_list( $schedule_id, $course_list, 0 );
    $time_list = Scheduler::get_time_list( $schedule_id );

    Scheduler::create_scheduled_courses( $schedule_id,
                                         $output_version->id,
                                         $course_list,
                                         $faculty_list,
                                         0,
                                         $time_list );
/*
    $faculty_list = Scheduler::get_faculty_list( $schedule_id, $course_list, 1 );

    Scheduler::create_scheduled_courses( $schedule_id,
                                         $output_version->id,
                                         $course_list,
                                         $faculty_list,
                                         1,
                                         $time_list );
 */
  }

  ////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////

  public static function create_scheduled_courses( $schedule_id,
                                                   $output_id,
                                                   $course_list,
                                                   $faculty_list,
                                                   $faculty_priority,
                                                   $time_list )
  {
    $course_index = 0;

    foreach( $course_list as $course )
    {
      $section_list = Scheduler::get_section_list( $course->day_sections, 
                                                   $course->night_sections, 
                                                   $course->internet_sections );
      $daynight_section_num = 1;
      $internet_section_num = 1;

      foreach( $section_list as $section )
      {
        $scheduled = false;

        foreach( $faculty_list as $faculty )
        {
          // If faculty has enough hours to teach course
          if( $faculty->hours >= $course->credit_hours )
          {
            // Make sure faculty has day sections specified
            if( $section == 0 && $faculty->sections[$course_index][0] )
            {


















            }
            // Make sure faculty has night sections specified
            else if( $section == 1 && $faculty->sections[$course_index][1] )
            {
              // Find best time/room combo
              // Give section to faculty
              
              $time_id = 0;
              $room_id;
              $min_time = 100;

              foreach( $time_list as $time )
              {
                if( $time->start_offset >= 660 ) // 660 is the beginning offset for 6:00pm
                {
                  if( !$time->is_taken )
                  {
                    if( ( $course->room_type == $time->room_type ) || $time->room_type == 'B' )
                    {
                      if( $course->credit_hours == $time->credit_hours )
                      {
                        $time_id = $time->class_time_id;
                        $room_id = $time->room_id;
                        break;          
                      }
                      else if( $course->credit_hours <= $time->credit_hours )
                      {
                        if( $time->credit_hours < $min_time )
                        {
                          $min_time = $time->credit_hours;
                          $time_id = $time->class_time_id;
                          $room_id = $time->room_id;
                        }           
                      }
                      else
                      {
                      }
                    } 
                    else
                    {
                    }
                  }
                  else
                  {
                  }
                }
              }


              //error_log( $course->course );
              if( $time_id )
              {
                //error_log( "suitable time id = " . $time_id );

                //error_log( ( "101101" & "010010" ) == true );

                $tmp_faculty = Faculty_Member::where_id( $faculty->faculty_id )->first();
                $user_id = $tmp_faculty->user_id;
                $last_name = $tmp_faculty->last_name;
                $first_name = $tmp_faculty->first_name;
                $faculty_name = $last_name . ", " . $first_name;

                // Gather required information for scheduled_courses table
                
                $tmp_time_blob = new Time_Blob;

                foreach( $time_list as $time )
                {
                  if( $time->class_time_id == $time_id && $time->room_id == $room_id )
                  {
                    $tmp_time_blob = $time;
                    $time->is_taken = true;
                    break;
                  }
                }

                // Toggle all time/room combinations which overlap the current one
                foreach( $time_list as $time )
                {
                  if( $tmp_time_blob->room_id == $time->room_id )
                  {

                    if( strpos( $tmp_time_blob->days & $time->days, '1' ) ===  false )
                    {
                      // No overlap! Do nothing
                    }
                    else
                    {
                      // Overlap! Continue checking
                      // If the offset times intersect, toggle is_taken for time
                      if( ( $tmp_time_blob->start_offset >= $time->start_offset &&
                            $tmp_time_blob->start_offset <= $time->end_offset ) ||
                          ( $tmp_time_blob->end_offset >= $time->start_offset &&
                            $tmp_time_blob->end_offset <= $time->end_offset ) )
                      {
                        $time->is_taken = true;
                      }

                    }
                  }
                }
                
                // Calculate current section number
                $section_number;

                if( $daynight_section_num < 10 )
                {
                  $section_number = '0' . $daynight_section_num;
                }
                else
                {
                  $section_number = $daynight_section_num;
                }

                $daynight_section_num++;

                error_log( $tmp_time_blob->starting_time . " " . 
                           $tmp_time_blob->duration . " " . 
                           $tmp_time_blob->building . " " . 
                           $tmp_time_blob->room_number . 
                           $course->room_type );

                error_log( $tmp_time_blob->days );
                error_log( "//////////////" );

            
                Scheduled_Course::Create( array(
                  "output_version_id" => $output_id,
                  "priority_flag"     => $faculty_priority,
                  "user_id"           => $user_id,
                  "faculty_name"      => $faculty_name,
                  "course"            => $course->course,
                  "section_number"    => $section_number, 
                  "course_type"       => $course->room_type,
                  "start_time"        => $tmp_time_blob->starting_time,
                  "duration"          => $tmp_time_blob->duration,
                  "building"          => $tmp_time_blob->building,
                  "room_number"       => $tmp_time_blob->room_number,
                  "monday"            => $tmp_time_blob->days[0],
                  "tuesday"           => $tmp_time_blob->days[1],
                  "wednesday"         => $tmp_time_blob->days[2],
                  "thursday"          => $tmp_time_blob->days[3],
                  "friday"            => $tmp_time_blob->days[4],
                  "saturday"          => $tmp_time_blob->days[5]
                ) );

                // Decrement hours and sections
                $faculty->hours -= $course->credit_hours;
                $faculty->sections[$course_index][1] -= 1;

                $scheduled = true;
                break;
              }
              else
              {
                //error_log( "COULD NOT FIND SUITABLE TIME/ROOM COMBINATION" );
                break;
              }



            }
            // Make sure faculty has internet sections specified
            else if( $section == 2 && $faculty->sections[$course_index][2] )
            {

              // Give internet section to faculty
              // Decrement faculty hours
              $tmp_faculty = Faculty_Member::where_id( $faculty->faculty_id )->first();
              $user_id = $tmp_faculty->user_id;
              $last_name = $tmp_faculty->last_name;
              $first_name = $tmp_faculty->first_name;
              $faculty_name = $last_name . ", " . $first_name;

              $section_number;

              if( $internet_section_num < 10 )
              {
                $section_number = "I0" . $internet_section_num;
              }
              else
              {
                $section_number = "I0" . $internet_section_num;
              }
              $internet_section_num++;

              Scheduled_Course::create( array(
                  "output_version_id" => $output_id,
                  "priority_flag"     => $faculty_priority,
                  "user_id"           => $user_id,
                  "faculty_name"      => $faculty_name,
                  "course"            => $course->course,
                  "section_number"    => $section_number,
                  "course_type"       => $course->room_type,
              ) );
              
              // Decrement hours and sections
              $faculty->hours -= $course->credit_hours;
              $faculty->sections[$course_index][2] -= 1;

              // Make more elegant later
              // Break out of section loop if it has already been scheduled
              $scheduled = true;
              break;
            }
          }
        }
        // If failed to schedule course section
        if( $scheduled == false )
        {
          //error_log( "FAILED TO SCHEDULE COURSE " . $course->course );
        }
      }

      $course_index++;
    }
  }

  //////////////////////////////////////////////////////////////////////////////// 
  //////////////////////////////////////////////////////////////////////////////// 
  public static function get_faculty_list( $schedule_id, $course_list, $priority_bool )
  {
    // If priority_bool is 0, use seniority
    // If priority_bool is 1, use submission

    $faculty_list;

    if( $priority_bool == 0 )
    {
      $faculty = Faculty_Member::where_schedule_id($schedule_id)
                    ->order_by('years_of_service','desc')->get();
    }
    else
    {
      $faculty = Faculty_Member::where_schedule_id($schedule_id)
                    ->order_by('updated_prefs_at','desc')->get();
    }

    $i = 0;

    foreach( $faculty as $x )
    {
      $faculty_list[$i] = new Faculty_Blob;

      $faculty_list[$i]->faculty_id = $x->id;
      $faculty_list[$i]->hours = $x->hours;
      
      $j = 0;

      foreach( $course_list as $y )
      {
        $prefs = Faculty_Preference::where_user_id($x->user_id)
                     ->where_schedule_id($schedule_id)
                     ->where_course_id($y->id)->first();
        
        if( !is_null( $prefs )  )
        {
          $faculty_list[$i]->sections[$j][0] = $prefs->day_sections;
          $faculty_list[$i]->sections[$j][1] = $prefs->evening_sections;
          $faculty_list[$i]->sections[$j][2] = $prefs->internet_sections;

          $faculty_list[$i]->day_prefs[$j][0] = $prefs->early_morning;
          $faculty_list[$i]->day_prefs[$j][1] = $prefs->mid_day;
          $faculty_list[$i]->day_prefs[$j][2] = $prefs->late_afternoon;
        }
        else
        {
          // Handle setting everything to zero
          $faculty_list[$i]->sections[$j][0] = 0;
          $faculty_list[$i]->sections[$j][1] = 0;
          $faculty_list[$i]->sections[$j][2] = 0;

          $faculty_list[$i]->day_prefs[$j][0] = 0;
          $faculty_list[$i]->day_prefs[$j][1] = 0;
          $faculty_list[$i]->day_prefs[$j][2] = 0;
        }

        $j++;
      }

      $i++;
    }

    return $faculty_list;
  }

  ////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////

  public static function get_course_list( $schedule_id )
  {
    $courses = Course_To_Schedule::where_schedule_id($schedule_id)->get();

    $course_list;
    $i = 0;
    foreach( $courses as $x )
    {
      $course_list[$i][0] = $x;
      $course_list[$i][1] = filter_var( $x->course, FILTER_SANITIZE_NUMBER_INT);
      $i++;
    }

    $i = 0;
    foreach( $course_list as $x )
    {
      $sortCourse[$i] = $x[1];
      $i++;
    }

    array_multisort( $sortCourse, SORT_DESC, $course_list );

    $min_course_list;

    $i = 0;
    foreach( $course_list as $x )
    {
      $min_course_list[$i] = $x[0];
      $i++;
    }

    return $min_course_list;
  }

  ////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////

  public static function get_time_list( $schedule_id )
  {
    $class_times = Class_Time::where_schedule_id($schedule_id)->get();
    $avail_rooms = Available_Room::where_schedule_id($schedule_id)->get();

    $time_list;

    $i = 0;

    foreach( $class_times as $x )
    {

      // calculate start_offset, end_offset, and credit_hours

      $start_time = strtotime( $x->starting_time );
      
      $hour = date( "H", $start_time );
      $minute = date( "i", $start_time );

      $start_offset = ($hour-7)*60 + $minute;
      $end_offset = $start_offset + $x->duration;

      $num_days = $x->monday +
                  $x->tuesday +
                  $x->wednesday +
                  $x->thursday +
                  $x->friday +
                  $x->saturday;

      $credit_hours = intval(($x->duration*$num_days)/50);

      foreach( $avail_rooms as $y )
      {
        $time_list[$i] = new Time_Blob;

        $time_list[$i]->class_time_id = $x->id;
        $time_list[$i]->days = $x->monday .
                               $x->tuesday .
                               $x->wednesday .
                               $x->thursday .
                               $x->friday .
                               $x->saturday;
        $time_list[$i]->starting_time = $x->starting_time;
        $time_list[$i]->duration = $x->duration;
        $time_list[$i]->start_offset = $start_offset;
        $time_list[$i]->end_offset = $end_offset;
        $time_list[$i]->credit_hours = $credit_hours;

        $time_list[$i]->room_id = $y->id;
        $time_list[$i]->room_type = $y->type;
        $time_list[$i]->room_size = $y->size;
        $time_list[$i]->is_taken = false;
        $time_list[$i]->building = $y->building;
        $time_list[$i]->room_number = $y->room_number;

        $i++;
      }
    }
    return $time_list;
  }

  ////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////

  public static function get_section_list( $day_sections,
                                           $night_sections,
                                           $internet_sections )
  {
    $course_sections;

    $i = 0;

    for( $j = 0; $j < $day_sections; $j++ )
    {
      $course_sections[$i] = 0;
      $i++;  
    }

    for( $j = 0; $j < $night_sections; $j++ )
    {
      $course_sections[$i] = 1;
      $i++;
    }

    for( $j = 0; $j < $internet_sections; $j++ )
    {
      $course_sections[$i] = 2;
      $i++;
    }


    // Shuffle the array so that the sections we
    // try to schedule will be spread out
    shuffle( $course_sections ); // CHANGE TO EQUAL DISTRIBUTION

    return $course_sections;
  }

}
