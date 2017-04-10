<?php

require 'config.php';

header('Content-type: application/rss+xml');
header('Cache-Control: public, max-age=3600');
echo '<?xml version="1.0" encoding="utf-8"?>';

?>

<rss version="2.0"
 xmlns:atom="http://www.w3.org/2005/Atom"
 xmlns:cc="http://web.resource.org/cc/"
 xmlns:content="http://purl.org/rss/1.0/modules/content/"
 xmlns:dc="http://purl.org/dc/elements/1.1/"
 xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"
 xmlns:media="http://search.yahoo.com/mrss/"
>

<channel>
<title><?=$title?></title>
<link><?=$baseURL?></link>
<atom:link href="<?=$SERVER['PHP_SELF']?>" rel="self" type="application/rss+xml" />
<description><?=$title?></description>
<language>en-us</language>
<generator>http://atom.geekhood.net</generator>

<?php

libxml_use_internal_errors(true);

$atom2rss = new DOMDocument();
$atom2rss->load('atom2rss.xsl');
$processor = new XSLTProcessor();
$processor->registerPHPFunctions();
$processor->importStylesheet($atom2rss);

foreach ($feeds as $feed) {
	$read = new DOMDocument;
	$read->preserveWhitespace = false;

	if ($read->load($feed)) {
		echo "<!-- $feed " . $read->documentElement->nodeName . " -->";
		if ($read->documentElement->nodeName == 'feed') {
			// this is an atom feed, so convert to rss
			$read = $processor->transformToDoc($read);
		}

		$items = $read->getElementsByTagName('item');
		foreach ($items as $item) {
			echo $read->saveXML($item);
		}
	} else {
		echo "<!-- error loading $feed: ";
		foreach (libxml_get_errors() as $error) {
			echo $error->line . ":" . $error->column . ": " . $error->message . "\n";
		}
		echo "-->";
		libxml_clear_errors();
	}
}

?>

</channel></rss>
