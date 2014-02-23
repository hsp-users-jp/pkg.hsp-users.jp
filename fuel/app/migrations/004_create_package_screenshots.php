<?php

namespace Fuel\Migrations;

class Create_package_screenshots
{
	public function up()
	{
		\DBUtil::create_table('package_screenshots', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'package_revision_id' => array('constraint' => 11, 'type' => 'int'),
			'path' => array('constraint' => 256, 'type' => 'varchar'),
			'title' => array('type' => 'text'),
			'description' => array('type' => 'text'),
			'created_at' => array('type' => 'timestamp', 'null' => true),
			'updated_at' => array('type' => 'timestamp', 'null' => true),
			'deleted_at' => array('type' => 'timestamp', 'null' => true),

		), array('id'));

		// ついでにフォルダも作っておく
		@ mkdir($ss_dir, 0777, true);
	}

	public function down()
	{
		\DBUtil::drop_table('package_screenshots');
	}
}