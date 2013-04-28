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


  $(document).on("click", ".delete-version-lnk", function(){
    var deleteId = $(this).parents('tr').find('.version-lnk').attr('id');

    ajaxDeleteVersion(deleteId);
    
  });

  $("#file-submit-iframe").load( ajaxFileUpload );

  $(".save-btn").on("click", function(e){
    e.preventDefault();

    ajaxSaveInput($(this));
  });

  $("#new-version-btn").on("click", ajaxCallScheduler);

  $("#fill-prefs").on("click", function(e){
    e.preventDefault();
    ajaxFillPrefs();
  });


  $(document).on('click', '.version-lnk', function(){
    ajaxDisplayOutput($(this));
  });

  $("#show-seniority").on('click', function(){
    var className = $(".output-container:visible").children(":visible").attr('class');
    console.log("Click show seniority classname is: " + className);
    $("#submission-container").hide();
    $("#seniority-container").show();
    $("#active-container-label").text("Viewing: By Seniority");
    $("#seniority-container").children(":visible").hide();
    $("#seniority-container").children("." + className).show();

  })

  $("#show-sumbission").on('click', function(){
    var className = $(".output-container:visible").children(":visible").attr('class');
    console.log("Click show submission classname is: " + className);
    $("#seniority-container").hide();
    $("#submission-container").show();
    $("#active-container-label").text("Viewing: By Preference Submissions");
    $("#submission-container").children(":visible").hide();
    $("#submission-container").children("." + className).show();
  })

  $("#hide-schedule-output").on('click', function(){
    $("#schedule-output-container").hide();
  })

  $("#bottom-buttons-container button").on('click', function(){
    var id = $(this).attr('id');

    console.log("id is: " + id);
    var container = $(".output-container:visible");

    switch(id)
    {
      case "show-by-room":
        $(container).children(":visible").hide();
        $(container).children('.by-room').show();
        break;
      case "show-by-class-name":
        $(container).children(":visible").hide();
        $(container).children('.by-class-name').show();
        break;
      case "show-by-faculty":
        $(container).children(":visible").hide();
        $(container).children('.by-faculty').show();
        break;
      case "show-by-time":
        $(container).children(":visible").hide();
        $(container).children('.by-time').show();
        break;
    }

  })

  $(document).on('click', '.class-block', function(){
    displayEditContainer($(this));
  })

  $("#edit-close").on("click", function(){
    $("#schedule-edit-container").hide();
    $("#schedule-container-overlay").hide();
    $(".day-checkbox").prop('checked', false);

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
        $("#versions-list").append(data.html);
        alert(data.message);

      }
      else if ( data.status == "error")
      {
        alert(data.message);
      }
      
    }

  }); 
}


function ajaxFillPrefs(){

  var scheduleId = $('#schedule_id').val();

  $.ajax({
    url: "fill_prefs",
    dataType: "json",
    type: "POST",
    data: {
        schedule_id : scheduleId
    },
    success: function(data) {

      alert(data.message);
      
    }

  }); 
}


function ajaxDisplayOutput(span){

  var scheduleId = $('#schedule_id').val();
  var outputVersionId = span.attr('id');

  $.ajax({
    url: "display_output",
    dataType: "json",
    type: "POST",
    data: {
        schedule_id : scheduleId,
        output_version_id : outputVersionId
    },
    success: function(data) {

      $("#seniority-container").html(data.seniority);
      $("#submission-container").html(data.submission);

      var blocks0 = data.classBlocks0;
      var blocks1 = data.classBlocks1;

      appendDivs(blocks1, 1);
      appendDivs(blocks0, 0);

      createFacultyOptions(data.faculty);
      createRoomOptions(data.rooms);
            
      $("#schedule-output-container").show();
      $("#submission-container").hide();
      $("#seniority-container").show();
      $("#active-container-label").text("Viewing: By Seniority");
      
    }
  });

}

