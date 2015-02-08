	$('[id^=package_rating_]')
		.raty({
			space: false,
			hints: ['だめ', 'いまいち', '普通', 'よい', 'すばらしい'],
			noRatedMsg: 'まだ誰も評価していません',
			path: '<?php echo Uri::create("assets/images"); ?>',
			readOnly: true,
			score: function(){
				return $(this).attr('data-score');
			},
		});
	$('[id^=package_rating_] img').removeAttr('title');
