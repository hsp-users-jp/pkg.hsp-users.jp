<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="sharkpp">
    <link rel="shortcut icon" href="<?php echo Uri::create('favicon.png'); ?>">

    <title>HSP Package DB :: <?php echo $title; ?></title>

    <!-- Bootstrap core CSS -->
    <?php echo Asset::css('bootstrap.min.css'); ?>
    <!-- Bootstrap theme -->
    <?php /*echo Asset::css('bootstrap-theme.min.css');*/ ?>

    <?php echo Asset::css('bootstrap-editable.css'); ?>

    <!-- Font Awesome CSS -->
    <?php echo Asset::css('font-awesome.min.css'); ?>

    <!-- Custom styles for this template -->
    <?php echo Asset::css('theme.css'); ?>

    <!-- dropzone CSS -->
    <?php echo Asset::css('dropzone.css'); ?>
    <?php echo Asset::css('basic.css'); ?>

    <style type="text/css">
    	html, body {
    		height: 100%;
    	}
    	body {
    		padding-bottom: 0;
    	}
		#warp {
			min-height: 100%;
			height: auto;
			margin: 0 auto -100px;
			padding: 0 0 100px;
		}
		#footer {
		  height: 100px;
		  padding-top: 10px;
		  background-color: #222;
		  color: #999;
		}
		#porwerd-by-fuelphp {
			border-top: 1px solid #c73dff;
			border-left: 1px solid #c73dff;
			border-bottom: 1px solid #c73dff;
			background-color: #2a2a2a;
			color: #c73dff;
			font-family: monospace;
			font-size: 9px;
		}
		#porwerd-by-fuelphp span {
			border-right: 1px solid #c73dff;
			padding: 1px;
		}
		#porwerd-by-fuelphp a,
		#porwerd-by-fuelphp a:hover {
			text-decoration: none;
			color: #c73dff;
		}
	/*	.navbar-inverse {
			background-color: #b7d7e4;
		}
		.navbar-inverse .navbar-nav>.active>a,
		.navbar-inverse .navbar-nav>.active>a:hover,
		.navbar-inverse .navbar-nav>.active>a:focus {
			color: #000;
			background-color: #8dbbd5;
		}
		.navbar-inverse .navbar-nav>li>a:hover,
		.navbar-inverse .navbar-nav>li>a:focus,
		.navbar-inverse .navbar-brand:hover,
		.navbar-inverse .navbar-brand:focus {
			color: #000;
			//background-color: transparent;
		}
		.navbar-inverse .navbar-nav>li>a,
		.navbar-inverse .navbar-brand {
			color: #333;
		} */
		#top-jumbotron {
			padding: 1em 200px 1em 200px;
			margin: -40px -200px 30px -200px;
			background-color: #222;
			border-color: #080808;
		//	background-color: #b7d7e4;
		//	border-color: #080808;
		}
		#top-jumbotron h1 {
			color: #fff;
			//color: #000;
		}
		#top-jumbotron p {
			color: #999;
			//color: #333;
		}
		.media .media-body {
			 word-wrap: break-word;
		}
    </style>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>

<body>
<div id="warp">
    <!-- Fixed navbar -->
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo Uri::create('/'); ?>">HSP Package DB</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li <?php echo ''!=Uri::segment(1)?:'class="active"'; ?>
               ><a href="<?php echo Uri::create('/'); ?>"><span class="fa fa-home"></span> ホーム</a></li>
            <li <?php echo 'package'!=Uri::segment(1)||'new'==Uri::segment(2)?:'class="active"'; ?>
               ><a href="<?php echo Uri::create('package'); ?>"><span class="fa fa-list"></span> パッケージ</a></li>
            <li <?php echo 'tag'!=Uri::segment(1)?:'class="active"'; ?>
               ><a href="<?php echo Uri::create('tag');     ?>"><span class="fa fa-tags"></span> タグ</a></li>
            <li <?php echo 'author'!=Uri::segment(1)?:'class="active"'; ?>
               ><a href="<?php echo Uri::create('author');  ?>"><span class="fa fa-users"></span> 作成者</a></li>
            <li <?php echo 'search'!=Uri::segment(1)?:'class="active"'; ?>
               ><a href="<?php echo Uri::create('search');  ?>"><span class="fa fa-search"></span> 検索</a></li>
<!--
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-wrench"></span> 管理 <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="#"><span class="fa fa-cog"></span> 設定</a></li>
                <li><a href="#"><span class="fa fa-list"></span> パッケージ一覧</a></li>
                <li class="divider"></li>
                <li><a href="#"><span class="fa fa-sign-out"></span> ログアウト</a></li>
              </ul>
            </li>
-->
          </ul>
          <ul class="nav navbar-nav navbar-right">
<?php if (!Auth::check()): ?>
            <li <?php echo 'signin'!=Uri::segment(1)?:'class="active"'; ?>
               ><a href="<?php echo Uri::create('signin'); ?>"><span class="fa fa-sign-in"></span> ログイン</a></li>
<?php else: ?>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-user"></span> <?php echo e(Auth::get_screen_name()); ?> <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="<?php echo Uri::create('settings'); ?>"><span class="fa fa-cog"></span> 設定</a></li>
                <li class="divider"></li>
                <li><a href="<?php echo Uri::create('package/new'); ?>"><span class="fa fa-plus-circle"></span> パッケージの追加</a></li>
<?php if (Model_Package::query()->where('user_id', Auth::get_user_id_only())->count()): ?>
                <li><a href="<?php echo Uri::create('author/'.urlencode(Auth::get_screen_name())); ?>"
                      ><span class="fa fa-list"></span> パッケージ一覧</a></li>
<?php endif; ?>
                <li class="divider"></li>
                <li><a href="<?php echo Uri::create('signout'); ?>"><span class="fa fa-sign-out"></span> ログアウト</a></li>
              </ul>
            </li>
<?php endif; ?>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>

    <div class="container theme-showcase">

<?php if (Uri::string()): ?>

<?php echo View::forge('auth/activation_warning')->render(); ?>

<?php if (Session::get_flash('success')): ?>
			<div class="alert alert-success">
				<strong>成功</strong>
				<p>
				<?php echo implode('</p><p>', e((array) Session::get_flash('success'))); ?>
				</p>
			</div>
<?php endif; ?>
<?php if (Session::get_flash('error')): ?>
			<div class="alert alert-danger">
				<strong>エラー</strong>
				<p>
				<?php echo implode('</p><p>', e((array) Session::get_flash('error'))); ?>
				</p>
			</div>
<?php endif; ?>

<?php endif; ?>

<?php echo $content; ?>

<div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true"></div>

    </div> <!-- /container -->
</div>

	<div id="footer">
		<div class="container">
			<p class="text-muted pull-right"><span id="porwerd-by-fuelphp"><a href="http://fuelphp.com/"><span>POWERD BY</span><span>FuelPHP</span></a></span></p>
			<p class="text-muted">
				<?php echo Html::anchor('about', 'このサイトについて'); ?>
			</p>
			<p class="text-muted">Copyright &copy; 2014 <a href="http://www.sharkpp.net/">sharkpp</a>. All Rights Reserved.</p>
		</div>
	</div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php echo Asset::js('jquery.min.js'); ?>
    <?php echo Asset::js('bootstrap.min.js'); ?>
    <?php echo Asset::js('bootstrap-editable.min.js'); ?>
    <?php echo Asset::js('dropzone.min.js'); ?>
    <?php echo Asset::js('holder.js'); ?>
    <?php !isset($js) ?: print('<script type="text/javascript">' . $js . '</script>'); ?>
  </body>
</html>