<?php

class Controller_Base extends Controller_Template
{
	public function before()
	{
		parent::before();

		// CSRFチェック
		if (Input::method() === 'POST' &&
			!Security::check_token())
		{
			Messages::error('Expire form session!', array('セッションの有効期限が切れました。','操作をやり直してください。'));
			Response::redirect_back('');
		}

		$is_loggedin = Auth::check();

		// 権限でアクセスをフィルタ
		$list = array(
				'admin',
			);
		if (count(array_filter($list, function($v){ return \Str::starts_with(Uri::string(), $v); })))
		{
			if (!$is_loggedin ||
				!Auth::is_super_admin())
			{
				Log::error(sprintf('Illegal access by "%s"(%d) [%s]'
						, Auth::get_screen_name(), Auth::get_user_id_only()
						, Input::server('REMOTE_ADDR','')
					));
				throw new HttpNotFoundException;
			}
		}

		// 非ログイン状態でアクセスをフィルタ
		$list = array(
				'settings',
				'package/new',
				'package/update',
			);
		if (!$is_loggedin &&
			count(array_filter($list, function($v){ return \Str::starts_with(Uri::string(), $v); })))
		{
			Messages::error('Login required!', 'ログインが必要なページにアクセスしようとしています。');
			Response::redirect_back('signin');
		}
	}
}
