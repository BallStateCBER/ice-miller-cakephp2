<?php
	$min_font_percent = 80;
	$max_font_percent = 150;
?>
<p id="tag_intro">
	Tags are key words that describe and group similar content together. 
	Click on a tag to read relevant articles.
</p>
<div id="tag_index">
	<div class="controls">
		<span class="fake_link" onclick="selectTagView('cloud')">Cloud</span>
		/
		<span class="fake_link" onclick="selectTagView('list')">List</span>
	</div>
	
	<div id="tag_cloud" class="tag_cloud">
		<?php echo $this->element('tags/cloud', compact('min_font_percent', 'max_font_percent', 'tag_cloud')); ?>
	</div>	
	
	<ul id="tag_list" class="tag_sublist" style="display: none;">
		<?php foreach ($tag_list as $letter => $tags): ?>
			<?php foreach ($tags as $tag_name => $tag_info): ?>
				<li>
					<?php echo $this->Html->link(
						ucfirst($tag_name),
						array(
							'controller' => 'articles',
							'action' => 'tagged',
							'id' => $tag_info['id']
						)
					); ?>
					<span class="count">
						(<?php echo $tag_info['count']; ?>)
					</span>
				</li>
			<?php endforeach; ?>
		<?php endforeach; ?>
	</ul>
	
	
</div>