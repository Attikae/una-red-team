$(document).ready(function(){
  
  setTableHeader();

  $(".unlock").on("click", function(){
    ajaxUnlockUser($(this));
  });

  $(".delete").on("click", function(){
    ajaxDeleteUser($(this));
  });
});


function ajaxUnlockUser(span)
{

  var userId = span.parents('tr').attr('id');

  $.ajax({
    url: "unlock_user",
    type: "POST",
    data: {
        user_id : userId
    },
    success: function(data) {

      alert("User account unlocked!");
      
    }

  }); 


}


/**
@method ajaxDeleteUser
@param 
@author Atticus Wright
@description Sends the user to be deleted to the server and
             visually removes the user row from the page
*/
function ajaxDeleteUser(span){

  var userId = span.parents('tr').attr('id');

  // Display delete confirmation
  var r = confirm("Are you sure you want to delete this user?")

  if(r == true)
  {
    $.ajax({
      url: "delete_user",
      type: "POST",
      data: {
          user_id : userId
      },
      success: function(data) {

        span.parents('tr').remove();
        
      }

    }); 
  }

}



/**
@method 
@param 
@author
*/
function setTableHeader(){
  
  // Dynamically sets the table header size
  // based on the table's width

  $("#table-header").width( $("#faculty-table").width() );
};
