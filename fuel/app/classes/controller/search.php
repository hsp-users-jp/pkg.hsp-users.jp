<?php

class Controller_Search extends Controller_Base
{

	public function action_index()
	{
		$q = Input::get('q');

		$query
			= Model_Package::query()
				->related('common')
				->related('version')
				;

		if (empty($q))
		{
			$query = $query->where('id', '-1');
		}
		else
		{
			$query = $query
						->or_where('common.description', 'like', '%'.$q.'%')
						->or_where('version.version', 'like', '%'.$q.'%');
		}

		$pagination = Pagination::forge('page', array(
				'total_items' => $query->count(),
				'uri_segment' => 'page',
			));

		$query = $query
				->rows_offset($pagination->offset)
				->rows_limit($pagination->per_page);

		$data['rows'] = $query->get();
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
