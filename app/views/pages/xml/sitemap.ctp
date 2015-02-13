<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc><?php echo Router::url('/', true); ?></loc>
        <changefreq>hourly</changefreq>
        <priority>1.0</priority>
    </url>
    
    <?php foreach ($pages as $page): ?>
	    <url>
	        <loc><?php echo Router::url('/'.$page['url'], true); ?></loc>
	        <?php if (isset($page['date_modified'])): ?>
	        	<lastmod><?php echo $time->toAtom($page['date_modified']); ?></lastmod>
	        <?php endif; ?>
	        <changefreq>monthly</changefreq>
	        <priority>0.5</priority>
	    </url>
    <?php endforeach; ?>
    
    <?php foreach ($articles as $article):?>
	    <url>
	        <loc><?php echo Router::url(array(
        		'controller' => 'articles',
        		'action' => 'view',
        		'id' => $article['Article']['id']
        	), true); ?></loc>
	        <lastmod><?php echo $time->toAtom($article['Article']['published_date']); ?></lastmod>
	        <priority>0.6</priority>
	        <changefreq>daily</changefreq>
	    </url>
    <?php endforeach; ?>
    
    <?php foreach ($tags as $id => $tag):?>
	    <url>
	        <loc><?php echo Router::url(array(
        		'controller' => 'articles',
        		'action' => 'tagged',
        		'id' => $id
        	), true); ?></loc>
	        <priority>0.6</priority>
	        <changefreq>monthly</changefreq>
	    </url>
    <?php endforeach; ?>
    
    <?php foreach ($authors[0]['User'] as $author):?>
	    <url>
	        <loc><?php echo Router::url(array(
        		'controller' => 'users',
        		'action' => 'view',
        		'id' => $author['id']
        	), true); ?></loc>
	        <priority>0.7</priority>
	        <changefreq>monthly</changefreq>
	    </url>
    <?php endforeach; ?>
</urlset>