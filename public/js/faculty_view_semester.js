$(document).ready(function(){

  // Hide all pages initially
  $("#versions-div").hide();
  $("#input-div").hide();
  $("#pref-div").hide();

  // Toggle pages
  $("#versions-btn").on("click",showVersions);
  $("#input-btn").on("click",showInput);
  $("#pref-btn").on("click",showPref);


  $(".version-lnk").on("click",function(){
    return false;
  });

  $("#pref-submit-btn").on("click", function(e){
    e.preventDefault();

    console.log("Before submit prefs calls");
    ajaxSubmitPrefs();
  });

  $("#pref-reset-btn").on("click", resetPrefs);


  ajaxRetrievePrefs();


});


/**
@method 
@param 
@author
*/
function showVersions(){

  // Display the versions div
  
  $(".container").hide();
  $("#versions-div").show();

}


/**
@method 
@param 
@author
*/
function showInput(){

  // Display the input div

  $(".container").hide();
  $("#input-div").show();
}


/**
@method 
@param 
@author
*/
function showPref(){

  // Display the preferences div

  $(".container").hide();
  $("#pref-div").show();
}

/**
@method ajaxSumbitPrefs
@param 
@author Atticus
@description Grabs the preferences submitted by a faculty user and
             passes the data to the server via ajax
*/
function ajaxSubmitPrefs(){

  var scheduleId = $('#faculty_schedule_id').val();

  var prefsData = [];
  prefsData[0] = ['test'];
  var i = 0;

  console.log("Before each");
  $("#course-prefs-table tbody tr").each(function(){
    var row = $(this);
    prefsData[i] = [];

    prefsData[i][0] = row.attr('id');

    if( row.find('.morning').is(':checked') )
    {
      prefsData[i][1] = 1;
    }
    else
    {
      prefsData[i][1] = 0;
    }

    if( row.find('.midday').is(':checked') )
    {
      prefsData[i][2] = 1;
    }
    else
    {
      prefsData[i][2] = 0;
    }

    if( row.find('.late-aft').is(':checked') )
    {
      prefsData[i][3] = 1;
    }
    else
    {
      prefsData[i][3] = 0;
    }

    prefsData[i][4] = row.find('.day-sections :selected').text();
    prefsData[i][5] = row.find('.night-sections :selected').text();
    prefsData[i][6] = row.find('.internet-sections :selected').text();
    
    i++;

  });

  console.log("Before ajax call!");

  $.ajax({
    url: "submit_prefs",
    type: "POST",
    data: {
        faculty_schedule_id : scheduleId,
        prefs_array : prefsData
    },
    success: function(data) {

      alert("Preferences submitted!");
      
    }

  }); 

}


/**
@method ajaxRetrivePrefs
@param 
@author Atticus
@description Retrieves a faculty user's submitted preferences from the server
             and displays them visually
*/
function ajaxRetrievePrefs(){

  var scheduleId = $('#faculty_schedule_id').val();


  $.ajax({
    url: "retrieve_prefs",
    type: "POST",
    dataType: "json",
    data: {
        faculty_schedule_id : scheduleId,
    },
    success: function(data) {

      prefs = data.prefsData;

      for (var i = 0; i < prefs.length; i++) {

        var row = $("#" + prefs[i].courseId);

        if(prefs[i].earlyMorning == "1")
        {
          row.find(".morning").prop("checked", true);
        }

        if(prefs[i].midDay == "1")
        {
          row.find(".midday").prop("checked", true);
        }

        if(prefs[i].lateAfternoon == "1")
        {
          row.find(".late-aft").prop("checked", true);
        }

        row.find(".day-sections").val(prefs[i].daySections);
        row.find(".night-sections").val(prefs[i].eveningSections);
        row.find(".internet-sections").val(prefs[i].internetSections);

      };
      
    }

  }); 

}



/**
@method resetPrefs
@param 
@author Atticus Wright
@description Visually resets a faculty user's preferences
*/
function resetPrefs(){

  $(".morning").prop("checked", false);
  $(".midday").prop("checked", false);
  $(".late-aft").prop("checked", false);
  $(".day-sections").val(0);
  $(".night-sections").val(0);
  $(".internet-sections").val(0);

}

