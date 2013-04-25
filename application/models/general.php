<?php

class General
{

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