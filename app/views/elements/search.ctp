<div id="search">
	<h3>
		Search:
		<span id="search_autosuggest_loading" style="display: none;">
			<img src="/img/loading3.gif" alt="Working..." title="Working..." style="vertical-align:top;" />
		</span>
	</h3>
	
		<?php echo $this->Form->create('Commentary', array('controller' => 'commentaries', 'action' => 'search')); ?>
		<input type="search" value="" id="search_input" style="" name="q" autocomplete="off" placeholder="Enter a search term..." />
		<?php echo $this->Form->end(null); ?> 
	
	<div id="search_autocomplete_choices"></div>
	<script type="text/javascript">
		new Ajax.Autocompleter("search_input", "search_autocomplete_choices", "/tags/autocomplete", {
			paramName: 'string_to_complete',
			minChars: 1,
			frequency: 0.2,
			indicator: 'search_autosuggest_loading'
		});
	</script>
</div>