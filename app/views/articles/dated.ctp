<?php if ($year): ?>
	<?php echo $this->element('articles/dated_nav'); ?>
	<h1 class="page_title">
		<?php echo $year; ?> Articles
	</h1>
	<div id="articles_by_date">
		<?php if (empty($articles)): ?>
			(none found)
		<?php else: ?>
			<?php foreach ($articles as $month => $group): ?>
				<h2>
					<?php echo date('F', mktime(0, 0, 0, $month)); ?>
				</h2>
				<table>
					<?php foreach ($group as $key => $article): ?>
						<tr <?php if ($key % 2 == 1): ?>class="shaded"<?php endif; ?>>
							<td class="date">
								<?php echo date('M j', $time->fromString($article['published_date'])); ?>
							</td>
							<td>
								<?php echo $this->Html->link(
									$article['title'],
									array(
										'controller' => 'articles',
										'action' => 'view',
										'slug' => $article['slug']
									),
									array('escape' => false)
								); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
	<?php echo $this->element('articles/dated_nav'); ?>
<?php else: ?>
	<h1 class="page_title">
		Articles By Year
	</h1>
	<p>
		Select a year below:
	</p>
	<ul>
		<?php foreach ($years as $y): ?>
			<li>
				<?php echo $this->Html->link(
					$y,
					array(
						'controller' => 'articles',
						'action' => 'dated',
						'year' => $y
					)
				); ?>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>