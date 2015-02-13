Effect.Updater = Class.create();
Object.extend(Object.extend(Effect.Updater.prototype, Effect.Base.prototype), {
	initialize: function(element) {
		this.element = $(element);
		var options = Object.extend ({
			duration: 0.001,
			newContent: ''
		}, arguments[1] || {});
		this.start(options);
	},
	loop: function(timePos) {
		if(timePos >= this.startOn) {
			Element.update(this.element ,this.options.newContent);
			this.cancel();
			return;
		}
	}
});

function showHide(toggle_this_id, child_count, handle_li_id) {
	var handle_li = $(handle_li_id);
	var toggle_this = $(toggle_this_id);
	if (toggle_this) {
		//var timeToTake = .5 * child_count;
		var timeToTake = .3;
		
		var toggleSubmenuHandle_expand = function (handle_li) {
			handle_li.down('img.expand_collapse').src = '/img/icons/menu-collapsed.png';
			handle_li.down('span').title = 'Click to expand';	
		};

		var toggleSubmenuHandle_collapse = function (handle_li) {
			handle_li.down('img.expand_collapse').src = '/img/icons/menu-expanded.png';
			handle_li.down('span').title = 'Click to collapse';
		};
		
		if (toggle_this.visible()) {
			Effect.Shrink(toggle_this_id, {
				duration: timeToTake, 
				direction: 'top-left', 
				queue: {
					position: 'end', 
					scope: 'tree', 
					limit: 1
				},
				beforeStart: toggleSubmenuHandle_expand.bind(this, handle_li)
			});
		} else {
			Effect.Grow(toggle_this_id, {
				duration: timeToTake, 
				direction: 'top-left', 
				queue: {
					position: 'end', 
					scope: 'tree', 
					limit: 1
				},
				beforeStart: toggleSubmenuHandle_collapse.bind(this, handle_li)
			});
		}						
	}
}

function toggleFieldset(id) {
	fieldset = $(id);
	internal = fieldset.down('.fieldset_internal');		
	if (internal.visible()) {
		Effect.SlideUp(internal, {
			afterFinish: function() {
				fieldset.addClassName('collapsed');
			},
			duration: 0.5,
			queue: {
				position: 'end',
				scope: 'fieldset_toggle',
				limit: 1
			}
		});
		
	} else {
		Effect.SlideDown(internal, {
			beforeStart: function() {
				fieldset.removeClassName('collapsed');
			},
			duration: 0.5,
			queue: {
				position: 'end',
				scope: 'fieldset_toggle',
				limit: 1
			}
		});
	}
}

function setupCollapsibleFieldsets() {
	$$('fieldset.collapsible').each(function(fieldset) {
		legend = fieldset.down('legend');
		legend.onclick = function() {
			toggleFieldset(fieldset.identify());
		};
		legend.addClassName('fake_link');
		legend.title = 'Clicky clicky.';
		fieldset.down('div').wrap('div'); // Extra div to make animation smoother
		wrapper = fieldset.down('div').wrap('div');
		wrapper.addClassName('fieldset_internal');
		if (fieldset.hasClassName('collapsed')) {
			wrapper.hide();
		}
	});
}

function selectTag(tag_id, submenu_header) {
	var avail_tag_li = $(tag_id + '_li');
	if (! avail_tag_li) {
		alert('There was an error selecting a tag (' + tag_id + '_li)');
		return;
	}
	var selected_tag_div = avail_tag_li.down('div').cloneNode(true);
	var selected_tag_li = selected_tag_div.wrap('li', {'id': tag_id + '_li_selected'});
	
	// If it's not provided whether this tag is or is not a submenu header,
	// we'll check and see for ourselves.
	if (typeof submenu_header == 'undefined') {
		submenu_header = ($(tag_id + '_submenu') != undefined);
	}
	
	// For submenu headers
	if (submenu_header) {
		// Hide 'add' button 
		avail_tag_li.down('img.add_remove').hide();
	
		// Remove 'click here to expand' title
		selected_span = selected_tag_li.down('span');
		selected_span.title = '';
		
		// Remove onclick action
		selected_span.onclick = '';
		
		// Change expand/collapse icon into a leaf icon
		selected_tag_li.down('img.expand_collapse').src = '/img/icons/menu-leaf.png';
		
	// For 'tree leaf' tags
	} else {
		// Hide tag in 'available' list 
		avail_tag_li.hide();
	}

	// Enable hidden input
	selected_tag_li.down('input').enable();

	// Change action button from plus to minus
	var icon = selected_tag_li.down('img');
	icon.src = '/img/icons/minus.png';
	icon.onclick = function() {unselectTag(tag_id, submenu_header);};
	icon.title = 'Click to remove';
	icon.style.visibility = '';
	
	// Place tag in 'selected' list
	$('selected_tags').down('ul').insert({bottom: selected_tag_li});
}

