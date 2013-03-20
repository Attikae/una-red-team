$(document).ready(function(){

	// HIDE THE ADD WINDOW HTML CODE
	$("<div class='add_window'></div>").appendTo("#schedules_div");
	$(".add_window").html( $("#add_window_html").html());
	$("#add_window_html").remove();
	$(".add_window").hide();
	
	$(".menu-btn").on("click", function(){
		$(".btn-active").removeClass("btn-active");
		$(this).addClass("btn-active");
	});
	
	$("#schedules_btn").on("click",function(){
		$("#faculty_div").css("visibility","hidden");
		$("#schedules_div").css("visibility","visible");
	});
	
	$("#faculty_btn").on("click",function(){
		$("#schedules_div").css("visibility","hidden");
		$("#faculty_div").css("visibility","visible");
	});
	///////////////////////////////////////////////////
	
	$("#add_btn").on("click", function(){
		$(".add_window").fadeIn(200);
	});
	
	$("#add_submit").on("click",function(){
		$(".add_window").hide();
	});
	
	$("#add_cancel").on("click",function(){
		$(".add_window").hide();
	});
	//////////////////////////////////////////////////////
	
	$(".sched-versions").hide();
	
	$("#s2013-btn").on("click",function(){
		$("#s2013-list:visible").slideUp();
		$("#s2013-list:hidden").slideDown();
	});
	
	$("#f2012-btn").on("click",function(){
		$("#f2012-list:visible").slideUp();
		$("#f2012-list:hidden").slideDown();
	});
	
});
