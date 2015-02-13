<div class="dated_articles_nav">
	<?php if ($has_prev): ?>
		<?php echo $this->Html->link(
			'&laquo; '.($year - 1),
			array(
				'controller' => 'articles',
				'action' => 'dated',
				'year' => $year - 1
			),
			array('escape' => false)
		); ?>
	<?php endif; ?>
	
	<?php if ($has_prev && $has_next): ?>
		|
	<?php endif; ?>
	
	<?php if ($has_next): ?>
		<?php echo $this->Html->link(
			($year + 1).' &raquo;',
			array(
				'controller' => 'articles',
				'action' => 'dated',
				'year' => $year + 1
			),
			array('escape' => false)
		); ?>
	<?php endif; ?>
</div>