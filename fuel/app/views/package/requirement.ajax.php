  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">パッケージ動作環境 詳細</h4>
      </div>
      <div class="modal-body">
<?php echo View::forge('package/requirement.body', $data)->render(); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">閉じる</button>
      </div>
    </div>
  </div>

<script>
(function(){
	$('#Modal tr[id^="hsp_spec_"]')
		.hide();
	$('#Modal tr[id^="hsp_category_"]')
		.children('td[data-dropdown]')
		.wrapInner('<a href="#"></a>')
		.children('a')
		.append('<span class="fa fa-caret-down fa-fw"></span>')
		.bind('click', function(){
				$('#Modal tr[id^="hsp_spec_' + $(this).parents('tr').attr('id').replace('hsp_category_', '') + '_"]')
					.toggle();
				return false;
			});
})();
</script>
