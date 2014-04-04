<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2014 sharkpp
 * @link       http://www.sharkpp.net/
 */

use AspectMock\Test as test;

/**
 * Model Package class tests
 * 
 * @group App
 * @group Controller
 */
class Test_Controller_Auth extends Test_MyTestCase
{
	static protected $csrf_token_key = '';

	protected function teardown()
	{
		self::$csrf_token_key = Config::get('security.csrf_token_key');

	//	test::clean(); // 登録したテストダブルをすべて削除

		parent::teardown();
	}

	/**
	 * @name 新規登録のテスト
	 */

	/**
	 * @name ログイン失敗？のテスト
	 */
	public function test_login_fail()
	{
		$redirect_ = test::double('Fuel\\Core\\Response', ['redirect' => true]);
	//	test::double('Fuel\\Core\\Request', ['main' => null]); // Request::forge()で同じ引数を複数回実行できるようにするため
		test::double('Fuel\\Core\\Security', ['check_token' => true]);

	//	$response
	//		= Request::forge('signin')
	//			->set_method('GET')
	//			->execute()
	//			->__toString();
	//
	//	$csrf_token = qp(HTML5::loadHTML($response), 'input[name="'.self::$csrf_token_key.'"]')->attr('value');
	//
	//	$this->assertNotEmpty( $csrf_token );
		$csrf_token=''; //見ていないので適当に


		$_POST = array(
						self::$csrf_token_key => $csrf_token,
						'username' => 'fuga',
						'password' => 'fuga',
					);
		$response
			= Request::forge('signin')
				->set_method('POST')
				->execute($_POST)
				->__toString();

		// エラーメッセージが出るはず
		$this->assertNotEmpty( qp(HTML5::loadHTML($response), 'div[class="alert alert-danger"] p')->text() );

		// エラーなのでリダイレクトされないはず
		$redirect_->verifyNeverInvoked('redirect', ['']);
	}

	/**
	 * @name ログイン成功？のテスト
	 */
	public function test_login_success()
	{
		$redirect_ = test::double('Fuel\\Core\\Response', ['redirect' => true]);
	//	test::double('Fuel\\Core\\Request', ['main' => null]); // Request::forge()で同じ引数を複数回実行できるようにするため
		test::double('Fuel\\Core\\Security', ['check_token' => true]);

		$csrf_token=''; //見ていないので適当に

		$_POST = array(
						self::$csrf_token_key => $csrf_token,
						'username' => 'admin',
						'password' => 'admin',
					//	'remember_me' => '',
					);
		$response
			= Request::forge('signin')
				->set_method('POST')
				->execute($_POST)
				->__toString();

//		// エラーメッセージはでない
		$this->assertEmpty( qp(HTML5::loadHTML($response), 'div[class="alert alert-danger"] p')->text() );

		$redirect_->verifyInvoked('redirect', ['']);
	}

	/**
	 * @name ログインページは非ログインのみアクセスできるのテスト
	 */
	public function test_login_skip_by_already_loggdin()
	{
		$redirect_ = test::double('Fuel\\Core\\Response', ['redirect' => true]);
		test::double('Fuel\\Core\\Security', ['check_token' => true]);
		test::double('Auth', ['check' => true]);

		$response
			= Request::forge('signin')
				->set_method('GET')
				->execute()
				->__toString();

		// ログイン済みなので即リダイレクトされる
		$redirect_->verifyInvoked('redirect', ['']);
	}

	/**
	 * @name 登録ページは非ログインのみアクセスできるのテスト
	 */
	public function test_signup_skip_by_already_loggdin()
	{
		$redirect_ = test::double('Fuel\\Core\\Response', ['redirect' => true]);
		test::double('Fuel\\Core\\Security', ['check_token' => true]);
		test::double('Auth', ['check' => true]);

		$response
			= Request::forge('signup')
				->set_method('GET')
				->execute()
				->__toString();

		// ログイン済みなので即リダイレクトされる
		$redirect_->verifyInvoked('redirect', ['']);
	}

