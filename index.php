<?php
	// 設定ファイル
	require_once("config.inc.php");		
	// 最終更新日時
	$lastUpdate = intval(file_get_contents(LogFilename));
	header("Content-type: text/html; charset=utf-8");
	header("Expires: ".gmdate("D, j m Y H:i:s", $lastUpdate+CacheDuration)." GMT"); // Sat, 26 Jul 1997 05:00:00 GMT
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" />
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="../style/style.css" />
	<title><?php echo PageTitle;?></title>
	<style type="text/css">
		.item { margin:5px;padding:5px; border: solid 1px; } table,th,td { border: solid 1px; }
		.articlelist { list-style-type: none; }
		.linkinpage { font-family: monospace; margin-right: 0.5em; }
	</style>
</head>
<body>
	<h1><?php echo PageTitle; ?></h1>
	<p><a href="../">ウェブサイトトップへ戻る</a></p>
	<p><a href="http://asuka--sen-nin.ddo.jp/checker/index.cgi">アスカチェッカー</a>配信者らのブログ新着情報を表示しています．掲載追加拒否承ります．スタイルシートデザインを募集しています．</p>
	<h1>お品書き</h1>
	<p>文字「<span class="linkinpage">■</span>」はこのページ内にリンクしています．記事タイトル部分は各ブログ記事単体ページへリンクしています．</p>
	<ul>
		<li><a href="<?php echo GeneratorFilename; ?>">最新の情報に更新する</a>（最終確認日時は<?php echo date("Y年m月j日(D) G時i分", $lastUpdate);?>）</li>
		<li><a href="#Articles">記事</a></li>
		<?php include_once(ListFilename);?>
		<li><a href="#Spec">入力されているフィードほか各種仕様</a></li>
	</ul>
	<h1><a name="Articles">記事</a></h1>
	<?php include_once(ArticlesFilename);?>
	<h1><a name="Spec">入力されているフィードほか各種仕様</a></h1>
	<p>下記フィードから取得しています．</p>
	<ul>
		<?php
			foreach ( $feedSources as $source )
			{
				echo '<li><a href="'.$source.'">'.$source.'</a></li>';
			}
		?>
	</ul>
	<h2>各種仕様</h2>
	<ul>
		<li>RSS/ATOM読込に<?php echo SIMPLEPIE_LINKBACK; ?>を利用している</li>
		<li>毎日2回，5時半前と17時半前に内容を自動更新している</li>
		<li>generate.phpへアクセスすることで手動更新できる．ただし，次の更新には1時間の猶予が必要</li>
		<li>画像やリンクなどの要素は有効だが，Javascriptなどは無効化している．たとえば，動画埋込がここでは無効化される．無効化される要素は<a href="http://simplepie.org/wiki/reference/simplepie/strip_htmltags">SimplePie Documentation: strip_htmltags()</a>に記述されている．</li>
	</ul>
</body>
</html>

