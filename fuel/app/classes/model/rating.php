<?php
/**
 * pkg.hsp-users.jp - HSP Package DB
 *
 * @author     sharkpp
 * @license    MIT License
 * @copyright  2015 sharkpp
 * @link       http://www.sharkpp.net/
 */

class Model_Rating extends Model_Rating_Package
{
	// ユーザーのパッケージに対する評価を更新
	static public function update_sore($user_id, $package_id, $score)
	{
		$score = (int)$score;

		// 評価を更新
		$rating_user =
			Model_Rating_User::query()
				->where('user_id', $user_id)
				->where('package_id', $package_id)
				->get_one();
		if ($score < 1 || 5 < $score)
		{ // 値が 1 - 5 の範囲に無ければスコアを削除する
			if( $rating_user)
			{
				$rating_user->delete();
			}
		}
		else
		{ // 値を更新
			if( !$rating_user)
			{
				$rating_user = new Model_Rating_User;
				$rating_user->user_id    = $user_id;
				$rating_user->package_id = $package_id;
			}
			$rating_user->rating = $score;
			$rating_user->save();
		}

		// パッケージに対する評価を更新
		$subQuery =
			DB::select(DB::expr('AVG(rating)'))
				->from(Model_Rating_User::table())
				->where(Model_Rating_User::table().'.package_id', $package_id);
		DB::update(Model_Rating_Package::table())
			->value('rating', $subQuery)
			->where('package_id', $package_id)
			->execute();

		// 変更結果を取得
		$rating_package =
			Model_Rating_Package::query()
				->where('package_id', $package_id)
				->get_one();
		return $rating_package ? $rating_package->rating : 0;
	}
}
