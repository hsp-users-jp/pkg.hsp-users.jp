# pkg.hsp-users.jp

## やりたいこと

* パッケージの管理
* (wiki)

### パッケージの管理でやりたいこと

* 管理対象
  * モジュール
  * プラグイン
  * ツール
  * サンプル
* パッケージの登録
  * ~~登録ユーザー管理が必要~~
  * 登録含め、3ステップぐらいで公開できるようにしたい
    * アップロードもドロップできるように！
    * userscript.net風？
    * 登録含め難しい？
* パッケージの検索
  * 全文検索
    * ~~パッケージの紹介文~~
    * 対応バージョン(3.1/Dish/hspclとか)
    * 対応OS(Windows/Dish-Android/Dish-iOSとか)
    * コメント
    * プラグインとかの場合、書庫の中まで？
      * ドキュメントのメールアドレスが表示されないようにすべき？
* 登録ユーザー
  * 認証
    * パスワード認証
    * ~~Twitter~~ / Facebook / github
    * SoupSeed←連携できる？
  * パッケージの管理
  * wiki書き込みとの連携  

### パッケージの管理での問題点の列挙

* ページのホスティングは？
  * ~~余ってるSaaSのアカウントを使う？~~
    * 静的なデータを保存する領域は無いからなんとかしないと
      * Amazon？ Azure？ データの引き出しは遅いのでローカルにキャッシュを持たないと厳しいかも？
  * ~~Amazonクラウド？~~
  * ~~サクラのクラウド？~~
  * ~~Google Apps？~~
  * VPS？
    * セキュリティを見ないと行けないので大変？
      * そのかわり何でも出来る
* ウイルス対策は？
  * VirusTotalのAPIを使う
    * 利用規約はOK？
    * アフェリエイトは使っても大丈夫？
* パッケージの保持のディスク容量は？
  * どこに保存する？
  * 一ユーザー辺りの容量制限をする？
  * リンクにすればいい？
    * ウイルス対策はとらなくても良い？
    * 一定容量以上の場合はリンクできるように
* 定期的に実行してほしい処理がある(cronみたいな仕組みがあるサーバーじゃないと駄目)
  * ウイルスチェック
    * 登録時でOKかも
  * バージョンアップチェック(やらないかも)

## メモ

* Model_Temporal だと
  * 部分削除が出来ない？→最新でなければ問題ない
  * 最新の削除を行うのがややこしい
  * 特定リビジョンへのIDが存在しない
* package:revision方式(２テーブル、1:多で管理)
  * 削除は可能
  * 最新の取得がややこしい
    * 別のモデルクラスを作って管理すればok？
* 独自履歴管理(Model_Temporal風)
  * 削除は問題ない
  * ユニークなIDを付けての追加が難しい

## サイト

### ほかサイトの名称と略称

perl
	CPAN
		The Comprehensive Perl Archive Network
		    ~             ~    ~       ~      
php
	PEAR
		PHP Extension and Application Repository
		~   ~             ~           ~         
	PECL
		The PHP Extension Community Library
		    ~   ~         ~         ~      
ruby
	RubyGems

Python
	PyPI
		the Python Package Index
		    ~~     ~       ~    



* 誰向け？
  * 開発者
  * ユーザー
* 何を提供？
  * 開発者には
    * モジュールのパッケージ管理
    * プラグインのパッケージ管理
    * 開発者向けのツール
  * ユーザーには
    * ゲーム
    * アプリケーション

### パッケージ管理コマンド＆略号案

	hucn
		hsp user community network
	hcn
		hsp community network
	CHAN
		the Comprehensive HSP Archive Network
	hpi
		the HSP Package Index
	hpa
		the HSP Package Archives
	hps
		the HSP Package System

### ドメイン案

	hspdev.jp
		wiki.hspdev.jp
		chan.hspdev.jp
		hpi.hspdev.jp
		pkg.hspdev.jp

	hspdev.org
		wiki.hspdev.org
		chan.hspdev.org
		hpi.hspdev.org
		pkg.hspdev.org

	hspdev.net
		wiki.hspdev.net
		chan.hspdev.net
		hpi.hspdev.net
		pkg.hspdev.net

	hsp-users.jp
		wiki.hsp-users.jp
		chan.hsp-users.jp
		hpi.hsp-users.jp
		pkg.hsp-users.jp *採用*

wikiは
	http://quasiquote.org/hspwiki/
	とかあるし？

## wiki

後回し
