<?php

class General
{

  /**************************************************************************
  /* @function    get_day_string
  /* @author      Atticus Wright
  /* @description This segment of code will take in an entry with boolean
  /*              value representing the days of the week and return a
  /*              string representation of those values
  /* @input       $entry with boolean day values
  /* @output      text string representation of days
  /*************************************************************************/
  public static function get_days_string($entry){

    $result = "";

    if($entry->monday)
    {
      $result .= "M";
    }

    if($entry->tuesday)
    {
      $result .= "T";
    }

    if($entry->wednesday)
    {
      $result .= "W";
    }

    if($entry->thursday)
    {
      $result .= "R";
    }

    if($entry->friday)
    {
      $result .= "F";
    }

    if($entry->saturday)
    {
      $result .= "S";
    }

    return $result;
  }
}