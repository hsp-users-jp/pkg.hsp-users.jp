<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title">パッケージバージョンの復元</h4>
		</div>
		<div class="modal-body">
<?php echo View::forge('package/cure.body', $data)->render(); ?>
		</div>
		<div class="modal-footer">
			<button id="yes" type="button" class="btn btn-default" data-dismiss="modal">はい</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">いいえ</button>
		</div>
	</div>
</div>

<script>
(function(){

	// reset load data for modal
	$('#Modal')
		.on('hidden.bs.modal', function (e) {
			$(this).removeData('bs.modal');
		});

	$('button#yes')
		.on('click', function(){
			$('form#ban-form').submit();
		});

})();
</script>
