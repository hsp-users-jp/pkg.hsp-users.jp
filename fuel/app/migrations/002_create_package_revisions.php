<?php

namespace Fuel\Migrations;

class Create_package_revisions
{
	public function up()
	{
		\DBUtil::create_table('package_revisions', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'package_base_id' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'package_type_id' => array('constraint' => 11, 'type' => 'int'),
			'user_id' => array('constraint' => 11, 'type' => 'int'),
			'name' => array('type' => 'text'),
			'path' => array('constraint' => 256, 'type' => 'varchar'),
			'original_name' => array('constraint' => 256, 'type' => 'varchar'),
			'version' => array('constraint' => 20, 'type' => 'varchar'),
			'license_id' => array('constraint' => 11, 'type' => 'int'),
			'url' => array('type' => 'text'),
			'description' => array('type' => 'text'),
			'created_at' => array('type' => 'timestamp', 'null' => true),
			'updated_at' => array('type' => 'timestamp', 'null' => true),
			'deleted_at' => array('type' => 'timestamp', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('package_revisions');
	}
}