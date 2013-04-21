$(document).ready(function(){

  // Hide all pages initially
  $("#versions-div").hide();
  $("#input-div").hide();
  $("#pref-div").hide();

  // Toggle pages
  $("#versions-btn").on("click",showVersions);
  $("#input-btn").on("click",showInput);
  $("#pref-btn").on("click",showPref);

  // Hide all file text fields
  $(".file-div").hide();

  // Toggle file divs
  $(".file-btn").on("click",toggleFileDiv);


  $(".version-lnk").on("click",function(){
    return false;
  });


  $(".delete-version-lnk").on("click",function(){
    return false;
  });

  $("#file-submit-iframe").load( ajaxFileUpload );

  $(".save-btn").on("click", function(e){
    e.preventDefault();

    ajaxSaveInput($(this));
  });

  $("#new-version-btn").on("click", ajaxCallScheduler);

  
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

function toggleFileDiv(){
  
  // Toggles the visibility of the
  // specified file div

  // Get ID of clicked button
  file_btn_id = $(this).attr("id");

  // Calculate ID of file div
  file_div_id = file_btn_id.substring(0,file_btn_id.length-3);
  file_div_id += "div";

  // If hidden, show
  if( $("#"+file_div_id).css("display") == "none" )
  {
    $("#"+file_div_id).show();
  }
  // If visible, hide
  else
  {
    $("#"+file_div_id).hide();
  }
}


function ajaxFileUpload(){

  var contents = $("#file-submit-iframe").contents().find('#file-contents').html();
  var inputType = $("#file-submit-iframe").contents().find('#input-type').html();

  if (contents == "invalid_file")
  {
    alert("Invalid file!");
  }
  else if (contents == "error") 
  {
    alert("Error uploading file!");
  }
  else
  {
  
    switch(inputType)
    {
      case "class-times" :
        $("#class-time-div textarea").val(contents);
        break;
      case "available-rooms" :
        $("#room-div textarea").val(contents);
        break;
      case "courses-to-schedule" :
        $("#course-div textarea").val(contents);
        break;
      case "conflict-times" :
        $("#conflict-div textarea").val(contents);
        break;
      case "prerequisites" :
        $("#prereq-div textarea").val(contents);
        break;
      case "faculty-members" :
        $("#faculty-div textarea").val(contents);
        break;
    }
  }
}


function ajaxSaveInput(saveButton) {

  var fileString = saveButton.parents().siblings('textarea').val();
  var fileId = saveButton.parents('.file-div').attr('id');
  var scheduleId = $('#schedule_id').val();
  
  var fileType;
  switch(fileId)
  {
    case "class-time-div" :
      fileType = "class_times";
      break;
    case "room-div" :
      fileType = "available_rooms";
      break;
    case "course-div" :
      fileType = "courses_to_schedule";
      break;
    case "conflict-div" :
      fileType = "conflict_times";
      break;
    case "prereq-div" :
      fileType = "prerequisites";
      break;
    case "faculty-div" :
      fileType = "faculty_members"
      break;
  } 

 $.ajax({
      url: "scan",
      dataType: "json",
      type: "POST",
      data: {
          file_type : fileType,
          file_string : fileString,
          schedule_id : scheduleId
      },
      success: function(data) {

        if ( data.status == "success" )
        {
          alert("Input data verified and stored!");
        }
        else if ( data.status == "error")
        {
          alert(data.message);
        }
        
      }

  }); 
}


function ajaxCallScheduler(element){

  var scheduleId = $('#schedule_id').val();

  $.ajax({
    url: "scheduler",
    dataType: "json",
    type: "POST",
    data: {
        schedule_id : scheduleId
    },
    success: function(data) {

      if ( data.status == "success" )
      {
        alert("Call to run scheduling algorithm worked!");
      }
      else if ( data.status == "error")
      {
        alert(data.message);
      }
      
    }

  }); 



}



