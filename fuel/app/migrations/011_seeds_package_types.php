<?php

namespace Fuel\Migrations;

class Seeds_package_types
{
	public function up()
	{
		$data = array(
					array('name' => '拡張プラグイン', 'icon' => 'fa fa-bolt'),
					array('name' => 'モジュール',    'icon' => 'fa fa-file-text'),
					array('name' => 'ツール',       'icon' => 'fa fa-wrench'),
					array('name' => 'サンプル',     'icon' => 'fa fa-code'),
				);
		\DB::start_transaction();
		foreach ($data as $row)
		{
			$model = new \Model_Package_type();
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
		\DBUtil::truncate_table(\Model_Package_type::table()); 
	}
}
