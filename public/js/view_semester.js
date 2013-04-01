$(document).ready(function(){

  // Hide all pages initially
  $("#versions-div").hide();
  $("#input-div").hide();
  $("#pref-div").hide();

  $(".file-div").hide();

  // Toggle pages
  $("#versions-btn").on("click",showVersions);
  $("#input-btn").on("click",showInput);
  $("#pref-btn").on("click",showPref);
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


