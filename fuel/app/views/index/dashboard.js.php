$(document).ready(function(){
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

	introJs()
		.setOptions({
			prevLabel: '前へ',
			nextLabel: '次へ',
			skipLabel: '飛ばす',
			doneLabel: '完了',
		})
		.onbeforechange(function(targetElement) {
			console.log(targetElement);
			console.log($(targetElement).parents('[class="dropdown"]'));
			console.log($(targetElement).parents('[class="dropdown"]').children('a'));
			console.log($(targetElement).parents('[data-step="5"]'));
			console.log($(targetElement).parents('[data-step="5"]').children('a'));
		//	$(targetElement).parents('[class="dropdown"]').children('a').click();
		//	$(targetElement).parents('[data-step="5"]').addClass('open');
			if ('6'==$(targetElement).attr('data-step')) {
				$('[data-step="5"]').addClass('open');
			}
		})
		.onchange(function(targetElement) {
			if ('6'==$(targetElement).attr('data-step')) {
				$('[data-step="5"]').addClass('open');
			}
		})
		.start();
})