#!/usr/local/bin/php
<?php
header("Content-type: text/html; charset=utf-8");

chdir("/home/paul/public_html/asukachrss");
// ----------------------------------------------------------------------
// 設定ファイル
require_once("config.inc.php");

// ----------------------------------------------------------------------
// 実行間隔制限
// ファイルロック
/*
前回の実行記録を調べる
前回の実行時刻よりIgnoreSpan秒経過していない場合，処理を中止する
ファイルロック解放はプログラム終端で実施する
*/
// ファイルオープン
$fp = fopen( LogFilename, "r+");
// ファイルロック
@flock( $fp, LOCK_EX );
// 前回の実行日時
$lasttime = intval( fgets( $fp ) );
// 現在日時
$nowtime = time();
// 一定時間経過していない場合，処理を中止する
if( $lasttime + IgnoreSpan > $nowtime )
{
	// ファイル解放
	flock( $fp, LOCK_UN );
	fclose( $fp );
	// メッセージ
	exit( MsgWaitAMinitue );
}
// 現在日時で上書きする
rewind( $fp );
fputs( $fp, $nowtime );

// ----------------------------------------------------------------------
// 変数宣言
$feed = new SimplePie();
$feed->set_feed_url( $feedSources );
$feed->set_cache_location( CacheLocation );
$feed->set_cache_duration( CacheDuration );
$feed->set_item_limit( FeedItemLimit );
$feed->init();
$feed->handle_content_type();


// -----------------------------------------------------------------
// フィードアイテム
// 記事群とそのリストを作る

// 出力数量が1未満だった場合は，全て表示する
/*
if ( OutputFeedItemLimit < 1 )
	$outputFeedItemLimit = $feed->get_item_quantity();
else
	$outputFeedItemLimit = OutputFeedItemLimit;
*/

// itemCounter初期化
$itemCounter = 0;
// 未来日時分岐
// $feedが日時ソートされているから，$itemCounterを加算して対応する
if ( IgnoreFuture )
{
	foreach ($feed->get_items() as $item)
	{
		// アイテムの日時が現在日時よりも大きいとき（未来アイテムのとき）
		if (intval($item->get_date("U")) > $nowtime)
			$itemCounter++;
		// $feedが日時ソートされているため，以降のアイテム検査は省略する
		else
			break;
	}
}
// 出力フィードアイテム数設定
if ( OutputFeedItemLimit < 1 )
{
	// 1未満とされている場合は，全て表示する．get_item_quantityは総数を返す
	$outputFeedItemLimit = $feed->get_item_quantity();
	}else
{
	// 設定数量にアイテムカウンタ増分を加えておいて，総アイテム数かどちらか小さい値を選ぶ
	$outputFeedItemLimit = 
	min( intval(OutputFeedItemLimit)+$itemCounter,
		$feed->get_item_quantity());
}


// ------------------------------------------------------------
// フィードアイテムループ始端
// リストデータ
$listData = "";
// 記事データ
$articlesData;
while ($itemCounter < $outputFeedItemLimit)
{
	// コードを簡潔に表現するため，$item とおく
	$item = $feed->get_item($itemCounter);
	// フィード終端であればループを抜ける
	if ( is_null( $item )	) break;
	// 条件次第でこのアイテムをスキップする
	if ( ignoreItem( $item ))
	{
		$outputFeedItemLimit++;
		$itemCounter++;
		continue;
	}
	// （<a href="#article'.$itemCounter.'">ページ内リンク#'.$itemCounter.'</a>）
	// 投稿日時フォーマット
	$date = $item->get_date("Y年m月j日(D) G時i分");
	// 投稿日時とフィードソース名
	$dateandbase = $date.'投稿 - <a href="'.$item->get_base().'">'.$item->get_feed()->get_title().'</a>';
	// パーマネントリンク
	$permalink = $item->get_permalink();
//	$permalink = processPermalink( $item->get_permalink() );
	// リストデータ追記
	$listData .= '<li><a href="#article'.$itemCounter.'" class="linkinpage">■</a> <a href="'.$permalink.'">'.$item->get_title().'</a><small> - '.$dateandbase.'</small></li>'."\n";
	// 記事データ追記
	$articlesData .= <<<EOT
	<div class="item">
	<h2 class="title"><a href="{$permalink}" name="article{$itemCounter}">{$item->get_title()}</a></h2>
	<p><small>{$dateandbase}</small></p>
	<div class="content">{$item->get_content()}</div>
	</div>
	<p><a href="#top">このページの上端へ戻る</a></p>
EOT;
	// -----------------------------------------------------------------
	// フィードアイテムループ終端
	$itemCounter++;
}

// ----------------------------------------------------------------------
// ファイル出力
file_put_contents( ArticlesFilename, $articlesData, LOCK_EX );
file_put_contents( ListFilename, "<ul class=\"articlelist\">".$listData."</ul>", LOCK_EX );

// ----------------------------------------------------------------------
// 後始末
// 実行時刻記録ファイルロック解放
flock( $fp, LOCK_UN );
fclose( $fp );

// ----------------------------------------------------------------------
// 完走メッセージ
echo 'Finished! Return to <a href="'.ViewFilename.'">Viewpage</a>';

// ----------------------------------------------------------------------
// アイテム無視条件
function ignoreItem( $item )
{
	// seesaa広告除去
	if ( IgnoreSeesaaAds )
	{
		/*
		// SimplePie_ItemのAuthorはフィード統合時に削除されるらしい
		if( !(FALSE === mb_strpos($item->get_author()->get_name(), "ads by Seesaa")) )
			return TRUE;
		*/
		// タイトルを基に判定する
		if( !(FALSE === mb_strpos($item->get_title(), "[PR]注目のキーワード「")) )
			return TRUE;
	}
	
	// ameblo広告除去
	if ( IgnoreAmebloAds )
	{
		// パーマネントリンクを基に判定する
		if( !(FALSE === mb_strpos($item->get_permalink(), "http://rss.rssad.jp/rss/ad/")) )
			return TRUE;
	}

	return FALSE;
}

?>
