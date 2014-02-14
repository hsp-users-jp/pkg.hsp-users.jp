<?php

class Controller_Auth extends Controller_Base
{

	public function action_signup()
	{
		if (Auth::check())
		{ // 既にログイン済みなのでトップに転送
			Response::redirect('');
		}

		$data['state'] = array();

		// fetch the oauth provider from the session (if present)
		$provider = Session::get('auth-strategy.authentication.provider', false);
		$data['provider'] = $provider;

		$val = Validation::forge('val');
		$val->add('username', 'ユーザー名')
			->add_rule('required');
		$val->add('password', 'パスワード');
		$val->add('password2', 'パスワード(確認)');
		$val->add('fullname', '名前')
			->add_rule('required');
		$val->add('email', 'メールアドレス')
			->add_rule('valid_email')
			->add_rule('required');
		if ($provider)
		{
			// OAuth経由の場合は @hoge を許す
			$nickname = Session::get('auth-strategy.user.nickname', '');
			$val->field('username')
				->add_rule('match_pattern', '/^([a-zA-Z0-9-_]+|'.$nickname.'@'.$provider.')$/');
		}
		else
		{
			// OAuth経由ではない場合は @hoge を許さない
			$val->field('username')
				->add_rule('match_pattern', '/^[a-zA-Z0-9-_]+$/');
//				->add_rule('valid_string', array('utf8', 'alpha', 'numeric', 'dashes'));
			// OAuth経由ではない場合はパスワードが必須
			$val->field('password')
				->add_rule('required');
			$val->field('password2')
				->add_rule('match_field', 'password')
				->add_rule('required');
		}

		// if we have provider information, create the login fieldset too
		if ($provider)
		{
		}

		if (Input::post())
		{
			if (!$provider)
			{
				$addtional = array();
			}
			else
			{
				$opauth = Auth_Opauth::forge(false);
				$addtional['password'] = $opauth->get('auth.info.password', Str::random('sha1'));
				$addtional['password2'] = $addtional['password'];
			}

			if ($val->run($addtional))
			{
				try
				{
					$activate_hash = 'a'; // @todo ハッシュを作成する

					$userid = Auth::create_user(
									$val->validated('username'),
									$val->validated('password'),
									$val->validated('email'),
									Config::get('app.user.default.group', 1),
									array(
										'activate_hash' => $activate_hash,
										'fullname'      => $val->validated('fullname'),
									)
								);
					if ($userid)
					{
						if ($provider)
						{
							// 手動でプロバイダにリンクします
							$this->link_provider($userid);
						}

						Auth::force_login($userid);

						// 登録されたメールに対して本登録のメールを送る
						// $email = Email::forge(); @todo ちゃんとやる

						Session::set('activate_hash', $activate_hash);
						Response::redirect('');
					}
					else
					{
						Messages::error('登録に失敗しました');
					}
				}
				catch (\SimpleUserUpdateException $e)
				{
					switch ($e->getCode())
					{
					case 2: // Email address already exists
						$errors = array('同じメールアドレスが既に登録されているため登録できませんでした');
						break;
					case 3: // Username already exists
						$errors = array('同じユーザー名が既に登録されているため登録できませんでした');
						break;
					default:
						$errors = array($e->getMessage());
						break;
					}
					Messages::error($errors);
				}
				catch (\Exception $e)
				{
					Messages::error($e->getMessage(), 'エラーが発生しました');
				}
			}
			else
			{
				$errors = array('入力項目を確認してください。');

				foreach ($val->error() as $field => $error)
				{
					$data['state'][$field] = 'has-error';
					$errors[] = $error->get_message();
				}
	
				Messages::error($errors);
			}
		}
		else
		{
			// セッションから (コールバックにより作成された) auth-strategy データを取得
			$user = Session::get('auth-strategy.user', array());
Log::debug(print_r(Session::get('auth-strategy', array()),true));

			$_POST['username'] = Arr::get($user, 'nickname')
			                     . ($provider ? '@' . $provider : '');
			$_POST['fullname'] = Arr::get($user, 'name');
			$_POST['email']    = Arr::get($user, 'email');
		}

		$this->template->title = '新規登録';
		$this->template->content = View::forge('auth/signup', $data);
	}

