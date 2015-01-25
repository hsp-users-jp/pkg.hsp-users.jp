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

class Create_packages
{
	public function up()
	{
		\DBUtil::create_table('packages', array(
			'revision_id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'id' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
			'package_type_id' => array('constraint' => 11, 'type' => 'int'),
			'user_id' => array('constraint' => 11, 'type' => 'int'),
			'name' => array('type' => 'text'),
			'path' => array('constraint' => 256, 'type' => 'varchar'),
			'original_name' => array('constraint' => 256, 'type' => 'varchar'),
			'version' => array('constraint' => 20, 'type' => 'varchar'),
			'license_id' => array('constraint' => 11, 'type' => 'int'),
			'url' => array('type' => 'text'),
			'description' => array('type' => 'text'),
			'comment' => array('type' => 'text'),
			'created_at' => array('type' => 'timestamp', 'null' => true),
			'updated_at' => array('type' => 'timestamp', 'null' => true),
			'deleted_at' => array('type' => 'timestamp', 'null' => true),

		), array('revision_id'));
	}

	public function down()
	{
		\DBUtil::drop_table('packages');
	}
}