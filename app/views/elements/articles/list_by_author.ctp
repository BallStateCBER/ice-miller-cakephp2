<table>
	<?php foreach ($articles as $key => $article): ?>
		<tr <?php if ($key % 2 == 1): ?>class="shaded"<?php endif; ?>>
			<td class="date">
				<?php echo date('F j, Y', $time->fromString($article['Article']['published_date'])); ?>
			</td>
			<td>
				<?php echo $this->Html->link(
					$article['Article']['title'],
					array(
						'controller' => 'articles',
						'action' => 'view',
						'slug' => $article['Article']['slug']
					),
					array('escape' => false)
				); ?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>