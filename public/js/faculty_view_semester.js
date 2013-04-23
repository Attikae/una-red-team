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

});

function showVersions(){

  // Display the versions div
  
  $(".container").hide();
  $("#versions-div").show();

}

function showInput(){

  // Display the input div

  $(".container").hide();
  $("#input-div").show();
}

function showPref(){

  // Display the preferences div

  $(".container").hide();
  $("#pref-div").show();
}

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

      alert("Ajax call to submit_prefs worked!");
      
    }

  }); 

}
