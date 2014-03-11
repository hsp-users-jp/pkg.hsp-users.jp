<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2014 sharkpp
 * @link       http://www.sharkpp.net/
 */

namespace Fuel\Migrations;

class Seeds_hsp_categories
{
	public function up()
	{
		$data = array(
					array('name' => 'HSP 3',    'icon' => 'fa fa-windows', 'description' => 'HSP 3'),
					array('name' => 'HSP 2',    'icon' => 'fa fa-windows', 'description' => 'HSP 2'),
					array('name' => 'HSP3Dish', 'icon' => 'fa fa-tablet',  'description' => 'Android/iOS/Windows向けランタイム'),
				);
		\DB::start_transaction();
		foreach ($data as $row)
		{
			$model = new \Model_Hsp_Category();
			foreach ($row as $key => $val)
			{
				$model[$key] = $val;
			}
			$model->save();
		}
		\DB::commit_transaction();
	}

	public function down()
	{
		\DBUtil::truncate_table(\Model_Hsp_Category::table()); 
	}
}
