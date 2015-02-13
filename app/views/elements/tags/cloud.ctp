<?php
	$i = 0;
	if (! isset($min_font_percent)) $min_font_percent = 80;
	if (! isset($max_font_percent)) $max_font_percent = 150;
	if (! isset($tag_limit)) $tag_limit = null;
	if (! isset($tag_cloud)) {
		$tag_cloud = $this->requestAction(
			array(
				'controller' => 'tags', 
				'action' => 'cloud'
			),
			array('named' => array(
				'model' => null, 
				'tag_limit' => $tag_limit
			))
		);
	}
?>
<div class="tag_cloud">
	<?php 
		foreach ($tag_cloud as $tag_name => $tag_info) {
			$possible_size_increase = $max_font_percent - $min_font_percent;
			$size_increase = $possible_size_increase * $tag_info['size_percent'];
			$font_size = $min_font_percent + $size_increase;
			echo $this->Html->link(
				$tag_name,
				array(
					'controller' => 'articles',
					'action' => 'tagged',
					'id' => $tag_info['id']
				),
				array(
					'style' => "font-size: $font_size%;", 
					'title' => $tag_info['count'].' article'.($tag_info['count'] > 1 ? 's' : ''),
					'class' => ($i % 2 == 0) ? 'reverse' : ''
				)
			);
			echo ' ';
			$i++; 
		} 
	?>
</div>