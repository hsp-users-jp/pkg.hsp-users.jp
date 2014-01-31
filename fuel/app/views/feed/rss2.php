<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
	  xmlns:dc="http://purl.org/dc/elements/1.1/"
	  xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	  xmlns:admin="http://webns.net/mvcb/"
	  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
	<channel>
		<title><?php echo e($title); ?></title>
		<link><?php echo Uri::current(); ?></link>
		<description><?php echo e($description); ?></description>
		<dc:language><?php echo \Config::get('config.language', 'ja'); ?></dc:language>
		<dc:creator>sharkpp</dc:creator>
		<dc:date><?php $d = new DateTime($date); echo e($d->format(DateTime::W3C)); ?></dc:date>
<?php foreach ($items as $item): ?>
		<item>
			<title><?php echo e($item['title']); ?></title>
			<link><?php echo e($item['url']); ?></link>
			<description><?php echo e($item['description']); ?></description>
			<dc:subject><?php echo e($item['subject']); ?></dc:subject>
			<dc:creator><?php echo e($item['username']); ?></dc:creator> 
			<dc:date><?php $d = new DateTime($item['date']); echo e($d->format(DateTime::W3C)); ?></dc:date>
			<guid><?php echo e($item['url']); ?></guid>
		</item>
<?php endforeach; ?>
	</channel>
</rss>