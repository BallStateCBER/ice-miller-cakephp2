<?php
$this->Paginator->options['url'] = array(
	'controller' => 'articles',
	'action' => 'index'
);
$this->Paginator->options['model'] = 'Article';
$this->Paginator->__defaultModel = 'Article';

if (! isset($articles)) {
	$action = 'articles/index';
	if (isset($this->passedArgs['page'])) {
		$action .= '/page:'.$this->passedArgs['page'];
	}
	if (isset($this->passedArgs['category'])) {
		$action .= '/category:'.$this->passedArgs['category'];
	}
	$articles = $this->requestAction($action);
}
?>

<?php if (isset($articles) && ! empty($articles)): ?> 
	<div class="articles_feed">
		<?php foreach ($articles as $article): ?>
			<?php echo $this->element(
				'articles/teaser', 
				array('article' => $article)
			); ?>
		<?php endforeach; ?>
	</div>
	<?php echo $this->element(
		'paging', 
		array('model' => 'Article', 'options' => array('numbers' => true))
	); ?>
<?php else: ?>
	No articles found.
<?php endif; ?>