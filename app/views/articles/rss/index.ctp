<?php
	$this->set('documentData', array(
		'xmlns:dc' => 'http://purl.org/dc/elements/1.1/',
		'xmlns:atom' => 'http://www.w3.org/2005/Atom'
	));

	$this->set('channelData', array(
		'title' => __("Ice Miller Articles (under construction)", true),
		'link' => $this->Html->url('/', true),
		'description' => __("Website under construction. Description will go here.", true),
		'language' => 'en-us',
		'copyright' => ((date('Y') > 2009) ? '2009-'.date('Y') : '2009'),
		'atom:link' => array(
			'attrib' => array(
				'href' => 'http://icemiller.cberdata.org/articles/index.rss',
				'rel' => 'self',
				'type' => 'application/rss+xml'
			)
		)
	));

	foreach ($articles as $article) {
		$articleTime = strtotime($article['Article']['published_date']);
 
		$articleLink = Router::url(array(
			'controller' => 'articles',
			'action' => 'view',
			'slug' => $article['Article']['slug']
		));
		
		// You should import Sanitize
		App::import('Sanitize');
        
		// This is the part where we clean the body text for output as the description 
		// of the rss item, this needs to have only text to make sure the feed validates
		$bodyText = preg_replace('=\(.*?\)=is', '', $article['Article']['body']);
		$bodyText = $this->Text->stripLinks($bodyText);
		$bodyText = Sanitize::stripAll($bodyText);
		$bodyText = $this->Text->truncate($bodyText, 300, '...', true, true);
 
		echo  $this->Rss->item(array(), array(
			'title' => $article['Article']['title'],
			'link' => $articleLink,
			'guid' => array('url' => $articleLink, 'isPermaLink' => 'true'),
			'description' =>  $bodyText,
			'dc:creator' => $article['User']['name'],
			'pubDate' => $article['Article']['published_date'],
			//'atom:link' => 
		));
	}
    