// If an unlisted tag needs to be placed
function selectUnlistedTag(tag_id, name) {
	tag_li = $('tag_' + tag_id + '_li');
	if (tag_li) {
		selectTag('tag_' + tag_id);
		return;
	}
	
	var ul = $('selected_tags').down('ul');
	
	var li = new Element('li', {
		'id': 'tag_' + tag_id + '_li_selected'
	});
	
	var div = new Element('div', {
		'class': 'single_row'
	});
	
	var button = new Element('img', {
		'class': 'add_remove',
		'onclick': 'unselectTag(' + tag_id + ', false);',
		'title': 'Click to remove',
		'src': '/img/icons/fugue/icons-shadowless/minus.png'
	});
	
	var span = new Element('span', {
		'id': 'tag_' + tag_id + '_span'
	});
	
	var icon = new Element('img', {
		'class': 'leaf',
		'src': '/img/icons/menu-leaf.png'
	});
	
	var input = new Element('input', {
		'type': 'hidden',
		'value': tag_id,
		'name': 'data[Tag][]'
	});
	
	span.update(name);
	span.insert(input);
	div.insert(button);
	div.insert(icon);
	div.insert(span);
	li.insert(div);
	ul.insert(li);
}

function unselectTag(tag_id, submenu_header) {
	var selected_tag_li = $(tag_id + '_li_selected');

	// Remove tag from 'selected' list
	selected_tag_li.remove();

	var avail_tag_li = $(tag_id + '_li');
	
	if (avail_tag_li) {
		
		// For submenu headers
		if (submenu_header) {
			// Show 'add' button in 'available' list
			avail_tag_li.down('img').show();
			
		// For 'tree leaf' tags
		} else {
			// Show tag in 'available' list 
			avail_tag_li.show();
		}
	} else {
		// If the tag is not found in the 'available tags' list,
		// then it was (probably) an unlisted tag that should not
		// appear in that list after being un-selected
	}
}

//Re-disable any hidden inputs that have been mysteriously re-enabled
function disableUnselectedTags() {
	alert('disabling unselected tags');
	$$('#available_tags input').each(function(input) {
		input.disable();
	});
	return true;
}

// For canonical tags, only ID needs to be passed.
// For unlisted / custom tags, both ID and name needs to be passed
function preloadSelectedTag(id, name) {
	span_id = id + '_span';
	handle = $(span_id);

	// If this tag is in the 'available tags' list
	if (handle) {
		selectTag(id);

	// If this is a custom tag
	} else {
		new_li = '<li><div class="single_row"><span id="' + id + '_span" class="selected_tag">' + name + '<input type="hidden" value="' + id + '" name="data[Tag][]" /></span></div></li>';
		$('selected_tags').down('ul').insert({bottom: new_li});
		new Draggable(id + '_span', {revert: true});
	}
}

window.onload = function() {
	setupCollapsibleFieldsets();
}

function selectTagView(mode) {
	if (mode == 'cloud') {
		$('tag_cloud').show();
		$('tag_list').hide();
	} else if (mode == 'list') {
		$('tag_cloud').hide();
		$('tag_list').show();
	}
}

function showAuthorInfo(author_id) {
	var author_pic = $('authorpic'+author_id);
	
	// If this author is already selected
	if (author_pic.hasClassName('selected')) {
		return;
	}

	Effect.Fade('authors_info', {
		duration: 0.0,
		queue: {
			position: 'end',
			scope: 'authorinfo',
			limit: 1
		},
		afterFinish: function() {
			$$('#authors .info > div').each(function (div) {
				div.hide();
			});
			$$('#authors .headshots img').each(function (img) {
				img.removeClassName('selected');
			});
			author_pic.addClassName('selected');
			$('author'+author_id).show();
			Effect.Appear('authors_info', {
				duration: 0.0
			});
			
		}
	});
}

function toggleDelayPublishing() {
	var current_time = new Date();
	var this_month = current_time.getMonth() + 1;
	if (this_month < 10) {
		this_month = '0' + this_month;
	}
	var this_day = current_time.getDate();
	if (this_day < 10) {
		this_day = '0' + this_day;
	}
	var this_year =  current_time.getFullYear();
	var selected_month = $('ArticlePublishedDateMonth').value;
	var selected_day = $('ArticlePublishedDateDay').value;
	var selected_year = $('ArticlePublishedDateYear').value;
	if ((selected_year + selected_month + selected_day) > (this_year + this_month + this_day)) {
		$('delayed_publishing_date').update('automatically on ' + selected_month + '-' + selected_day + '-' + selected_year);
	} else {
		$('delayed_publishing_date').update();
	}
}