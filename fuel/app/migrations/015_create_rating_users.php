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

class Create_rating_users
{
	public function up()
	{
		\DBUtil::create_table('rating_users', array(
			'user_id' => array('constraint' => 11, 'type' => 'int'),
			'package_id' => array('constraint' => 11, 'type' => 'int'),
			'rating' => array('constraint' => 11, 'type' => 'int'),

		), array('user_id', 'package_id'));
	}

	public function down()
	{
		\DBUtil::drop_table('rating_users');
	}
}