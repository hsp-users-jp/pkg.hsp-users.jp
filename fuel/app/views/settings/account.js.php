$(document).ready(function(){

	$('#Modal')
		.on('show.bs.modal', function(){
				$('#send-activation-mail').attr('disabled', 'disabled');
			});

})