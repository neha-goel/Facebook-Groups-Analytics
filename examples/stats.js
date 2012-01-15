$(document).ready(function(){
	$('#show_result').click(function() {
		var data = "group="+$("#group").val()+"&from="+$("#from").val()+"&to="+$("#to").val();
		processRequest(data);
	});
	
	$('#show_result_publish').click(function() {
		var data = "publish=1&group="+$("#group").val()+"&from="+$("#from").val()+"&to="+$("#to").val();
		processRequest(data);
	});
	
	function processRequest(data){
		if( $("#group").val() ){
			$("#result").html("<strong>Loading.......</strong>");
			$.ajax({
				type : "POST",
				url : "ajax.php",
				data : data,
				success : function(html){
					$("#result").html(html);
				}
			});
		}else{
			$("#result").html("<span class='error'>Please select group.</span>");
		}
	}	
});