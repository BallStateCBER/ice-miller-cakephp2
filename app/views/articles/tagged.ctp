<div class="tagged">
	<h1 class="page_title">
		Articles Tagged With <em><?php echo ucwords($tagName); ?></em>
	</h1>
	<?php if (empty($articles)): ?>
		(None found)
	<?php else: ?>
		<?php
			$this->Paginator->options(array(
				'url' => array(
					'controller' => 'articles',
					'action' => 'tagged',
					'id' => $tagID
				),
				'model' => 'Article',
				'escape' => false, // Allows the » characters to show up correctly
			));
		?>
		<?php echo $this->element('paging', array('model' => 'Article', 'options' => array('numbers' => true))); ?>
		<ul>
			<?php foreach ($articles as $key => $article): ?>
				<li class="<?php echo $key % 2 == 1 ? 'shaded' : ''; ?>">
					<div class="title">
						<?php echo $this->Html->link(
							$article['Article']['title'],
							array(
								'controller' => 'articles', 
								'action' => 'view', 
								'slug' => $article['Article']['slug']
							)
						); ?>
					</div>
					<div class="date">
						<?php echo date('F j, Y', $time->fromString($article['Article']['published_date'])); ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php echo $this->element('paging', array('model' => 'Article', 'options' => array('numbers' => true))); ?>
	<?php endif; ?>
</div>