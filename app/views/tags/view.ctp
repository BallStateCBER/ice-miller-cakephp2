<?php 
$first_model = true; 
?> 
<?php if (empty($tagged_content)): ?>
	Huh. That's weird. It doesn't seem like there's anything on the site right now that has been given that tag. How did you get here?
<?php else: ?>
	<div class="tagged_items_by_model">
		<?php if (count($tagged_content) > 1): ?>
			<strong>
				Select a category:
			</strong>
			<ul class="models" id="viewtag_categoryoptions">
				<?php foreach ($tagged_content as $model => $data): ?>
					<li id="viewtag_category_<?php echo $model ?>" <?php if ($first_model): ?>style="background-image: url('/img/icons/fugue/icons/magnifier-medium.png')"<?php endif; ?>>
						<?php 
						$model_plural = Inflector::pluralize($model);
						$model_plural_lower = strtolower($model_plural);
						echo $ajax->link(
							'<span class="count">'.$data['count'].'</span> '.$model_plural, 
							array( 'controller' => strtolower($model_plural_lower), 'action' => 'with_tag', $data['tag_id']), 
							array(
								'update' => 'tagged_content_links_loading',
								'loading' => "$('viewtag_category_$model').style.backgroundImage = \"url('/img/loading2.gif')\";",
								'loaded' => "$$('#viewtag_categoryoptions > li').each(function(li){li.style.backgroundImage = 'none';}); $('viewtag_category_$model').style.backgroundImage = \"url('/img/icons/fugue/icons/magnifier-medium.png')\";"
							),
							null, null, false
						); 
						?>			
					</li>
					<?php if ($first_model): $first_model = false; ?>
						<script type="text/javascript">
							new Ajax.Updater('tagged_content_links_loading','/<?php echo $model_plural_lower ?>/with_tag/<?php echo $data['tag_id'] ?>', {
								asynchronous:true, 
								evalScripts:true, 
								requestHeaders:['X-Update', 'tagged_content_links_loading']
							}); 
						</script>
					<?php endif; ?>
				<?php endforeach; ?>			
			</ul>
		<?php else: ?>
			<?php foreach ($tagged_content as $model => $data): ?>
				<script type="text/javascript">
					new Ajax.Updater('tagged_content_links_loading','/<?php echo Inflector::pluralize(strtolower($model)) ?>/with_tag/<?php echo $data['tag_id'] ?>', {
						asynchronous:true, 
						evalScripts:true, 
						requestHeaders:['X-Update', 'tagged_content_links_loading']
					}); 
				</script>
			<?php break; endforeach; ?>
		<?php endif; ?>
		<div id="tagged_content_links_loading"></div>
	</div>
	<br class="clear;" />
<?php endif; ?>