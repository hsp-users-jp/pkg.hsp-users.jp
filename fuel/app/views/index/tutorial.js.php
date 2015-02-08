<!--
$(document).ready(function(){

	var url2page = {
		'<?php echo Uri::create("/"); ?>':         'dashboard',
		'<?php echo Uri::create("package/1"); ?>': 'package',
	};
	var pageCache = {
		'tutorial': $('body').html()
						.match(/<div[ ]id="warp">([\s\S]+)<div[ ]id="footer">/m)[1]
						.replace(/data-step="([0-6])"\s+data-intro/mg, 'data-xxx')
	};
	var currentPage = '';
	var onBeforeChange = function(elm){
		var step = parseInt($(elm).attr('data-step'), 10);
		var targetPage = '';
		if (step < 10) {
			targetPage = 'dashboard';
		} else if (step < 20) {
			targetPage = 'package';
		}
		if ('' == currentPage) {
			setTimeout(function(){
				$('[class="introjs-helperNumberLayer"]').css('box-sizing', 'content-box');
			}, 200);
		}
		if (currentPage != targetPage) {
			$('#_'+currentPage).hide();
			$('#_'+targetPage).show();
			currentPage = targetPage;
		}
	};

	$.each(url2page, function(url, pageName){
		$.get(url, function(data){
			var pageName = url2page[this.url];
			data = data.match(/<div[ ]id="warp">([\s\S]+)<div[ ]id="footer">/m)[1];
			if ('dashboard' != pageName)
				data = data.replace(/data-step="([0-6])"\s+data-intro/mg, 'data-xxx');
			pageCache[pageName] = data;

			var restCount = 0;
			$.each(url2page, function(url, pageName){
					restCount += typeof pageCache[pageName] == 'undefined' ? 1 : 0;
				});
			if (restCount) {
				return;
			}

			$('#warp').empty();
			$.each(pageCache, function(pageName, contents){
					$('#warp')
						.append('<div id="_'+pageName+'"><div\>');
					$('#_'+pageName)
						.html(contents)
						.hide();
				});

<?php echo View::forge('index/_star.js')->render(); ?>

			// 最初のチュートリアルを起動
			introJs()
				.setOptions({
					prevLabel: '前へ',
					nextLabel: '次へ',
					skipLabel: 'チュートリアルを中止',
					doneLabel: 'チュートリアルを終了',
					showProgress: true,
				})
				.onbeforechange(onBeforeChange)
				.oncomplete(function(){
					location.href = '<?php echo Uri::create("/"); ?>';
				})
				.onexit(function(){
					location.href = '<?php echo Uri::create("/"); ?>';
				})
				.start();
		});
	});
})
// -->