	/**
	 * @name 既に登録済みのユーザーは登録できないのテスト
	 */
	public function test_signup_failure_by_bad_password()
	{
		$redirect_ = test::double('Fuel\\Core\\Response', ['redirect' => true]);
		test::double('Fuel\\Core\\Security', ['check_token' => true]);

		$csrf_token=''; //見ていないので適当に

		$_POST = array(
						self::$csrf_token_key => $csrf_token,
						'username' => 'admin',
						'password' => '',
						'password2' => '',
						'fullname' => 'Administrator',
						'email' => 'admin@example.net',
					);
		$response
			= Request::forge('signup')
				->set_method('POST')
				->execute($_POST)
				->__toString();

		// エラーメッセージが出るはず
		$this->assertNotEmpty( qp(HTML5::loadHTML($response), 'div[class="alert alert-danger"] p')->text() );

		// エラーなのでリダイレクトされないはず
		$redirect_->verifyNeverInvoked('redirect', ['']);
	}

	/**
	 * @name 既に登録済みのユーザーは登録できないのテスト
	 */
	public function test_signup_failure_by_missmatch_password()
	{
		$redirect_ = test::double('Fuel\\Core\\Response', ['redirect' => true]);
		test::double('Fuel\\Core\\Security', ['check_token' => true]);

		$csrf_token=''; //見ていないので適当に

		$_POST = array(
						self::$csrf_token_key => $csrf_token,
						'username' => 'admin',
						'password' => 'admin',
						'password2' => 'admin1',
						'fullname' => 'Administrator',
						'email' => 'admin@example.net',
					);
		$response
			= Request::forge('signup')
				->set_method('POST')
				->execute($_POST)
				->__toString();

		// エラーメッセージが出るはず
		$this->assertNotEmpty( qp(HTML5::loadHTML($response), 'div[class="alert alert-danger"] p')->text() );

		// エラーなのでリダイレクトされないはず
		$redirect_->verifyNeverInvoked('redirect', ['']);
	}

	/**
	 * @name 既に登録済みのユーザーは登録できないのテスト
	 */
	public function test_signup_failure_by_dup_user()
	{
		$redirect_ = test::double('Fuel\\Core\\Response', ['redirect' => true]);
		test::double('Fuel\\Core\\Security', ['check_token' => true]);

		$csrf_token=''; //見ていないので適当に

		$_POST = array(
						self::$csrf_token_key => $csrf_token,
						'username' => 'admin',
						'password' => 'admin',
						'password2' => 'admin',
						'fullname' => 'Administrator',
						'email' => 'admin@example.net',
					);
		$response
			= Request::forge('signup')
				->set_method('POST')
				->execute($_POST)
				->__toString();

		// エラーメッセージが出るはず
		$this->assertNotEmpty( qp(HTML5::loadHTML($response), 'div[class="alert alert-danger"] p')->text() );

		// エラーなのでリダイレクトされないはず
		$redirect_->verifyNeverInvoked('redirect', ['']);
	}


	/**
	 * @name 既に登録済みのメールアドレスは登録できないのテスト
	 */
	public function test_signup_failure_by_dup_email()
	{
		$redirect_ = test::double('Fuel\\Core\\Response', ['redirect' => true]);
		test::double('Fuel\\Core\\Security', ['check_token' => true]);

		$csrf_token=''; //見ていないので適当に

		$_POST = array(
						self::$csrf_token_key => $csrf_token,
						'username' => 'hoge',
						'password' => 'fuga',
						'password2' => 'fuga',
						'fullname' => 'Administrator',
						'email' => 'admin@example.org',
					);
		$response
			= Request::forge('signup')
				->set_method('POST')
				->execute($_POST)
				->__toString();

		// エラーメッセージが出るはず
		$this->assertNotEmpty( qp(HTML5::loadHTML($response), 'div[class="alert alert-danger"] p')->text() );

		// エラーなのでリダイレクトされないはず
		$redirect_->verifyNeverInvoked('redirect', ['']);
	}

}

