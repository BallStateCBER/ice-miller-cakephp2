<?php 
class TreeHelper extends Helper {
	var $helpers = array('Html', 'Ajax');
	
	var $uniqueId = 1000;
	
	function getUniqueId($level) {
		$letter = chr(65+$level);
		$ret = $letter.$this->uniqueId;
		$this->uniqueId++;
		return $ret;
	}
	
	function show($name, $data, $minimize = true) {
		list($modelName, $fieldName) = explode('/', $name);
		$output = $this->list_element($data, $modelName, $fieldName, 0);
		return $this->output($output);
	}

	function list_element($data, $modelName, $fieldName, $level) {
		$output = '<ul class="tag_editing">';
		
		foreach ($data as $key => $val) {
			$tag_name = ucfirst($val[$modelName][$fieldName]);
			$selectable = $val[$modelName]['selectable'];
			
			$tag_id_number = $val[$modelName]['id'];
			$tag_id = 'tag_'.$tag_id_number;
			$span_id = $tag_id.'_span';
			$row_id = $tag_id.'_avail_row';
			$button_id = $tag_id.'_button';
			$li_id = $tag_id.'_li';
			$submenu_id = $tag_id.'_submenu';
			$placeholder_id = $tag_id.'_ph';
				
			/* Used before the button hiding/showing was handled entirely with CSS.
			$mouse_events = 
				'onmouseover="$(this.identify()).down(\'img\').style.visibility = \'visible\'"'.
				'onmouseout="$(this.identify()).down(\'img\').style.visibility = \'hidden\'"';
			*/			
			
			$hidden_input = "<input disabled=\"disabled\" type=\"hidden\" name=\"data[$modelName][]\" value=\"$tag_id_number\" />";
			$plus_icon_attributes = 'src="/img/icons/plus.png" title="Click to add"';
			$placeholder = "<li id=\"$placeholder_id\" style=\"display: none;\"></li>";
			
			// For tree branches (submenu headers)
			if (isset($val['children'][0])) {
				
				$span_onclick = "showHide('$submenu_id', ".count($val['children']).", '$li_id');"; 
				$span_class = 'submenu_handle';
				$add_button = ($selectable) ? '<img '.$plus_icon_attributes.' onclick="selectTag(\''.$tag_id.'\', true)" class="add_remove" />' : '';
				
				$output .= 
					'<li id="'.$li_id.'">'.
						'<div class="single_row" id="'.$row_id.'">'.
							$add_button.
							"<span onclick=\"$span_onclick\" class=\"$span_class\" id=\"$span_id\" title=\"Click to expand\">".
								'<img src="/img/icons/menu-collapsed.png" class="expand_collapse" />'.
								$tag_name.
								$hidden_input.
							'</span>'.
						'</div>'.
						'<div id="'.$submenu_id.'" style="display: none;">'.
							$this->list_element($val['children'], $modelName, $fieldName, $level+1).
						'</div>'.
					"</li>";
			
			// For tree leaves
			} else {
				$span_class = 'available_tag';
				$add_button = ($selectable) ? '<img '.$plus_icon_attributes.' onclick="selectTag(\''.$tag_id.'\', false)" class="add_remove" />' : '';
				
				$output .= 
					$placeholder.
					'<li id="'.$li_id.'">'.
						'<div class="single_row" id="'.$row_id.'">'.
							$add_button.
							'<img src="/img/icons/menu-leaf.png" class="leaf" />'.
							"<span id=\"$span_id\" class=\"$span_class\">".
								$tag_name.
								$hidden_input.
							'</span>'.
						'</div>'.
					'</li>';
					//$this->Ajax->drag($draggable_id, array('revert' => true));
			}
		}
		 
		return $output.'</ul>';
	}
}
?>