<?php

class Model_Package_Upload
{
	private $path;

	public static function forge($path)
	{
		return new self($path);
	}

	function __construct($path)
	{
		$this->path = $path;
	}

	public function analyze()
	{
		$result = array();

		// パッケージから情報を取得

		if (preg_match('/^.+?\.(as|hsp)$/', basename($this->path)))
		{
			// 仕様は
			//   http://www.onionsoft.net/hsp/v33/doclib/HSP%20Document%20Library/hdl_usage.htm
			//     の「ドキュメント付けされたヘッダファイル」
			//   http://www.onionsoft.net/hsp/v33/doclib/HSP%20Document%20Library/HS_BIBLE.txt
			// を参考
			// 
			// HS_BIBLE.txt
			// > フィールドタグには、下記の種類があります。
			// > 
			// >   (タグ)    (内容)
			// > ・%index    シンボル名, 見出し
			// > ・%prm      パラメータリスト, パラメータ説明文
			// > ・%inst     解説文
			// > ・%sample   サンプルスクリプト
			// > ・%href     関連項目
			// > ・%dll      使用プラグイン/モジュール
			// > ・%ver      バージョン
			// > ・%date     日付
			// > ・%author   著作者
			// > ・%url      関連 URL
			// > ・%note     備考 (補足情報等)
			// > ・%type     タイプ
			// > ・%group    グループ
			// > ・%port     対応環境
			// > ・%portinfo 移植のヒント

			// とりあえず、仕様を斜め読んだ感じで実装
			// あとで直そう @todo
			// 1. '/*' から '%*/' までを全て取得
			// 2. 行頭 '%' から 次の '%' までを取得し、タグと内容に分ける
			// 3. 内容をtrim

			$tmp = @ file_get_contents($this->path) ?: '';
			$tmp = mb_convert_encoding($tmp, 'UTF-8', 'SJIS-win');
			$tmp = str_replace("\r", "\n", str_replace("\r\n", "\n", $tmp));
Log::debug($this->path);

			$fields = array();
			if (preg_match_all('=/\*.+?\*/=ms', $tmp, $m))
			{
				foreach ($m[0] as $block_comments)
				{
					if (preg_match_all('=^(%[^%][^\s\n]+)\s*\n((?:(?!\n%).)*)=ms',
					                   $block_comments, $mm))
					{
						for ($i = 0; $i < count($mm[0]); ++$i)
						{
							if ('%rem' == strtolower($mm[1][$i])) {
								break;
							}
							$fields[] = array(
									'tag' => strtolower($mm[1][$i]),
									'text' => trim($mm[2][$i]),
								);
						}
					}
				}
			}
Log::debug(print_r($fields,true));

			foreach ($fields as $field)
			{
				switch ($field['tag'])
				{
				case '%index':
					break 2;
				case '%url';
					$result['url'] = $field['text'];
					break;
				case '%ver';
					$result['version'] = $field['text'];
					break;
				case '%inst';
					$result['description'] = $field['text'];
					break;
				case '%dll';
					$result['title'] = $field['text'];
					break;
				}
			}

			if ($package_type = Model_Package_Type::query()
									->where('name', 'モジュール')
									->get_one())
			{
				$result['package_type'] = $package_type->id;
			}
		}

		return $result;
	}
}
