<?php 
	$authors = $this->requestAction(array(
		'controller' => 'users',
		'action' => 'authors'
	));
?>
<div id="authors">
	<div class="headshots">
		<?php $first = true; ?>
		<?php foreach ($authors as $author): ?>
			<?php echo $this->Html->image('users/'.$author['picture'], array(
				'url' => array(
					'controller' => 'users',
					'action' => 'view', 
					'id' => $author['id']
				),
				'alt' => $author['name'],
				'title' => $author['name'],
				'id' => 'authorpic'.$author['id'],
				'onmouseover' => 'showAuthorInfo('.$author['id'].')',
				'class' => $first ? 'selected' : ''  
			)); ?>
			<?php if ($first) $first = false; ?>
		<?php endforeach; ?>
	</div>
	<div class="info" id="authors_info">
		<?php $first = true; ?>
		<?php foreach ($authors as $author): ?> 
			<div id="author<?php echo $author['id']; ?>" <?php if (! $first): ?>style="display: none;"<?php endif; ?>>
				<div class="name">
					<?php echo $this->Html->link(
						$author['name'],
						array(
							'controller' => 'users',
							'action' => 'view', 
							'id' => $author['id']
						)
					); ?>
				</div>
				<span class="latest_article">
					<span class="date">
						Newest article: <?php echo date('F j, Y', $time->fromString($author['Article'][0]['published_date'])); ?>
					</span><br />
					<?php echo $this->Html->link(
						$author['Article'][0]['title'],
						array(
							'controller' => 'articles',
							'action' => 'view', 
							'id' => $author['Article'][0]['id']
						)
					); ?>
					<br />
				</span>
			</div>
			<?php if ($first) $first = false; ?>
		<?php endforeach; ?>
	</div>
</div>