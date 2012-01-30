$(document).ready(function(){
	$('#show_result').click(function() {
		var data = $('#frmStat').serialize();
		processRequest(data);
		return false;
	});
	
	$('#copy_result').click(function() {
		var data = $('#frmStat').serialize();
		data += "&copy_result=1";
		processRequest(data);
		return false;
	});
	
	$('#stat').change(function() { 
		if ( $(this).val()== 'gotComments' ){
			var htmlStr = '<br/><label>Ignore self comments</label>'
						+'<input type="checkbox" name="selfComments" id="selfComments" value="1" /><br/>';
			$("#more_options").html(htmlStr);
		}else if( $(this).val()== 'totalStatus' ){
			var htmlStr = '<br/><label>Ignore links</label>'
						+'<input type="checkbox" name="ignoreLinks" id="ignoreLinks" value="1" /><br/>'
						+'<br/><label>Ignore photos</label>'
						+'<input type="checkbox" name="ignorePhotos" id="ignorePhotos" value="1" /><br/>'
						+'<br/><label>Include post with more than</label>'
						+'<input type="text" name="minLines" id="minLines" value="" class="small" /> lines<br/>'
						+'<br/><label>Include post with more than</label>'
						+'<input type="text" name="minWords" id="minWords" value="" class="small" /> words<br/>';
			$("#more_options").html(htmlStr);
		}else{
			$("#more_options").html("");
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
		return false;
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