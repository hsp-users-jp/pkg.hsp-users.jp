<?php

namespace Fuel\Migrations;

class Seeds_licenses
{
	public function up()
	{
		$data = array(
					array('name' => 'GPLv3', 'description' => 'GNU一般公衆ライセンス (GPL) バージョン3', 'url' => 'http://www.gnu.org/licenses/gpl-3.0.en.html'),
					array('name' => 'GPLv2', 'description' => 'GNU一般公衆ライセンス (GPL) バージョン2', 'url' => 'http://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html'),
					array('name' => 'LGPLv3', 'description' => 'GNU劣等一般公衆ライセンス (LGPL) バージョン3', 'url' => 'http://www.gnu.org/licenses/lgpl-3.0.en.html'),
					array('name' => 'LGPLv2.1', 'description' => 'GNU劣等一般公衆ライセンス (LGPL) バージョン2.1', 'url' => 'http://www.gnu.org/licenses/old-licenses/lgpl-2.1.en.html'),
					array('name' => 'Apache License', 'description' => 'Apacheソフトウェア財団 (ASF) が作成したソフトウェア向けライセンス規定。GPLv3と互換。', 'url' => 'http://www.apache.org/licenses/'),
					array('name' => 'MIT License', 'description' => '非コピーレフト、BSDスタイルのライセンス。', 'url' => 'http://sourceforge.jp/projects/opensource/wiki/licenses%2FMIT_license'),
					array('name' => '修正BSDライセンス', 'description' => '非コピーレフト、BSDスタイルのライセンス。', 'url' => 'http://sourceforge.jp/projects/opensource/wiki/licenses%2Fnew_BSD_license'),
					array('name' => '二条項BSDライセンス', 'description' => '非コピーレフト、BSDスタイルのライセンス。', 'url' => 'http://opensource.org/licenses/BSD-2-Clause'),
					array('name' => 'zlib license', 'description' => '緩やかなフリーソフトウェアライセンス', 'url' => 'http://zlib.net/zlib_license.html'),
					array('name' => 'PDS', 'description' => 'パブリックドメインソフトウェア', 'url' => 'https://www.gnu.org/philosophy/categories.en.html#PublicDomainSoftware'),
					array('name' => 'NYSL', 'description' => '煮るなり焼くなり好きにしろライセンス', 'url' => 'http://www.kmonos.net/nysl/'),
					array('name' => 'その他', 'description' => '', 'url' => ''),
				);
		\DB::start_transaction();
		foreach ($data as $row)
		{
			$model = new \Model_License();
			foreach ($row as $key => $val)
			{
				$model[$key] = $val;
			}
			$model->save();
		}
		\DB::commit_transaction();
	}

	public function down()
	{
		\DBUtil::truncate_table(\Model_License::table()); 
	}
}
