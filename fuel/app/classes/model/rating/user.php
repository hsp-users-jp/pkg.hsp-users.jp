<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2015 sharkpp
 * @link       http://www.sharkpp.net/
 */

class Model_Rating_User extends \Orm\Model
{
	protected static $_properties = array(
		'user_id',
		'package_id',
		'rating',
	);

	protected static $_primary_key = array(
		'user_id',
		'package_id',
	);

	protected static $_table_name = 'rating_users';

}
