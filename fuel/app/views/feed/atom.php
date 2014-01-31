<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom"
      xmlns:media="http://search.yahoo.com/mrss/"
      xml:lang="ja-JP">
	<id>tag:<?php echo str_replace(':', '.', Input::server('HTTP_HOST'));
	     ?>,<?php $d = new DateTime($date); echo e($d->format('Y'));
	     ?>:<?php echo $id; ?></id>
<?php
//	<link type="text/html" rel="alternate" href=""/>
?>
	<link type="application/atom+xml" rel="self" href="<?php echo Uri::current(); ?>" />
	<title><?php echo e($title); ?></title>
	<updated><?php $d = new DateTime($date); echo e($d->format(DateTime::W3C )); ?></updated>
<?php foreach ($items as $item): ?>
	<entry>
		<id>tag:<?php echo str_replace(':', '.', Input::server('HTTP_HOST'));
		     ?>,<?php $d = new DateTime($item['date']); echo e($d->format('Y'));
		     ?>:<?php echo $item['id']; ?></id>
		<link type="text/html" rel="alternate" href="<?php echo e($item['url']); ?>"/>
		<title>?php echo e($item['title']); ?></title>
		<updated><?php $d = new DateTime($item['date']); echo e($d->format(DateTime::W3C )); ?></updated>
		<author>
			<name><?php echo e($item['username']); ?></name>
		</author>
		<content type="html"><?php echo e($item['description']); ?></content>
	</entry>
<?php endforeach; ?>
</feed>