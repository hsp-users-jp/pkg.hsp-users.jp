<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2014 sharkpp
 * @link       http://www.sharkpp.net/
 */

class Controller_Index extends Controller_Base
{
	public function action_dashboard()
	{
		switch (Session::get('registration_success'))
		{
		case 'interim': // 仮登録
			Session::delete('registration_success');
			$this->template->title = '仮登録完了';
			$this->template->content = View::forge('auth/registered_interim', array());
			return;
		case 'definitive': // 本登録
			Session::delete('registration_success');
			$this->template->title = '登録完了';
			$this->template->content = View::forge('auth/registered_definitive', array());
			return;
		}

		$data['recents_top']
			= Model_Package::order_by_recent_update()
				->rows_limit(10)
				->get();

		$data['popular_top']
			= Model_Package::order_by_popular()
				->rows_limit(10)
				->get();

		$this->template->title = 'トップ';
		$this->template->content = View::forge('index/dashboard', $data);
	}

	public function action_about()
	{
		$this->template->title = 'このサイトについて';
		$this->template->breadcrumb = array( '/' => 'トップ', '' => $this->template->title );
		$this->template->content = View::forge('index/about');
	}

	public function action_404()
	{
		Fuel::$profiling = false;
		$this->response_status = '404';
		$this->template->title = '404 Not found!';
		$this->template->breadcrumb = array( '/' => 'トップ', '' => $this->template->title );
		$this->template->content = View::forge('index/404');
	}

	public function get_redirect(/* … */)
	{
		// 使い方：
		//   'index/redirect/hoge/fuga' で 'hoge/fuga' に転送

		$url = implode('/', func_get_args());

		return Response::redirect($url, 'refresh');
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
