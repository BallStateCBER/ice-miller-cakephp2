<?php echo $this->element(
	'articles/comment', 
	array(
		'comment' => $comment, 
		'root' => $comment['Comment']['parent_id'] == null,
		'article_id' => $article_id
	)
); ?>