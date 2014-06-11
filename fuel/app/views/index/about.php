<h1>このサイトについて</h1>
<hr>

<blockquote class="blockquote-reverse">
	<p>HSPユーザーのためのHSPユーザーによるHSPユーザのデータベース</p>
	<footer>sharkpp, 2014-04-02</footer>
</blockquote>

<p>Copyright &copy; 2014 <a href="http://www.sharkpp.net/">sharkpp</a>. All Rights Reserved.</p>

<h2>ライセンス</h2>

<p>
<?php
	$licenses
		= array(
			array(
				'title' => 'HSP Package DB',
				'text' => Markdown::parse(File::read(DOCROOT.'../README.md', true))
			),
			array(
				'title' => 'FuelPHP',
				'text' => Markdown::parse(File::read(DOCROOT.'../FuelPHP-LICENSE.md', true))
			),
			array(
				'title' => 'Monolog - Logging for PHP 5.3+',
				'text' => implode("<br />", explode("\n", File::read(VENDORPATH.'monolog/monolog/LICENSE', true)))
			),
			array(
				'title' => 'PSR Log',
				'text' => implode("<br />", explode("\n", File::read(VENDORPATH.'psr/log/LICENSE', true)))
			),
			array(
				'title' => 'Bootstrap 3',
				'text' => implode("<br />", explode("\n", File::read(DOCROOT.'assets/css/bootstrap-LICENSE', true)))
			//	'text' => Markdown::parse(File::read(DOCROOT.'assets/css/bootstrap-LICENSE', true))
			),
			array(
				'title' => 'Font Awesome 4',
				'text' => Markdown::parse(File::read(DOCROOT.'../FontAwesome-LICENSE.md', true))
			),
			array(
				'title' => 'X-editable (Bootstrap 3 build)',
				'text' => implode("<br />", explode("\n", File::read(DOCROOT.'assets/css/bootstrap-editable-LICENSE-MIT', true)))
			),
			array(
				'title' => 'DropzoneJS',
				'text' => implode("<br />", explode("\n", File::read(DOCROOT.'../DropzoneJS-LICENSE.md', true)))
			//	'text' => Markdown::parse(File::read(DOCROOT.'../DropzoneJS-LICENSE.md', true))
			),
		  );
?>
<div class="panel-group" id="accordion" data-toggle="false">
<?php $id = 'collapse1'; foreach ($licenses as $license): ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion" href="#<?php echo $id; ?>">
<?php echo e($license['title']); ?>
				</a>
			</h4>
		</div>
		<div id="<?php echo $id; ?>" class="panel-collapse collapse">
			<div class="panel-body" style="font-family:monospace; letter-spacing:0.1em;">
<?php echo $license['text']; ?>
			</div>
		</div>
	</div>
<?php $id = Str::increment($id); endforeach; ?>
</div>
</p>