$(document).ready(function(){

  addYearOptions();
});

/**
@method 
@param 
*/
function addYearOptions()
{
  // Generate year options for the next 50 years

  for( i = 0; i < 50; i++ )
  {
    year_option = "<option>" + (2013+i) + "</option>";
    $("#select-year").append(year_option);
  }
}
