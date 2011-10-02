<?php
// ----------------------------------------------------------------------
/*
	rss aggregate
*/
// ----------------------------------------------------------------------

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

// 記事ファイル
define( "ArticlesFilename", "articles.inc.php" );

// 記事リストファイル
define( "ListFilename", "list.inc.php" );

// 実行記録ファイル
define( "LogFilename", "executelog.txt");

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
);

// フィードあたりアイテム数上限
define( "FeedItemLimit", 10);

// 出力フィードアイテム数上限
define( "OutputFeedItemLimit", 20 );

// ページトップへ戻るリンク
define( "LinkToPageTop", '<a href="#top">このページの上端へ戻る</a>' );

// フィードキャッシュ有効時間（秒）
define( "CacheDuration", 60*60 );

// 未来の記事を除外する
define( "IgnoreFuture", TRUE);

// seesaaの広告を除外する
define( "IgnoreSeesaaAds", TRUE );

// loda.jp permalinkの，最初の%3Dを=に置換する
define( "ReplaceLodaJpPermalink", TRUE );

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