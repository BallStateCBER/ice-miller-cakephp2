<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
	<channel>
		<title>The Muncie Scene - Recent Articles</title>
		<link>http://themunciescene.com</link>
		<description>If you lived here, you'd be Internets by now.</description>
		<language>en-us</language>
		<pubDate><?php echo date("D, j M Y H:i:s", gmmktime()) .  ' GMT'; ?></pubDate>
		<?php echo $time->nice($time->gmt()) . ' GMT'; ?>
		<docs>http://blogs.law.harvard.edu/tech/rss</docs>
		<generator>Bernard's Crude RSS Refinery</generator>
		<managingEditor>phantom@themunciescene.com</managingEditor>
		<webMaster>phantom@themunciescene.com</webMaster>
		<?php foreach ($articles as $article): ?>
			<item>
				<title><?php echo $article['Article']['title']; ?></title>
				<link>http://themunciescene.com/articles/view/<?php echo $article['Article']['id']; ?></link>
				<?php echo $time->nice($article['Article']['created']) . ' GMT'; ?>
				<pubDate><?php echo $time->nice($time->gmt($article['Article']['created'])) . ' GMT'; ?></pubDate>
				<guid>http://themunciescene.com/articles/view/<?php echo $article['Article']['id']; ?></guid>
			</item>
		<?php endforeach; ?>
	</channel>
</rss>
<?php /*
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
<channel>
<title>Fyodor's RSS Feed</title>
<link>http://fyodor.homelinux.com/cakephp/articles</link>
<description>Fyodor's very own RSS feed</description>
<language>en-uk</language>
<pubDate><?php echo date("D, j M Y H:i:s", gmmktime()) .  ' GMT'; ?></pubDate>
<?php echo $time->nice($time->gmt()) . ' GMT'; ?>
<docs>http://blogs.law.harvard.edu/tech/rss</docs>
<generator>CakePHP</generator>
<managingEditor>fyodor@fyodor.homelinux.com</managingEditor>
<webMaster>fyodor@fyodor.homelinux.com</webMaster>
<?php foreach ($articles as $article): ?>
<item>
<title><?php echo $article['title']; ?></title>
<link>http://fyodor.homelinux.com/cakephp/articles/view/<?php echo $article['id']; ?></link>
<?php echo $time->nice($article['created']) . ' GMT'; ?>
<pubDate><?php echo $time->nice($time->gmt($article['created'])) . ' GMT'; ?></pubDate>
<guid>http://fyodor.homelinux.com/cakephp/articles/view/<?php echo $article['id']; ?></guid>
</item>
<?php endforeach; ?>
</channel>
</rss>
*/ ?>