<div class="panel panel-default">
<div class="panel-body">
	<div class="media"><?php
$author = Auth::get_metadata_by_id($user->id, 'fullname', '不明');
$url = Auth::get_metadata_by_id($user->id, 'url', ''); ?>
		<span class="pull-left">
			<?php echo Asset::gravatar($user->email, array(), array('size' => 96, 'd' => 'identicon')); ?>
		</span>
		<div class="media-body">
<?php if (Auth::is_banned($user)): ?>
			<h4 class="media-heading"><span class="fa fa-ban fa-lg text-danger" title="BAN済み"></span> <?php echo e($user->username) ?></h4>
<?php else: ?>
			<h4 class="media-heading"><?php echo e($user->username) ?></h4>
<?php endif; ?>
			<p><?php echo e($author) ?></p>
			<p>
<?php if ($url): ?>
				<a href="<?php echo e($url); ?>"class="fa-stack fa-lg fa-fw">
					<span class="fa fa-fw fa-stack-2x fa-square"></span>
					<span class="fa fa-fw fa-stack-1x fa-home fa-inverse"></span>
				</a>
<?php endif; ?>
<?php $lookup = array(
                'twitter'  => array('name' => 'Twitter',  'icon' => 'fa-twitter',     'color' => '#569DE6'),
                'google'   => array('name' => 'Google',   'icon' => 'fa-google-plus', 'color' => '#3A77ED'),
                'facebook' => array('name' => 'Facebook', 'icon' => 'fa-facebook',    'color' => '#2F4984'),
                'github'   => array('name' => 'GitHub',   'icon' => 'fa-github',      'color' => '#262626'),
              );
      foreach ($providers as $provider => $data): ?>
				<span class="fa-stack fa-lg fa-fw" title="<?php echo Arr::get($lookup, $provider.'.name', $provider); ?>">
					<span class="fa fa-fw fa-stack-2x fa-square"
					      style="color: <?php echo Arr::get($lookup, $provider.'.color'); ?>;"></span>
					<span class="fa fa-fw fa-stack-1x <?php echo Arr::get($lookup, $provider.'.icon',
					                                                      'fa-'.$provider) ?> fa-inverse"></span>
				</span>
<?php endforeach; ?>
			</p>
		</div>
	</div>
</div>
</div>

<?php /*
<?php foreach ($rows as $row): ?>
<?php echo View::forge('package/item', array('package' => $row, 'without_author' => true))->render(); ?>
<?php endforeach; ?>
*/ ?>

<?php $i = 0; $column_num = 2; $rows_count = count($rows); foreach ($rows as $row): ?>
<?php if (0 == $i % $column_num): ?>
<div class="row">
<?php endif; ?>
	<div class="col-sm-6">
<?php echo View::forge('package/item', array('package' => $row, 'without_author' => true, 'x' => 2))->render(); ?>
	</div>
<?php if ($column_num - 1 == $i % $column_num || $i + 1 == $rows_count): ?>
</div>
<?php endif; ?>
<?php $i++; endforeach; ?>