	public function action_signin()
	{
		Session::delete('auth-strategy');

		if (Auth::check())
		{ // 既にログイン済みなのでトップに転送
			Response::redirect('');
		}

//		$provider = Input::get('provider');
//		if (null !== $provider)
//		{
//			if (empty($provider))
//			{
//				Messages::error('プロバイダの指定がありません。');
//				Response::redirect_back();
//			}
//
//			$config['provider'] = $provider;
//			Auth_Opauth::forge($config);
//
//			return;
//		}

		// fetch the oauth provider from the session (if present)
		$provider = \Session::get('auth-strategy.authentication.provider', false);
	
		// if we have provider information, create the login fieldset too
		if ($provider)
		{
		}

		$val = Validation::forge('val');
		$val->add('username', 'ユーザー名')
			->add_rule('required');
		$val->add('password', 'パスワード')
			->add_rule('required');
		$val->add('remember_me', 'ログイン状態を維持');

		if (Input::post())
		{
			if ($val->run())
			{
				try
				{
					if (Auth::login($val->validated('username'), $val->validated('password')))
					{
						if ((int)$val->validated('remember_me'))
						{ // クッキーを登録
							Auth::remember_me();
						}
						else
						{ // クッキーを削除
							 Auth::dont_remember_me();
						}

						Messages::success('ログインしました');
						Response::redirect('');
					}
					else
					{
					}
				}
				catch (\Exception $e)
				{
					Messages::error($e->getMessage(), 'エラーが発生しました');
				}
			}
			else
			{
				$errors = array('入力項目を確認してください。');

				foreach ($val->error() as $field => $error)
				{
					$data['state'][$field] = 'has-error';
					$errors[] = $error->get_message();
				}
	
				Messages::error($errors);
			}
		}
		else
		{
			//	\Session::set('auth-strategy', array(
			//	        'user' => $this->get('auth.info'),
			//	        'authentication' => array(
			//	                'provider'              => $this->get('auth.provider'),
			//	                'uid'                   => $this->get('auth.uid'),
			//	                'access_token'  => $this->get('auth.credentials.token', null),
			//	                'secret'                => $this->get('auth.credentials.secret', null),
			//	                'expires'               => $this->get('auth.credentials.expires', null),
			//	                'refresh_token' => $this->get('auth.credentials.refresh_token', null),
			//	        ),
			//	));

			// セッションから (コールバックにより作成された) auth-strategy データを取得
			$user = Session::get('auth-strategy.user', array());

			$_POST['username'] = Arr::get($user, 'nickname');
			$_POST['email']    = Arr::get($user, 'email');
		}

		$data['state'] = array();
		$data["subnav"] = array('signin'=> 'active' );
		$this->template->title = 'Auth &raquo; Signin';
		$this->template->content = View::forge('auth/signin', $data);
	}

	public function action_signout()
	{
//		$data["subnav"] = array('signout'=> 'active' );
//		$this->template->title = 'Auth &raquo; Signout';
//		$this->template->content = View::forge('auth/signout', $data);

		// remove the remember-me cookie, we logged-out on purpose
		Auth::dont_remember_me();
		
		// ログアウト
		Auth::logout();
		
		// inform the user the logout was successful
		Messages::success('ログアウトしました');

		// ログインが必要なページ以外はもとのページに戻る
		$list = array(
				'admin',
				'settings',
				'package/new',
				'package/update',
			);
		if (count(array_filter($list, function($v){ return \Str::starts_with(Input::referrer(), Uri::base().$v); })))
		{
			Response::redirect();
		}
		else
		{
			Response::redirect_back();
		}
	}

	public function action_join()
	{
	// あとからSNSアカウントを追加
		$data["subnav"] = array('signout'=> 'active' );
		$this->template->title = 'Auth &raquo; Signout';
		$this->template->content = View::forge('auth/join', $data);
	}

