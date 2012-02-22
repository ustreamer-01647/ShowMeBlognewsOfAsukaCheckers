<?php
// ----------------------------------------------------------------------
/*
	rss aggregate
*/

// ----------------------------------------------------------------------
// 出力設定クラス
class OutputSpec{
	var $id; // 識別子
	var $viewFilename; // 閲覧ページ
	var $articlesFilename; // 記事本体
	var $listFilename; // 記事リスト
	var $feedItemLimit; // 記事最大数
	var $dontStripTag; // 除去しないタグ
	
	function __construct ( $_id, $_viewFilename, $_articlesFilename, $_listFilename
		, $_feedItemLimit, $_dontStripTag = null )
	{
		$this->id = $_id;
		$this->viewFilename = $_viewFilename;
		$this->articlesFilename = $_articlesFilename;
		$this->listFilename = $_listFilename;
		$this->feedItemLimit = $_feedItemLimit;
		$this->dontStripTag = $_dontStripTag;
	}
}

/*
記事本体，リスト，striptag，閲覧ページ
*/
// ----------------------------------------------------------------------
// 表示

// ページタイトル
define( "PageTitle", "アスカチェッカー民ブログ新着情報" );

// ----------------------------------------------------------------------
// ファイルとディレクトリ

// 閲覧用ファイル
define( "ViewFilename", "index.php" );

// 実行ファイル
define( "GeneratorFilename", "generate.php" );

// キャッシュディレクトリ（書込権限必要）
define( "CacheLocation", "cache" );

// 実行記録ファイル
define( "LogFilename", "executelog.txt");

// ----------------------------------------------------------------------
// 出力仕様設定

$outputSpec[] = new OutputSpec("heavy", "Hview.php", "Harticles.inc.php" , "Hlist.inc.php", 10
	, array( "object", "param", "embed", "iframe" ));
$outputSpec[] = new OutputSpec("light", "Lview.php", "Larticles.inc.php" , "Llist.inc.php", 20);

// ----------------------------------------------------------------------
// フィード入出力設定

// 読み込むフィード
$feedSources = array(
	"http://www15.atwiki.jp/asuka-ch/rss10_new.xml",// wiki
	"http://loda.jp/asukach/index.xml",// loda
	"http://kazuhiroyahoo.take-uma.net/ATOM/",//kazuhiro yahoo
	"http://galliverion.blog.fc2.com/?xml",//gariver
	"http://suzukix.seesaa.net/index20.rdf",//butaman
	"http://heiho0zikkyou0box.blog34.fc2.com/?xml",//heihou
	"http://asukachanneler.blog.fc2.com/?xml",// asukach-ler
	"http://rssblog.ameba.jp/hukufukuaki/rss20.xml", // hatarakuruma
	"http://karasu0504.blog.fc2.com/?xml",//karasu
	"http://oblate01.blog.fc2.com/?xml",// motsuko
	"http://honji37744.blog.fc2.com/?xml",// honji-
	"http://zarame42715.game-ss.com/ATOM/",// zarame
	"http://blog.livedoor.jp/saro01836/atom.xml",// saro
	"http://01647.tumblr.com/rss",// paul
	"http://nana4ch.blog.fc2.com/?xml", // nns774
	"http://ux.getuploader.com/asukach_01/rss", // asukach loda ( kasagiri )
	"http://leon11020.exblog.jp/index.xml", // leon11010
);
// フィードあたりアイテム数上限
define( "FeedItemLimit", 10);

// ページトップへ戻るリンク
define( "LinkToPageTop", '<p><a href="#top">このページの上端へ戻る</a></p>' );

// フィードキャッシュ有効時間（秒）
define( "CacheDuration", 60*60 );

// 未来の記事を除去する
define( "IgnoreFuture", TRUE);

// seesaaの広告を除去する
define( "IgnoreSeesaaAds", TRUE );

// amebloの広告を除去する
define( "IgnoreAmebloAds", TRUE );

// ----------------------------------------------------------------------
// 実行タイミング管理

// 連続実行拒否時間（秒）を経過しない限り，プログラムを完走させない
define( "IgnoreSpan", 60*60);

// ----------------------------------------------------------------------
// ライブラリ

// プログラム開発時に使用したSimplePieのバージョン番号は1.2
require_once( "simplepie.inc");

// ----------------------------------------------------------------------
// 内部メッセージ

// 実行間隔制限メッセージ
define ( "MsgWaitAMinitue", "Wait for ".IgnoreSpan." second(s)!" );

// 完走報告メッセージ
define ( "MsgFinished", 'Finished! View <a href="'.ViewFilename.'">'.ViewFilename );

?>
