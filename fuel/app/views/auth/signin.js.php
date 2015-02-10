$(document).ready(function(){
	$('[id^="login_"]')
		.on('click', function(){
			$('[id^="login_"]')
				.addClass('disabled');
		});
})
