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

class Create_package_screenshots
{
	public function up()
	{
		\DBUtil::create_table('package_screenshots', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name' => array('constraint' => 256, 'type' => 'varchar'),
			'width' => array('constraint' => 11, 'type' => 'int'),
			'height' => array('constraint' => 11, 'type' => 'int'),
			'title' => array('type' => 'text'),
			'description' => array('type' => 'text'),
			'created_at' => array('type' => 'timestamp', 'null' => true),
			'updated_at' => array('type' => 'timestamp', 'null' => true),
			'deleted_at' => array('type' => 'timestamp', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('package_screenshots');
	}
}