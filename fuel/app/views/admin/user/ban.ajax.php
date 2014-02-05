<div class="modal-dialog  modal-sm">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title">操作の確認</h4>
		</div>
		<div class="modal-body">
<?php echo View::forge('admin/user/ban.body', $data)->render(); ?>
		</div>
		<div class="modal-footer">
			<button id="yes" type="button" class="btn btn-default" data-dismiss="modal">はい</button>
			<button type="button" class="btn btn-primary" data-dismiss="modal">いいえ</button>
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
