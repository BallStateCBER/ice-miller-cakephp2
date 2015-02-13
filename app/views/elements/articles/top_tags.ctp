<?php $tags = $this->requestAction('articles/getTopTags/10'); ?>
<table>
	<?php foreach ($tags as $key => $tag): ?>
		<tr>
			<th>
				<a href="/articles/tagged/<?php echo $tag['articles_tags']['tag_id']; ?>">
					<?php echo $tag['tags']['name']; ?>
				</a>
			</th>
			<td>
				<?php echo $tag[0]['occurrences']; ?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>