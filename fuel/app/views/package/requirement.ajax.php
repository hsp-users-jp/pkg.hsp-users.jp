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
	<?php $csrf_token_key = Config::get('security.csrf_token_key'); ?>

	$('#Modal tr[id^="hsp_spec_"]')
		.hide();
	$('#Modal tr[id^="hsp_category_"]')
		.children('td[data-dropdown]')
		.wrapInner('<a href="#"></a>')
		.children('a')
		.append('<span class="fa fa-caret-down fa-fw"></span>')
		.bind('click', function(){
				$('#Modal tr[id^="hsp_spec_' + 
						$(this).parents('tr').attr('id').replace('hsp_category_', '') + '_"]')
					.toggle();
				return false;
			});
	$('a[id^="hsp_spec_req\["]')
		.editable({
			ajaxOptions: { dataType: 'json' }, 
			mode: 'popup',
			tpl: '<div class="editable-checklist"></div>',
			escape: false,
			defaultValue: 2,
			source: [
				{value: 0, text: '&nbsp;&nbsp;-&nbsp;&nbsp;'},
				{value: 1, text: '<span class="label label-success"><span class="fa fa-check"></span></span>'},
				{value: 3, text: '<span class="label label-danger"><span class="fa fa-times"></span></span>'}
			],
			display: function(value, sourceData) {
				var checked = $.fn.editableutils.itemsByValue(value, sourceData);
				$(this).html(checked[0].text);
			},
			params: function(params) {
				params.<?php echo $csrf_token_key; ?>
					= $('#form_<?php echo $csrf_token_key; ?>').attr('value');
				return params;
			},
			success: function(response, newValue) {
				console.log(response);
				$('#form_<?php echo $csrf_token_key; ?>').attr('value', response.csrf_token);
			}
		});
})();
</script>