function appendDivs(blocks, priority){

  var container;
  if(priority == 0)
  {
    container = $("#seniority-container");
  }
  else if(priority == 1)
  {
    container = $("#submission-container");
  }

  for (var i = 0; i < blocks.length; i++) {

    var table = container.find(blocks[i].tableId)

    if(blocks[i].monday == "1")
    {
      var div = createClassDiv(blocks[i]);
      table.find('.monday-row').append(div);
    }

    if(blocks[i].tuesday == "1")
    {
      var div = createClassDiv(blocks[i]);
      table.find('.tuesday-row').append(div);
    }

    if(blocks[i].wednesday == "1")
    {
      var div = createClassDiv(blocks[i]);
      table.find('.wednesday-row').append(div);
    }

    if(blocks[i].thursday == "1")
    {
      var div = createClassDiv(blocks[i]);
      table.find('.thursday-row').append(div);
    }

    if(blocks[i].friday == "1")
    {
      var div = createClassDiv(blocks[i]);
      table.find('.friday-row').append(div);
    }

    if(blocks[i].saturday == "1")
    {
      var div = createClassDiv(blocks[i]);
      table.find('.saturday-row').append(div);
    }
    setClassBlockData(blocks[i]);


  };


}

function createClassDiv(block){

  var div = document.createElement('div');
  div.id = block.id;
  div.className = "class-block" + " " + block.id;
  div.style.width = block.width + "px";
  div.style.left = block.left + "px";
  div.innerHTML = block.course + "</br>" + block.timeFormatted + 
                  "</br>" + block.facultyName;
  return div;
}

function setClassBlockData(block){

  var domDiv = $("." + block.id);

  domDiv.data("monday", block.monday);
  domDiv.data("tuesday", block.tuesday);
  domDiv.data("wednesday", block.wednesday);
  domDiv.data("thursday", block.thursday);
  domDiv.data("friday", block.friday);
  domDiv.data("saturday", block.saturday);
  domDiv.data("startHour", block.startHour);
  domDiv.data("startMinute", block.startMinute);
  domDiv.data("course", block.course);
  domDiv.data("room", block.room);
  domDiv.data("userId", block.userId);

}

function ajaxDeleteVersion(deleteId){

  $.ajax({
    url: "delete_version",
    type: "POST",
    data: {
        version_id : deleteId
    },
    success: function(data) {

      $("#" + deleteId).parents('tr').remove();
      
    }

  }); 

}

function displayEditContainer(div){

  var divData = div.data();
  $("#schedule-container-overlay").show()
  $("#schedule-edit-container").show();

  console.log("Start hour is: " + divData.startHour);
  console.log("Start hour is: " + div.data("startHour"));
  console.log("Start minute is: " + divData.startMinute);

  $("#course-label").text(divData.course);
  $("#start-hour-select").val(divData.startHour);
  $("#start-minute-select").val(divData.startMinute);
  $("#faculty-select").val(divData.userId);
  $("#room-select").val(divData.room);

  if(divData.monday == 1)
  {
    $(".monday-checkbox").prop('checked', true);
  }

  if(divData.tuesday == 1)
  {
    $(".tuesday-checkbox").prop('checked', true);
  }

  if(divData.wednesday == 1)
  {
    $(".wednesday-checkbox").prop('checked', true);
  }

  if(divData.thursday == 1)
  {
    $(".thursday-checkbox").prop('checked', true);
  }

  if(divData.friday == 1)
  {
    $(".friday-checkbox").prop('checked', true);
  }

  if(divData.saturday == 1)
  {
    $(".saturday-checkbox").prop('checked', true);
  }

}


function createFacultyOptions(faculty){

  console.log(faculty);
  var select = $("#faculty-select");
  select.empty();
  for (var i = 0; i < faculty.length; i++) {
    var option = "<option value='" + faculty[i].userId + "'>" + 
                  faculty[i].facultyName + "</option>";
    select.append(option);
  };

}

function createRoomOptions(rooms){
  console.log(rooms);
  var select = $("#room-select");
  select.empty();
  for (var i = 0; i < rooms.length; i++) {
    var option = "<option value='" + rooms[i].room + "'>" + 
                  rooms[i].room + "</option>";
    select.append(option);
  };

}



