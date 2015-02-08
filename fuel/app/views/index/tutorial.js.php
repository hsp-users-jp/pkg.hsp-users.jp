<!--
$(document).ready(function(){

	var url2page = {
		'<?php echo Uri::create("/"); ?>':         'dashboard',
		'<?php echo Uri::create("package"); ?>':   'package_list',
		'<?php echo Uri::create("package/1"); ?>': 'package_detail',
	};
	var pageCache = {
		'tutorial': $('body').html()
						.match(/<div[ ]id="warp">([\s\S]+)<div[ ]id="footer">/m)[1]
	};
	var currentPage = '';
	var onBeforeChange = function(elm){
		var item = this._introItems[this._currentStep];
		var step = parseInt($(elm).attr('data-step'), 10);
		var targetPage = typeof item.query != 'undefined'
							? item.query.match(/#_([^\s]+)/)[1]
							: typeof item.page != 'undefined'
								? item.page
								: currentPage;
		if ('' == targetPage) {
			targetPage = 'dashboard';
		}
		if (currentPage != targetPage) {
			$('#_'+currentPage).hide();
			$('#_'+targetPage).show();
			$('body').scrollTop(0);
			currentPage = targetPage;
		}
	};

	$.each(url2page, function(url, pageName){
		$.get(url, function(data){
			var pageName = url2page[this.url];
			data = data.match(/<div[ ]id="warp">([\s\S]+)<div[ ]id="footer">/m)[1];
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

			var steps = [
					{	page: 'dashboard',
						intro: "ようこそ HSP Package DB へ<br />" +
						       "このチュートリアルでは各画面の使い方を紹介します。"	},

					{	query: '#_dashboard ul[class="nav navbar-nav"] > li:eq(0)',
						intro: "この画面(ダッシュボート)を表示します。"	},
					{	query: '#_dashboard [class="well"]',
						intro: "名称や作者などでパッケージをすぐに検索することができます。"	},
					{	query: '#_dashboard ul[class="nav navbar-nav"] > li:eq(3)',
						intro: "ここからも同じように検索することができます。"	},
					{	query: '#_dashboard [class="row"] > div:eq(1)',
						intro: "ここには最近更新された順番にパッケージが並びます。"	},
				//	{	query: '#_dashboard [class="row"] > div:eq(1) [id^="package_rating_"]:eq(0)',
				//		intro: "パッケージに対しての"	},
					{	query: '#_dashboard [class="row"] > div:eq(2)',
						intro: "ここにはダウンロードの回数順にパッケージが並びます。"	},

					{	page: 'package_list',
						intro: "パッケージの一覧画面です。"	},
					{	query: '#_package_list ul[class="nav navbar-nav"] > li:eq(1)',
						intro: "登録済みパッケージの一覧画面を表示します。"	},
					{	query: '#_package_list [class="panel panel-default"]:eq(0)',
						intro: "パッケージごとの情報が表示されます。"	},
					{	query: '#_package_list [class="panel panel-default"]:eq(0) [id^="package_rating_"]',
						intro: "パッケージの評価が表示されます。"	},
					{	query: '#_package_list [class="panel panel-default"]:eq(0) > div:eq(1) > div:eq(1) > a:eq(0)',
						intro: "ここからパッケージのダウンロードをすることが出来ます。"	},
					{	query: '#_package_list [class="panel panel-default"]:eq(0) > div:eq(1) > div:eq(1) > a:eq(1)',
						intro: "パッケージの詳細画面が表示されます。"	},

					{	page: 'package_detail',
						intro: "パッケージの詳細画面です。"	},
					{	query: '#_package_detail h1',
						intro: "パッケージのタイトルです。<br />" +
						       "パッケージ作者の場合、タイトル領域をクリックし編集することができます。"	},
					{	query: '#_package_detail h1 [id^="package_rating_"]',
						intro: "パッケージの評価が表示されます。<br />" +
						       'ログイン済みの場合、<img src="<?php echo Uri::create("assets/images/star-on.png"); ?>" />' +
						       'マークをクリックし評価することができます。'	},

					{	query: '#_dashboard ul[class="nav navbar-nav"] > li:eq(2)',
						intro: "パッケージ製作者の一覧画面を表示します。"	},
					{	query: '#_dashboard ul[class="nav navbar-nav"] > li:eq(3)',
						intro: "パッケージを名称や作者で検索できる画面を表示します。"	},
					{	query: '#_dashboard ul[class="nav navbar-nav navbar-right"] > li[class="dropdown"]',
						intro: "ユーザーの設定やパッケージの追加や登録済みパッケージの一覧表示などを選べます。"	},
				];
			$.each(steps, function(index, value){
					if (typeof value.query != 'undefined') {
						value.element
							= $(value.query)
								.attr('data-step', index + 1)
								.get(0);
					}
				});

			// 最初のチュートリアルを起動
			introJs()
				.setOptions({
					prevLabel: '前へ',
					nextLabel: '次へ',
					skipLabel: 'チュートリアルを中止',
					doneLabel: 'チュートリアルを終了',
					showProgress: true,
					steps: steps,
				})
				.onbeforechange(onBeforeChange)
				.onafterchange(function(){
					$('[class="introjs-helperNumberLayer"]')
						.css('box-sizing', 'content-box');
					$('[class*="introjs-button"')
						.removeClass('introjs-button')
						.addClass('btn btn-default');
				})
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
