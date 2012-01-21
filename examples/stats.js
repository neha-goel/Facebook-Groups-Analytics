$(document).ready(function(){
	$('#show_result').click(function() {
		var data = "group="+$("#group").val()+"&stats="+$("#stats").val();
		processRequest(data);
	});
});

function processRequest(data){
	if( $("#group").val() & $("#stats").val().length ){
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
		$("#result").html("<span class='error'>Please select values.</span>");
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
		message += $("#automessage" ).val();
		message += $("#custom_message" ).val();
		var data = "publish=1&group="+$("#group").val()+"&stats="+$("#stats").val()+"&message="+message;
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