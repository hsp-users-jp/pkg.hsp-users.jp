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
class Test_Controller_Index extends Test_MyTestCase
{
	protected function teardown()
	{
		parent::teardown();

	//	test::clean(); // 登録したテストダブルをすべて削除
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
			->set_method('GET')
			->execute()
			->response();
	}

// 最近の更新に追加されるか？
//トップページ
//続き

// ダウンロードに追加されるか？
//トップページ
//続き

}

