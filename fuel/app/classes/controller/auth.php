<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2014-2015 sharkpp
 * @link       http://www.sharkpp.net/
 */

class Controller_Auth extends Controller_Base
{

	public function action_signup()
	{
		if (Auth::check())
		{ // 既にログイン済みなのでトップに転送
			return Response::redirect('');
		}

		$data['state'] = array();

		// セッションからの OAuth プロバイダを取得 (存在する場合)
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

		// プロバイダ情報を保持している場合、加えてログインフィールドセットも作成
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
					$userid = Auth::create_user(
									$val->validated('username'),
									$val->validated('password'),
									$val->validated('email'),
									Config::get('app.user.default.group', 1),
									array(
										'fullname' => $val->validated('fullname'),
										'fullname_sync_sns' => true,
										'activate_hash' => '',       // send_activativation_mail() の中で生成
										'activate_hash_expire' => 0, //            〃
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

						// 登録されたメールに対して仮登録のメールを送る
						Model_User::send_activativation_mail();

						Session::set('registration_success', 'interim');
						return Response::redirect('');
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
					case 2: // メールアドレスが重複
						$errors = array('同じメールアドレスが既に登録されているため登録できませんでした');
						break;
					case 3: // ユーザー名が重複
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
		$this->template->breadcrumb = array( '/' => 'トップ', '' => $this->template->title );
		$this->template->content = View::forge('auth/signup', $data);
	}

	public function action_signin()
	{
		Session::delete('auth-strategy');

		if (Auth::check())
		{ // 既にログイン済みなのでトップに転送
			return Response::redirect('');
		}

//		$provider = Input::get('provider');
//		if (null !== $provider)
//		{
//			if (empty($provider))
//			{
//				Messages::error('プロバイダの指定がありません。');
//				return Response::redirect_back();
//			}
//
//			$config['provider'] = $provider;
//			Auth_Opauth::forge($config);
//
//			return;
//		}

		// セッションからの OAuth プロバイダを取得 (存在する場合)
		$provider = \Session::get('auth-strategy.authentication.provider', false);
	
		// プロバイダ情報を保持している場合、加えてログインフィールドセットも作成
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
						return Response::redirect('');
					}
					else
					{
						$errors[] = 'ユーザー名もしくはメールアドレスもしくはパスワードが間違っています。';
						$errors[] = '入力項目をもう一度よくお確かめください。。';
						Messages::error($errors);
					}
				}
				catch (\Exception $e)
				{
					Messages::error($e->getMessage(), 'エラーが発生しました。');
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
		$this->template->title = 'ログイン';
		$this->template->breadcrumb = array( '/' => 'トップ', '' => $this->template->title );
		$this->template->content = View::forge('auth/signin', $data);
	}

	public function action_signout()
	{
//		$data["subnav"] = array('signout'=> 'active' );
//		$this->template->title = 'Auth &raquo; Signout';
//		$this->template->content = View::forge('auth/signout', $data);

		// remember-me クッキーを削除し、意図的にログアウト
		Auth::dont_remember_me();
		
		// ログアウト
		Auth::logout();
		
		// ログアウトの成功をユーザーに知らせる
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
			return Response::redirect();
		}
		else
		{
			return Response::redirect_back();
		}
	}

