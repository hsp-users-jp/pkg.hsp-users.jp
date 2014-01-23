<?php

class Controller_Index extends Controller_Base
{

	public function action_dashboard()
	{
		$data['recents_top']
			= Model_Package::order_by_recent_update()
				->related('common')
				->limit(10)
				->get();

		$this->template->title = 'ダッシュボード';
		$this->template->content = View::forge('index/dashboard', $data);
	}

	public function action_about()
	{
		$data = array();
		$this->template->title = 'このサイトについて';
		$this->template->content = View::forge('index/about', $data);
	}

	public function action_404()
	{
		$data["subnav"] = array('dashboard'=> 'active' );
		$this->template->title = 'Index &raquo; Dashboard';
		$this->template->content = View::forge('index/404', $data);
	}

	public function get_redirect(/* … */)
	{
		// 使い方：
		//   'index/redirect/hoge/fuga' で 'hoge/fuga' に転送

		$url = implode('/', func_get_args());

		Response::redirect($url, 'refresh');
	}

	public function get_ajax()
	{
Log::debug(print_r(Input::get(),true));
		if (Input::get('t'))
		{
			switch (!Input::is_ajax() ?: Input::get('t'))
			{
			default:
				throw new HttpNotFoundException;
			case 'package.type':
				$data = array();
				foreach (Model_Package_Type::query()
							->get() as $row)
				{
					$data[] = array(
							'value' => $row->id,
							'text' => $row->name,
						);
				}
				break;
			case 'package.license':
				$data = array();
				foreach (Model_License::query()
							->get() as $row)
				{
					$data[] = array(
							'value' => $row->id,
							'text' => $row->name,
						);
				}
				break;
			}
			$json = Format::forge($data)->to_json();
			$headers = array (
				'Pragma'            => 'no-cache',
			);
			return Response::forge($json, 200, $headers);
		}
	}
}
