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
// 出力設定
$feed = new SimplePie();
//$feed->set_max_checked_feeds( count($feedSources) );
$feed->set_feed_url( $feedSources );
$feed->set_cache_location( CacheLocation );
$feed->set_cache_duration( CacheDuration );
$feed->set_item_limit( FeedItemLimit );

foreach ( $outputSpec as $oSpec )
{
	// クローン作成
	$_feed = clone $feed;
	
	// 除去タグ設定
	if ( null != $oSpec->dontStripTag )
	{
		$strip_htmltags = $_feed->strip_htmltags;
		foreach ( $oSpec->dontStripTag as $tag )
		{
			// Remove these tags from the list
			array_splice($strip_htmltags, array_search($tag, $strip_htmltags), 1);
		}
		print_r( $_feed->strip_htmltags );
		$_feed->strip_htmltags = $strip_htmltags;
//		$_feed->strip_htmltags($strip_htmltags);
		print_r( $_feed->strip_htmltags );
	}
	
	build( $_feed, $oSpec );
}

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

// ----------------------------------------------------------------------
// 未来の記事日時をスキップする
function skipFuture ( $feed, $itemCounter )
{
	global $nowtime;
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

// ----------------------------------------------------------------------
// aggregate本体

function build ( $feed, $outputSpec )
{
	// おまじない
	$feed->init();
	$feed->handle_content_type();
	
// -----------------------------------------------------------------
// フィードアイテム
// 記事群とそのリストを作る

	// itemCounter初期化
	$itemCounter = 0;
	// 未来日時記事無視分岐
	// $feedが日時ソートされているから，$itemCounterを加算して対応する
	if ( IgnoreFuture )
	{
		skipFuture ( $feed, $itemCounter );
	}

	// 出力するフィードアイテム番号の範囲を定める
	// itemCounterから（ここで書き換える）feedItemLimitまでを出力する
	if ( $outputSpec->feedItemLimit < 1 )
	{
		// 1未満とされている場合は，全て表示する．get_item_quantityは総数を返す
		$outputSpec->feedItemLimit = $feed->get_item_quantity();
	}else
	{
		// 設定数量にアイテムカウンタ増分を加えておいて，総アイテム数かどちらか小さい値を選ぶ
		$outputSpec->feedItemLimit = 
		min( intval($outputSpec->feedItemLimit)+$itemCounter,
			$feed->get_item_quantity());
	}
	
	// ------------------------------------------------------------
	// フィードアイテムループ始端
	// リストデータ
	$listData;
	// 記事データ
	$articlesData;
	
	while ($itemCounter < $outputSpec->feedItemLimit)
	{
		// コードを簡潔に表現するため，$item とおく
		$item = $feed->get_item($itemCounter);
		// フィード終端であればループを抜ける
		if ( is_null( $item )	) break;
		// 条件次第でこのアイテムをスキップする
		if ( ignoreItem( $item ))
		{
			$outputSpec->feedItemLimit++;
			$itemCounter++;
			continue;
		}
		// 投稿日時フォーマット
		$date = $item->get_date("Y年n月j日(D) G時i分");
		// 投稿日時とフィードソース名
		$dateandbase = $date.'投稿 - <a href="'.$item->get_base().'">'.$item->get_feed()->get_title().'</a>';
		// パーマネントリンク
		$permalink = $item->get_permalink();
		// リストデータ追記
		$listData .= '<li><a href="#article'.$itemCounter.'" class="linkinpage">■</a> <a href="'.$permalink.'">'.$item->get_title().'</a><small> - '.$dateandbase.'</small></li>'."\n";
		// 記事データ追記
		$articlesData .= <<<EOT
<div class="item">
<h2 class="title"><a href="{$permalink}" name="article{$itemCounter}">{$item->get_title()}</a></h2>
<p><small>{$dateandbase}</small></p>
<div class="content">{$item->get_content()}</div>
</div>
EOT;
		$articlesData .= LinkToPageTop;
		
		// -----------------------------------------------------------------
		// フィードアイテムループ終端
		$itemCounter++;
	}
	

	// ----------------------------------------------------------------------
	// ファイル出力
	file_put_contents( $outputSpec->articlesFilename, $articlesData, LOCK_EX );
	file_put_contents( $outputSpec->listFilename, "<ul class=\"articlelist\">".$listData."</ul>", LOCK_EX );

}
?>
