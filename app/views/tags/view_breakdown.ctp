<?php
$model_plural = Inflector::pluralize($model);
$model_lower_plural = strtolower($model_plural);
?>
<h3 class="selected_tag_category">
	<?php echo $model_plural ?>
</h3>
<?php if (empty($tagged_items)): ?>
	Strange. No <?php echo $model_lower_plural ?> have been tagged with this tag. How did you get here?
<?php else: ?>
	<?php if ($model == 'Flyer'): ?>
		<div class="flyers">
			<?php foreach ($tagged_items as $id => $flyer): ?>
				<a href="/img/flyers/<?php echo $flyer['full_filename'] ?>" rel="shadowbox">
					<img 
						src="/img/flyers/<?php echo $flyer['thumbnail_filename']; ?>" 
						class="flyer_thumbnail"
						title="Click to view full-sized" 
					/ >
				</a>
			<?php endforeach; ?>
		</div>
	<?php else: ?>
		<ul class="items">
			<?php foreach ($tagged_items as $id => $title): ?>
				<li>
					<?php echo $html->link($title, array('controller' => $model_lower_plural, 'action' => 'view', $id)); ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
<?php endif; ?>