<h1>パッケージ作者一覧</h1>
<hr>

<?php $i = 0; $authors_count = count($authors); foreach ($authors as $author): ?>
<?php if (0 == $i % 3): ?>
<div class="row">
<?php endif; ?>
	<div class="col-sm-4"><?php $is_banned = Auth::is_banned($author->user_id); ?>
<?php if ($is_banned): ?>
		<div class="panel panel-danger">
<?php else: ?>
		<div class="panel panel-default">
<?php endif; ?>
			<div class="panel-body">
				<div class="media">
					<a class="pull-left" href="#">
						<?php echo Asset::gravatar($author->email, array(), array('size' => 48, 'd' => 'identicon')); ?>
					</a>
					<div class="media-body">
<?php if ($is_banned): ?>
						<h4 class="media-heading"><span class="fa fa-ban fa-lg text-danger" title="BAN済み"></span> <?php echo Html::anchor('author/'.urlencode($author->username), e($author->username)); ?></h4>
<?php else: ?>
						<h4 class="media-heading"><?php echo Html::anchor('author/'.urlencode($author->username), e($author->username)); ?></h4>
<?php endif; ?>
						<div><?php echo e(Auth::get_profile_fields_by_id($author->user_id, 'fullname', '不明')); ?></div>
						<div><?php echo Html::anchor('search?q=author:'.urlencode($author->username), 'パッケージ('.$author->count_of_packages.')'); ?></div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php if (2 == $i % 3 || $i + 1 == $authors_count): ?>
</div>
<?php endif; ?>
<?php $i++; endforeach; ?>
