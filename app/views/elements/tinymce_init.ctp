<?php
	// The misc:tinymce_image permission is needed for the image uploading button to appear.
?>
<script type="text/javascript">
	tinyMCE.init({
		theme : "advanced",
		theme_advanced_buttons1 : "bold,italic,underline,removeformat,separator,link,unlink,<?php if ($this->Permission->permitted('misc', 'tinymce_image')): ?>phpimage,<?php endif; ?>hr,charmap",
		theme_advanced_buttons2 : "bullist,numlist,separator,justifyleft,justifycenter,justifyright,justifyfull,blockquote,outdent,indent,separator,undo,redo",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		mode : "textareas",
		convert_urls : false,
		gecko_spellcheck : true,
		plugins : "phpimage,paste",
		paste_auto_cleanup_on_paste: true,
		paste_strip_class_attributes: "all",
		paste_remove_spans: false,
		paste_remove_styles: false,
		handle_event_callback: function () {
			form_changed = true;
		},
		setup: function(ed) {
			ed.onInit.add(function(ed) {
				var textarea = ed.getElement();
				Element.extend(textarea);
				var textarea_dimensions = textarea.getDimensions();
				var width = textarea_dimensions.width;

				// If the editor and the toggle switch have already been loaded, don't re-load the toggle switch
				if ($(textarea.id + '_tinymce_toggle')) {
					return;
				}

				// Mode: <span class="toggle_tinymce_on">Rich Text</span> | <span class="fake_link toggle_tinymce_off" onclick="toggleTinyMCEEditor(\''+textarea.id+'\')">HTML</span>
				var span1 = new Element('span', {'class': 'toggle_tinymce_on'}).update('Rich Text');
				var span2 = new Element('span', {'class': 'fake_link toggle_tinymce_off', 'onclick': 'toggleTinyMCEEditor(\''+textarea.id+'\')'}).update('HTML');
				//var div = new Element('div', {'class': 'toggle_tinymce'}).insert({'top' : 'Mode: ' + span1 + ' | ' + span2});
				var div = new Element('div', {'id': textarea.id + '_tinymce_toggle', 'class': 'toggle_tinymce', 'style': 'width: ' + width + 'px'}).update('Mode: ');
				div.insert({'bottom': span1});
				div.insert({'bottom': span2});
				div.insert({'bottom': '<br />'});
				textarea.insert({'before': div});
			});
		},
		content_css : '/css/tinymce_custom.css',
	});

	function toggleTinyMCEEditor(id) {
		var on_switch = $$('#' + id + '_tinymce_toggle span.toggle_tinymce_on')[0];
		var off_switch = $$('#' + id + '_tinymce_toggle span.toggle_tinymce_off')[0];
		if (!tinyMCE.get(id)) {
			tinyMCE.execCommand('mceAddControl', false, id);
			var deactivated_button = on_switch;
			var activated_button = off_switch;
		} else {
			tinyMCE.execCommand('mceRemoveControl', false, id);
			var deactivated_button = off_switch;
			var activated_button = on_switch;
		}
		deactivated_button.removeClassName('fake_link');
		deactivated_button.onclick = null;
		activated_button.addClassName('fake_link');
		activated_button.onclick = function() {toggleTinyMCEEditor(id);};
	}
</script>

<?php /*
force_p_newlines: true,
force_br_newlines: false
*/ ?>