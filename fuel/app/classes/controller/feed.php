<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2014 sharkpp
 * @link       http://www.sharkpp.net/
 */

class Controller_Feed extends Controller
{
	private function render_rss($data)
	{
		// http://validator.w3.org/feed/

		switch (\Input::extension())
		{
		default:
			throw new HttpNotFoundException;

		case null:
		case 'rss':
			$view = 'feed/rss2';
			$content_type = 'application/rss+xml; charset=utf-8';
			break;

		case 'atom':
			$view = 'feed/atom';
			$content_type = 'application/atom+xml; charset=utf-8';
			break;
		}

		if (empty($data['date']))
		{
			$data['date'] = Date::forge(1294176140)->format("%m-%d-%Y %H:%M:%S");
		}

		$headers = array (
		//	'Pragma'       => 'no-cache',
			'Content-Type' => $content_type,
		);
		return Response::forge(View::forge($view, $data), 200, $headers);
	}

	public function action_recent()
	{
		$data = array(
				'title'       => 'HSP Package DB :: 最近の更新',
				'id'          => sha1(sprintf('id:%s', \Uri::string())),
				'description' => '最近更新されたパッケージの一覧が新しい日付からリストされています',
				'date'        => '', // アイテムループで更新 
				'items'       => array(),
			);

		foreach (Model_Package::order_by_recent_update()
					->related('user')
					->rows_limit(10)
					->get() as $package)
		{
			$item = array(
					'title'       => $package->name,
					'url'         => Uri::create('package/' . $package->id),
					'id'          => sha1(sprintf('id:%s:%d', \Uri::string(), $package->id)),
					'description' => $package->description,
					'subject'     => '',
					'username'    => Auth::get_metadata_by_id($package->user->id, 'fullname', '不明'),
					'date'        => $package->updated_at,
				);
			$data['date'] = $data['date'] < $item['date'] ? $item['date'] : $data['date'];
			$data['items'][] = $item;
		}

		return $this->render_rss($data);
	}

	public function action_popular()
	{
	
		$data = array(
				'title'       => 'HSP Package DB :: 人気のダウンロード',
				'id'          => sha1(sprintf('id:%s', \Uri::string())),
				'description' => '',
				'date'        => '', // アイテムループで更新 
				'items'       => array(),
			);

		foreach (Model_Package::order_by_popular()
					->related('user')
					->rows_limit(10)
					->get() as $package)
		{
			$item = array(
					'title'       => $package->name,
					'url'         => Uri::create('package/' . $package->id),
					'id'          => sha1(sprintf('id:%s:%d', \Uri::string(), $package->id)),
					'description' => $package->description,
					'subject'     => '',
					'username'    => Auth::get_metadata_by_id($package->user->id, 'fullname', '不明'),
					'date'        => $package->updated_at,
				);
			$data['date'] = $data['date'] < $item['date'] ? $item['date'] : $data['date'];
			$data['items'][] = $item;
		}

		return $this->render_rss($data);
	}

	public function action_news()
	{
		$data = array(
				'title'       => 'HSP Package DB :: ニュース',
				'id'          => sha1(sprintf('id:%s', \Uri::string())),
				'description' => '',
				'date'        => '', // アイテムループで更新 
				'items'       => array(),
			);
		return $this->render_rss($data);
	}

	public function action_tags()
	{
		$data = array(
				'title'       => 'HSP Package DB :: タグ',
				'id'          => sha1(sprintf('id:%s', \Uri::string())),
				'description' => '',
				'date'        => '', // アイテムループで更新 
				'items'       => array(),
			);
		return $this->render_rss($data);
	}

}
