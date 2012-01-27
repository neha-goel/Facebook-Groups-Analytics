$(document).ready(function(){
	$('#show_result').click(function() {
		var data = "group="+$("#group").val()+"&from="+$("#from").val()+"&to="+$("#to").val()+"&stat="+$("#stat").val()+"&usersCount="+$("#usersCount").val();
		if ( $('#selfComments').is(':checked') ){
			data +="&selfComments="+$("#selfComments").val();
		}
		processRequest(data);
	});
	
	$('#stat').change(function() { 
		if ( $(this).val()== 'gotComments' ){
			$("#more_options").css("display", "block");
		}else{
			$("#more_options").css("display", "none");
		}
	});

	var dates = $('#from, #to').datepicker({
		defaultDate: "+1w",
		changeMonth: true,
		numberOfMonths: 2,
		dateFormat: 'dd M yy',
		onSelect: function( selectedDate ) {
			var option = this.id == "from" ? "minDate" : "maxDate",
			instance = $( this ).data( "datepicker" ),
			date = $.datepicker.parseDate(
				instance.settings.dateFormat ||
				$.datepicker._defaults.dateFormat,
				selectedDate, instance.settings );
			var date_limit = updateDateRange(date,option);		
			dates.not( this ).datepicker( "option", option, date_limit );
		}
	});
});

function updateDateRange(date_str, option) {
	var date = new Date(date_str);
	if( option == "minDate" ){
		date.setDate(date.getDate() + 1);
	}else if( option == "maxDate" ){
		date.setDate(date.getDate() - 1);
	}
	return date;
}

function processRequest(data){
	publishResult();
	if( $("#group").val() && $("#stat").val() ){
		$("#result").html("<strong>Loading.......</strong>");
		$.ajax({
			type : "POST",
			url : "ajax.php",
			data : data,
			success : function(html){
				$("#result").html("<script>publishResult();</script>"+html);
			}
		});
	}else{
		$("#result").html("<span class='error'>Please select group and statistic.</span>");
	}
}

function publishResult(){
	$('#publish_result').click(function() {
		var message = '';
		if( $("#title" ).val().length ){
			message = $("#title" ).val();
		}else{
			message = $("#auto_title" ).val();
		}
		message += $("#auto_message" ).val();
		message += $("#additional_message" ).val();
		var data = "publish=1&group="+$("#group").val()+"&message="+message;
		processPublishRequest(data);
	});
}

function processPublishRequest(data){
	$("#result").html("<strong>Loading.......</strong>");
	$.ajax({
		type : "POST",
		url : "ajax.php",
		data : data,
		success : function(html){
			$("#result").html(html);
		}
	});
}