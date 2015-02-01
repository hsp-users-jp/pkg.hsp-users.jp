<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2015 sharkpp
 * @link       http://www.sharkpp.net/
 */

namespace Fuel\Migrations;

class Create_rating_packages
{
	public function up()
	{
		\DBUtil::create_table('rating_packages', array(
			'package_id' => array('constraint' => 11, 'type' => 'int'),
			'rating' => array('type' => 'double'),

		), array('package_id'));

		try
		{
			\DB::start_transaction();
			\DB::query(\DB::expr(
					'INSERT INTO rating_packages(package_id) '.
					'SELECT id as package_id '.
					'FROM   package_bases'
				))
				->execute();
			\DB::commit_transaction();
		}
		catch (\Exception $e)
		{ // 例外を握りつぶす
			\DB::rollback_transaction();
			//throw $e;
		}
	}

	public function down()
	{
		\DBUtil::drop_table('rating_packages');
	}
}