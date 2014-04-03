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
class Test_Controller_Index extends Test_DbTestCase
{
	protected function teardown()
	{
		parent::teardown();

		test::clean(); // 登録したテストダブルをすべて削除
	}

	/**
	 * @name ページが見つからないときのテスト
	 */
	public function test_404()
	{
		$response = Request::forge('/')
			->set_method('GET')->execute()->response();

		$this->setExpectedException('Fuel\Core\HttpNotFoundException');
		$response = Request::forge('hoge')
			->set_method('GET')->execute()->response();
	}

// 最近の更新に追加されるか？
//トップページ
//続き

// ダウンロードに追加されるか？
//トップページ
//続き



	/**
	 * @name パッケージ追加のテスト
	 */
	public function test_append()
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
}

