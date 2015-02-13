<?php if (! $query): ?>
	
	<p class="notification_message">
		Please enter a word or phrase in the search box above and click 'Search'.
	</p>
	
<?php elseif (empty($articles)): ?>
	
	<p class="notification_message">
		No results found.
	</p>
	
<?php else: ?>
	
	<h1 class="page_title">
		Search Results For "<?php echo $query; ?>"<br />
		<?php echo $this->Paginator->counter(array(
			'format' => '(%start%-%end% of %count%)'
		)); ?>
	</h1>

	<ol id="search_results" start="<?php echo $list_start_number; ?>">
		<?php foreach ($articles as $article): ?>
			<li class="result">
				<span class="title">
					<?php echo $this->Html->link(
						$article['Article']['title'],
						array(
							'controller' => 'articles',
							'action' => 'view',
							'slug' => $article['Article']['slug']
						),
						array('escape' => false)
					); ?>
				</span>
				<div class="info">
					<?php echo date('F j, Y', $time->fromString($article['Article']['published_date'])); ?>
					<?php if (isset($authors[$article['Article']['user_id']])): ?>
						<?php echo $this->Html->link(
							$authors[$article['Article']['user_id']],
							array(
								'controller' => 'users',
								'action' => 'view',
								'id' => $article['Article']['user_id']
							),
							array(
								'escape' => false,
								'title' => 'View profile'
							)
						); ?>
					<?php endif; ?>
				</div>
				<p class="exerpt">
					<?php
						// If the search query is found intact
						if (stripos(strip_tags($article['Article']['body']), $query) !== false) {
							echo $this->Text->highlight( 
								$this->Text->excerpt(
									strip_tags($article['Article']['body']),
									$query,
									100
								),
								$query
							);
						
						// If the search query (might) be found as broken up words
						} else {
							$query_split = explode(' ', $query);
							$excerpt = $this->Text->excerpt(
								strip_tags($article['Article']['body']),
								$query_split[0],
								100
							);
							foreach ($query_split as $query_word) {
								$excerpt = $this->Text->highlight(
									$excerpt,
									$query_word
								);
							}
							echo $excerpt;
						}
					?>
					<?php  ?>
				</p>
			</li>	
		<?php endforeach; ?>
	</ol>
	<?php echo $this->element(
		'paging', 
		array('model' => 'SearchIndex', 'options' => array('numbers' => true))
	); ?>
<?php endif; ?>