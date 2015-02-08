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
console.log(pageCache);
	var onBeforeChange = function(elm){
		var step = parseInt($(elm).attr('data-step'), 10);
		var targetPage = '';
		if (step < 10) {
			targetPage = 'dashboard';
		} else if (step < 20) {
			targetPage = 'package';
		}
console.log('step='+step+',targetPage='+targetPage);
		if ('' == currentPage) {
			setTimeout(function(){
				$('[class="introjs-helperNumberLayer"]').css('box-sizing', 'content-box');
			}, 200);
		}
		if (currentPage != targetPage) {
		//	$('body').html(pageCache[targetPage]);
			$('#_'+currentPage).hide();
			$('#_'+targetPage).show();
			currentPage = targetPage;
/*			setTimeout(function(){
				introJs().exit();
			}, 100);
			setTimeout(function(){
				console.log('--'+step);
				introJs()
					.onbeforechange(onBeforeChange)
					.goToStep(step)
					.start();
			}, 200);*/
		}
	};

	$.each(url2page, function(url, pageName){
		$.get(url, function(data){
			var pageName = url2page[this.url];
			data = data.match(/<div[ ]id="warp">([\s\S]+)<div[ ]id="footer">/m)[1];
			if ('dashboard' != pageName)
				data = data.replace(/data-step="([0-6])"\s+data-intro/mg, 'data-xxx');
		//	data = data.match(/[\x3c]body[\x3e]([\s\S]+)[\x3c]\/body[\x3e]/m)[1].replace(/.!-- JavaScript --.[\s\S]+/m, '');
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
//					console.log(pageName);
//					console.log(contents.substr(0,32));
//					console.log(contents.substr(-32));
					$('#warp')
						.append('<div id="_'+pageName+'"><div\>');
					$('#_'+pageName)
						.html(contents)
						.hide();
					console.log('----------');
				});
					console.log('==========');

<?php echo View::forge('index/_star.js')->render(); ?>

			// 最初のチュートリアルを起動
		//	onBeforeChange($('[data-step="1"]').get(0));
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

/*
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
*/
})
// -->
