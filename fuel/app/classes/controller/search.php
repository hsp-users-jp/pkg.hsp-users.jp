<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2014 sharkpp
 * @link       http://www.sharkpp.net/
 */

class Controller_Search extends Controller_Base
{

	public function action_index()
	{
		$q = Input::get('q');

		// 検索クエリを分解
		$tmp = array();
		$i = 0;
		foreach (explode('"', preg_replace('/(\s+)/', ' ', $q)) as $q_)
		{
			$tmp
				= array_merge(
					$tmp,
					$i%2 ? array('"'.$q_.'"')
					     : explode(' ', trim($q_)));
			$i++;
		}
		$q = array();
		$prev_or = false;
		foreach ($tmp as $q_)
		{
			if (!empty($q_) && '""' != $q_)
			{
				if ('OR' == $q_)
				{
					$prev_or = true;
				}
				else if ($prev_or)
				{
					$q[count($q)-1][] = $q_;
					$prev_or = false;
				}
				else
				{
					$q[] = array($q_);
				}
			}
		}

		$query
			= Model_Package::query()
				->related('user')
				->related('license')
				->order_by('created_at', 'desc')
				;

		// クエリを組み立て
		if (empty($q))
		{
			$query = null;
		}
		else
		{
//			$query = $query
//						->or_where('common.description', 'like', '%'.$q.'%')
//						->or_where('version.version', 'like', '%'.$q.'%');
			foreach ($q as $q_and)
			{
			//	if (1 < count($q))
			//	{
					$query = $query->and_where_open();
			//	}

				foreach ($q_and as $q_or)
				{
					list($func,) = explode(':', $q_or);
					switch ($func)
					{
					case 'type':
						list(,$param) = explode(':', $q_or);
						$package_type
							= Model_Package_Type::query()
							//	->select('id')
								->where('name', $param)
								->get_one();
						if ($package_type)
						{
							$query = $query
								->or_where('package_type_id', $package_type->id);
						}
					//		$query = $query
					//			->or_where('common.package_type_id', 3);
						break;
					case 'author':
						list(,$param) = explode(':', $q_or);
					//	foreach (Auth::get_id_by_profile_field('fullname', $param) as $userid)
					//	{
					//		$query = $query
					//			->or_where('user_id', $userid);
					//	}
						foreach (\Auth\Model\Auth_User::query()
									->where('username', $param)
									->get() as $user)
						{
							$query = $query
								->or_where('user_id', $user->id);
						}
						break;
					default:
						$query = $query
							->or_where('name', 'like', '%'.$q_or.'%')
							->or_where('description', 'like', '%'.$q_or.'%')
							->or_where('version', 'like', '%'.$q_or.'%')
							->or_where('license.name', 'like', '%'.$q_or.'%')
							;
					}
				}

			//	if (1 < count($q))
			//	{
					$query = $query->and_where_close();
			//	}
			}
		}

		$pagination = Pagination::forge('page', array(
				'total_items' => $query ? $query->count() : 0,
				'uri_segment' => 'page',
			));

		if ($query)
		{
			$query = $query
					->rows_offset($pagination->offset)
					->rows_limit($pagination->per_page);
		}
	
		$data['rows'] = $query ? $query->get() : array();
	//	$data['pagination'] = $pagination; // タグを出力するのでエスケープ処理させないため set_safe で追加

		$this->template->title = 'パッケージ一覧';
		$this->template->content = View::forge('search/index', $data);
		$this->template->content->set_safe(array('pagination' => $pagination));
	}

	public function action_package()
	{
		$data["subnav"] = array('package'=> 'active' );
		$this->template->title = 'Search &raquo; Package';
		$this->template->content = View::forge('search/package', $data);
	}

	public function action_author()
	{
		$subQuery
			= DB::select(DB::expr('MAX(revision_id)'))
				->from(Model_Package::table())
				->where('deleted_at', '=', null)
				->group_by('id');
		$authors
			= DB::select(DB::expr('*, COUNT(*) as count_of_packages'))
				->from(\Auth\Model\Auth_User::table());
		if (!Auth::is_super_admin())
		{
			$authors = $authors
				->where('group_id', '!=', Auth::get_group_by_name('Banned')->id)
				->where(Model_Package_Base::table().'.deleted_at', '=', null);
		}
		$authors = $authors
				->join(Model_Package::table(), 'inner')
				->on(\Auth\Model\Auth_User::table().'.id', '=', Model_Package::table().'.user_id')
				->and_on(Model_Package::table().'.revision_id', 'in', DB::expr('('.$subQuery->__toString().')'))
				->join(Model_Package_Base::table(), 'inner')
				->on(Model_Package::table().'.id', '=', Model_Package_Base::table().'.id')
				->group_by(\Auth\Model\Auth_User::table().'.id')
				->execute()
				->as_array()
				;

		$data['authors'] = Prop::forge($authors);

		$data["subnav"] = array('auther'=> 'active' );
		$this->template->title = 'Search &raquo; Author';
		$this->template->content = View::forge('search/author', $data);
	}

	public function action_recent()
	{ // 最近の更新
		$data["subnav"] = array('auther'=> 'active' );
		$this->template->title = 'Search &raquo; Auther';
		$this->template->content = View::forge('search/recent', $data);
	}

	public function action_popular()
	{ // 人気のダウンロード
		$data["subnav"] = array('auther'=> 'active' );
		$this->template->title = 'Search &raquo; Auther';
		$this->template->content = View::forge('search/popular', $data);
	}

}