	public function action_oauth($provider = null)
	{
		// bail out if we don't have an OAuth provider to call
		if ($provider === null)
		{
			Messages::error('プロバイダの指定がありません。');
			Response::redirect_back();
		}

		try
		{
			// Opauth の読み込み、プロバイダのストラテジーを読み込みプロバイダにリダイレクトするでしょう
			Auth_Opauth::forge();
		}
		catch (\Exception $e)
		{ // 大文字小文字を間違えても例外発生でここに来る
			Log::error($e->getMessage());
			throw new HttpNotFoundException;
		}
	}

	public function action_callback()
	{
		// Opauth can throw all kinds of nasty bits, so be prepared
		try
		{
			// Opauth オブジェクトを取得
			$opauth = \Auth_Opauth::forge(false);

			// そして、コールバックを処理
			$status = $opauth->login_or_register();
Log::debug($status);

			// fetch the provider name from the opauth response so we can display a message
			$provider = $opauth->get('auth.provider', '?');
Log::debug(print_r($opauth->get('auth', array()),true));

			if (Auth::is_banned())
			{
				Messages::error(sprintf('deny logged-in, %d was banned', Auth::get_user_id_only()),
				                sprintf('%s でのログインに失敗しました', ucfirst($provider)));
				// Banされているのでログインに失敗したので強制ログアウト
				Auth::instance()->logout(); // なぜか Auth::logout() だとダメ
				// and set the redirect url for this status
				$url = ''; //dashboard
			}
			else
			{
				// deal with the result of the callback process
				switch ($status)
				{
				// a local user was logged-in, the provider has been linked to this user
				case 'linked':
					// inform the user the link was succesfully made
					Messages::success(sprintf('%s と関連付けを行いました', ucfirst($provider)));
					// and set the redirect url for this status
					$url = ''; //dashboard
					break;
	
				// the provider was known and linked, the linked account as logged-in
				case 'logged_in':
					// inform the user the login using the provider was succesful
					Messages::success(sprintf('%s でログインしました', ucfirst($provider)));
					// and set the redirect url for this status
					$url = ''; //dashboard
					break;
	
				// we don't know this provider login, ask the user to create a local account first
				case 'register':
					// and set the redirect url for this status
					$url = 'signup';
					break;
	
				// we didn't know this provider login, but enough info was returned to auto-register the user
				case 'registered':
					// inform the user the login using the provider was succesful, and we created a local account
					Messages::success('アカウントが登録されました');
					// and set the redirect url for this status
					$url = ''; //dashboard
					break;
	
				default:
					throw new \FuelException('Auth_Opauth::login_or_register() has come up with a result that we dont know how to handle.');
				}
			}

			// リダイレクト先の URL をセット
			Response::redirect($url);
		}

		// deal with Opauth exceptions
		catch (\OpauthException $e)
		{
			Messages::error($e->getMessage());
			Response::redirect_back('signin');
		}

		// catch a user cancelling the authentication attempt (some providers allow that)
		catch (\OpauthCancelException $e)
		{
			// you should probably do something a bit more clean here...
			exit('It looks like you canceled your authorisation.'.\Html::anchor('users/oath/'.$provider, 'Click here').' to try again.');
		}
	}

	protected function link_provider($userid)
	{
		// do we have an auth strategy to match?
		if ($authentication = \Session::get('auth-strategy.authentication', array()))
		{
			// don't forget to pass false, we need an object instance, not a strategy call
			$opauth = \Auth_Opauth::forge(false);
			
			// call Opauth to link the provider login with the local user
			$insert_id = $opauth->link_provider(array(
					'parent_id'     => $userid,
					'provider'      => $authentication['provider'],
					'uid'           => $authentication['uid'],
					'access_token'  => $authentication['access_token'],
					'secret'        => $authentication['secret'],
					'refresh_token' => $authentication['refresh_token'],
					'expires'       => $authentication['expires'],
					'created_at'    => time(),
				));
		}
	}
}
