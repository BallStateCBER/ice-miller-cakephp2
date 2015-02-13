<?php
if (! isset($this->Paginator->params['paging'])) {
	return;
}

// Return if page count is only one
if (! isset($model) || $this->Paginator->params['paging'][$model]['pageCount'] < 2) {
	return;
}

/* Possible options, all set to boolean values:
 * 		'numbers'	e.g. "1 | 2 | 3 | ..."
 * 		'counter'	e.g. "Page 1 of 10, showing 5 of 50..." */
$default_options = array('numbers' => false, 'counter' => false);
$options = isset($options) ? array_merge($default_options, $options) : $default_options;
?>

<div class="paginator">  
	<?php if ($this->Paginator->hasPrev()): ?>	
		<?php echo $this->Paginator->prev(
			$this->Html->image(
				'/img/icons/arrow-180-medium_gray.png', 
				array('title' => 'Previous')
			),
			array('escape' => false)
		); ?>
	<?php endif; ?>
	
	<?php if ($options['numbers']): ?>
		<?php echo $this->Paginator->numbers(
			am(
				$options, 
				array('before' => false, 'after' => false, 'separator' => ' | ')
			)
		); ?>
	<?php endif; ?>
	
	<?php if ($this->Paginator->hasNext()): ?>
		<?php echo $this->Paginator->next(
			$this->Html->image(
				'/img/icons/arrow-000-medium_gray.png', 
				array('title' => 'Next')
			), 
			array('escape' => false)
		); ?>
	<?php endif; ?>
	
	<?php if ($options['counter']): ?>
		<?php echo $this->Paginator->counter(array(
			'format' => 'Page %page% of %pages%, showing %current% out of %count% total, starting on record %start%, ending on %end%'
			//'format' => '(%start%-%end% of %count%)'
		)); ?>
	<?php endif; ?>
</div>