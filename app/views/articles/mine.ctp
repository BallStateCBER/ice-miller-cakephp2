<h1 class="page_title">
	<?php echo $title_for_layout; ?>
</h1>

<?php if (empty($articles)): ?>
	You currently have no published articles.
<?php else: ?>
	<table class="my_articles">
		<thead>
			<tr>
				<th class="modified">Published</th>
				<th>Title</th>
				<th class="actions">Actions</th>
			</tr>
		</thead>
		<tfoot></tfoot>
		<tbody>
			<?php foreach ($articles as $key => $article): ?>
				<tr<?php if ($key % 2 == 1): ?> class="alternate"<?php endif; ?>>
					<td>
						<?php echo date('F j, Y', $this->Time->fromString($article['Article']['published_date'])); ?>
					</td>
					<td>
						<?php echo $this->Html->link(
							$article['Article']['title'],
							array(
								'controller' => 'articles', 
								'action' => 'view', 
								'slug' => $article['Article']['slug']
							)
						); ?>
					</td>
					<td>
						<?php 
							echo $this->Html->link(
								'Unpublish',
								array('controller' => 'articles', 'action' => 'unpublish', $article['Article']['id'])
							);
							echo '&nbsp;|&nbsp;';
							echo $this->Html->link(
								'Edit',
								array('controller' => 'articles', 'action' => 'edit', $article['Article']['id'])
							);
							echo '&nbsp;|&nbsp;';
							echo $this->Html->link(
								'Delete',
								array('controller' => 'articles', 'action' => 'delete', $article['Article']['id']),
								array(),
								'Are you sure that you want to delete this article?',
								false
							);
						?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>