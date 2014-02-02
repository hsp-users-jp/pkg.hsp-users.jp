<h1>パッケージ作者一覧</h1>
<hr>

<?php $i = 0; $authors_count = count($authors); foreach ($authors as $author): ?>
<?php if (0 == $i % 3): ?>
<div class="row">
<?php endif; ?>
	<div class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="media">
					<a class="pull-left" href="#">
						<?php echo Asset::gravatar($author['email'], array(), array('size' => 48, 'd' => 'identicon')); ?>
					</a>
					<div class="media-body">
						<h4 class="media-heading"><?php echo Html::anchor('author/'.e($author['username']), e($author['username'])); ?></h4>
						<div><?php echo e(Auth::get_profile_fields_by_id($author['user_id'], 'fullname', '不明')); ?></div>
						<div><?php echo Html::anchor('search?q=author:'.e($author['username']), 'パッケージ('.$author['count_of_packages'].')'); ?></div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php if (2 == $i % 3 || $i + 1 == $authors_count): ?>
</div>
<?php endif; ?>
<?php $i++; endforeach; ?>
