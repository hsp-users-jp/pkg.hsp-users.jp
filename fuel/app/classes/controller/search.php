<?php

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
				->related('common')
				->related('version')
				->related('user')
				->order_by('version.created_at', 'desc')
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
				if (1 < count($q))
				{
					$query = $query->and_where_open();
				}

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
								->or_where('common.package_type_id', $package_type->id);
						}
					//		$query = $query
					//			->or_where('common.package_type_id', 3);
						break;
					case 'author':
						list(,$param) = explode(':', $q_or);
						foreach (Auth::get_id_by_profile_field('fullname', $param) as $userid)
						{
							$query = $query
								->or_where('user_id', $userid);
						}
						break;
					default:
						$query = $query
							->or_where('common.description', 'like', '%'.$q_or.'%')
							->or_where('version.version', 'like', '%'.$q_or.'%')
							;
					}
				}

				if (1 < count($q))
				{
					$query = $query->and_where_close();
				}
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

	public function action_auther()
	{
		$data["subnav"] = array('auther'=> 'active' );
		$this->template->title = 'Search &raquo; Auther';
		$this->template->content = View::forge('search/auther', $data);
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