	public function get_activate($activate_hash)
	{
		if (!$activate_hash)
		{
			throw new HttpNotFoundException;
		}

		// アクティベーションハッシュからユーザーを捜す
		$userid = Auth::get_id_by_profile_field('activate_hash', $activate_hash);
		$hash_expire = 1 != count($userid) ? 0 : Auth::get_metadata_by_id($userid[0], 'activate_hash_expire', 0);

		if ($hash_expire <= time())
		{// 0 or 2以上のユーザーに関連付けられていたら何かおかしい
			$this->template->title = 'アクティベーション失敗';
			$this->template->breadcrumb = array( '/' => 'トップ', '' => $this->template->title );
			$this->template->content = View::forge('auth/activation_expired');
			return;
		}

		$userid = $userid[0];

		// 登録完了

		Auth::force_login($userid);

		// メール送信
		Model_User::send_activated_mail();

		Session::set('registration_success', 'definitive');

		return Response::redirect();
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
		// 呼び出すための OAuth プロバイダを持っていない場合は出て行く
		if ($provider === null)
		{
			Messages::error('プロバイダの指定がありません。');
			return Response::redirect_back();
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
		// Opauth は厄介な雑用のすべての種類を投げることができます、さあ、準備ができました
		try
		{
			// Opauth オブジェクトを取得
			$opauth = \Auth_Opauth::forge(false);

			// そして、コールバックを処理
			$status = $opauth->login_or_register();
Log::debug($status);

			// メッセージを表示できるように opauth 応答からプロバイダ名を取得
			$provider = $opauth->get('auth.provider', '?');
Log::debug(print_r($opauth->get('auth', array()),true));

			if (Auth::is_banned())
			{
				Messages::error(sprintf('deny logged-in, %d was banned', Auth::get_user_id_only()),
				                sprintf('%s でのログインに失敗しました', ucfirst($provider)));
				// Banされているのでログインに失敗したので強制ログアウト
				Auth::instance()->logout(); // なぜか Auth::logout() だとダメ
				// そして、この状態のためのリダイレクト URL を設定
				$url = '';
			}
			else
			{
				// コールバック処理の結果を扱う
				switch ($status)
				{
				// ローカルのユーザがログインしていて、プロバイダはこのユーザーと関連付けられている
				case 'linked':
					// 関連付けが正常に行われたことをユーザに通知
					Messages::success(sprintf('%s と関連付けを行いました', ucfirst($provider)));
					// そして、この状態のためのリダイレクト URL を設定
					$url = '';
					break;
	
				// 既知のプロバイダへ関連付けられ、そのアカウントでログイン
				case 'logged_in':
					// プロバイダを使用してログインが成功したことをユーザーに通知
					Messages::success(sprintf('%s でログインしました', ucfirst($provider)));
					// そして、この状態のためのリダイレクト URL を設定
					$url = '';
					// 名前をSNSで指定されている物と同期するようになっている場合は更新
					if (Auth::get('fullname_sync_sns', false))
					{
						$fullname = $opauth->get('auth.info.name',
						                         Auth::get('fullname', ''));
						if (empty($fullname) ||
							!Auth::update_user(
								array(
									'fullname' => $fullname
								)))
						{
							Log::warning(sprintf('fullname update failed [%s] (id:%d) new value = "%s"',
							                     ucfirst($provider), Auth::get_user_id_only(),
							                     $fullname));
						}
					}
					break;
	
				// このプロバイダでのログインは知らないので、最初にユーザーにローカルアカウントを作成するように依頼
				case 'register':
					// そして、この状態のためのリダイレクト URL を設定
					$url = 'signup';
					break;
	
				// このプロバイダでのログインは知らなかったが、十分な情報が返されたのでユーザーを自動的に登録した
				case 'registered':
					// プロバイダを使用してログインが成功したことをユーザーに通知、そして、ローカルアカウントが作成された
					Messages::success('アカウントが登録されました');
					// そして、この状態のためのリダイレクト URL を設定
					$url = '';
					break;
	
				default:
					throw new \FuelException('Auth_Opauth::login_or_register() has come up with a result that we dont know how to handle.');
				}
			}

			// リダイレクト先の URL をセット
			return Response::redirect($url);
		}

		// Opauth の例外を処理
		catch (\OpauthException $e)
		{
			Messages::error($e->getMessage());
			return Response::redirect_back('signin');
		}

		// 認証の試みをユーザーが取り消しをしたことを捕捉 (一部のプロバイダはこれを許可)
		catch (\OpauthCancelException $e)
		{
			// おそらく、ここでもう少しきれいな何かをする必要があります...
			exit('It looks like you canceled your authorisation.'.\Html::anchor('users/oath/'.$provider, 'Click here').' to try again.');
		}
	}

	protected function link_provider($userid)
	{
		// 一致する認証ストラテジーを持っているか？
		if ($authentication = \Session::get('auth-strategy.authentication', array()))
		{
			// ストラテジーの呼び出しではなくオブジェクトのインスタンスが必要なので false を渡すことを忘れないでください
			$opauth = \Auth_Opauth::forge(false);
			
			// ローカルユーザーとプロバイダのログインを関連付けるため Opauth を呼び出す
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
