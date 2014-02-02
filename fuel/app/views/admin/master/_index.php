<div class="row">
	<div class="col-md-3">
<?php echo View::forge('admin/master/_sidebar')->render(); ?>
	</div>
	<div class="col-md-9">

<h1><?php echo e($title); ?><small>マスターテーブルを編集</small></h1>
<hr>

<div class="text-center">
<?php echo $pagination ?>
</div>

<div class="table-responsive">
	<table class="table table-striped">
		<tr>
			<th class="text-center" style="width: 5em;">&nbsp</th>
<?php foreach ($cols as $col): ?>
			<th><?php echo e($col) ?></th>
<?php endforeach; ?>
		</tr>
<?php foreach ($rows as $row): ?>
		<tr>
			<td class="text-center">
				<a href="<?php echo Uri::create(Uri::string().'/edit/:id', array('id'=>$row->id)) ?>"
				   data-toggle="_modal" data-target="#modal"><span class="fa fa-edit"></span></a>
				<a href="<?php echo Uri::create(Uri::string().'/remove/:id', array('id'=>$row->id)) ?>"
				   data-toggle="_modal" data-target="#modal"><span class="fa fa-trash-o"></span></a>
			</td>
<?php foreach ($cols as $col): ?>
			<td><?php echo e($row[$col]); ?></td>
<?php endforeach; ?>
		</tr>
<?php endforeach; ?>
		<tr>
			<td class="text-center">
				<a href="<?php echo Uri::create(Uri::string().'/new') ?>" data-toggle="_modal" data-target="#Modal"
				  ><span class="fa fa-plus-circle"></span></a>
			</td>
<?php foreach ($cols as $col): ?>
			<td>&nbsp</td>
<?php endforeach; ?>
		</tr>
	</table>
</div>

<div class="text-center">
<?php echo $pagination ?>
</div>

	</div>
</div>

<script>
var run_jquery_loaded = function(){if (typeof $ !== 'undefined') {

/*
$('a[href*="new"],a[href*="edit"]')
	.bind('click', function(){
			$('#myModal').modal({
					remote: $(this).attr('href'),
				});
			return false;
		});
*/

}else{setTimeout(run_jquery_loaded,100);}};run_jquery_loaded();
</script>
