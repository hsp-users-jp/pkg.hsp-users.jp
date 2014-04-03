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
class Test_Controller_Auth extends Test_DbTestCase
{
	protected function teardown()
	{
		parent::teardown();

		test::clean(); // 登録したテストダブルをすべて削除

		Input::reset();
		Validation::reset();
		Request::reset();
	}

	/**
	 * @name 新規登録のテスト
	 */

	/**
	 * @name ログイン失敗？のテスト
	 */
	public function test_login_fail()
	{
		$csrf_token_key = Config::get('security.csrf_token_key');

		$redirect_ = test::double('Fuel\Core\Response', ['redirect' => true]);
	//	test::double('Fuel\Core\Request', ['main' => null]); // Request::forge()で同じ引数を複数回実行できるようにするため
		test::double('Fuel\Core\Security', ['check_token' => true]);

	//	$response
	//		= Request::forge('signin')
	//			->set_method('GET')
	//			->execute()
	//			->__toString();
	//
	//	$csrf_token = qp(HTML5::loadHTML($response), 'input[name="'.$csrf_token_key.'"]')->attr('value');
	//
	//	$this->assertNotEmpty( $csrf_token );
		$csrf_token=''; //見ていないので適当に


		$_POST = array(
						$csrf_token_key => $csrf_token,
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
	}

	/**
	 * @name ログイン成功？のテスト
	 */
	public function test_login_success()
	{
		$csrf_token_key = Config::get('security.csrf_token_key');

		$redirect_ = test::double('Fuel\Core\Response', ['redirect' => true]);
	//	test::double('Fuel\Core\Request', ['main' => null]); // Request::forge()で同じ引数を複数回実行できるようにするため
		test::double('Fuel\Core\Security', ['check_token' => true]);

		$csrf_token=''; //見ていないので適当に

		$_POST = array(
						$csrf_token_key => $csrf_token,
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
	 * @name パッケージ追加のテスト
	 */
/*	public function test_append()
	{
//		$response = Request::forge('feed/recent')->execute()->response();
//		$render = $response->send();
//		echo $render;
        // Response::redirect()を単にtrueを返すテストダブルに置き換え
        $req = test::double('Fuel\Core\Response', ['redirect' => true]);

        // 'test/redirect'へのリクエストを生成
        $response = Request::forge('feed/recent')
                        ->set_method('GET')->execute()->response();

        // Response::redirect()が以下の引数で実行されたことを確認
        $req->verifyInvoked('redirect', ['welcome/404', 'location', 404]);
        	}
        	*/
}

