<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2014 sharkpp
 * @link       http://www.sharkpp.net/
 */

class Test_MyTestCase extends TestCase
{
	protected $tables = array();

	protected function setup()
	{
		parent::setup();

		// マイグレーション処理を全て実行してデータベースを構築
//		@ unlink(implode(DS, array(APPPATH,'config','test','migrations.php')));
		\Migrate::version(0, 'default', 'app');
		\Migrate::latest('default', 'app');
		\Migrate::version(0, '*', 'package');
		\Migrate::latest('*', 'package');

		if (!empty($this->tables))
		{
			foreach ($this->tables as $table)
			{
				$this->fixit($table);
			}
		}
	}
	
	protected function teardown()
	{
		// 登録したテストダブルをすべて削除
		AspectMock\Test::clean();

		// Fuel\Core クラスの静的変数の状態をリセット
		Input::reset();
		Validation::reset();
		Request::reset();
		Session::reset();

		// Auth パッケージクラスの静的変数の状態をリセット
		Test_Auth::reset(); // ※データベース操作をしているのでマイグレーションより先に処理

		// データベースを空っぽにする
		\Migrate::version(0, 'default', 'app');
		\Migrate::version(0, '*', 'package');

		// 親クラスのメソッドを続けて実行
		parent::teardown();
	}

	protected function fixit($table)
	{
	}
}
