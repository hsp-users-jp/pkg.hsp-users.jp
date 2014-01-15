<?php if ('recents' == Input::get('sort')): ?>
	<h1>最近の更新</h1>
<?php elseif ('popular' == Input::get('sort')): ?>
	<h1>人気のダウンロード</h1>
<?php else: ?>
	<h1>パッケージの一覧</h1>
<?php endif ?>
<hr>

<div class="text-center">
<?php echo $pagination ?>
</div>

<?php foreach ($rows as $row): ?>
<?php echo View::forge('package/item', array('package' => $row))->render(); ?>
<?php endforeach; ?>

<div class="text-center">
<?php echo $pagination ?>
</div>
