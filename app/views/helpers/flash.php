<?php
class FlashHelper extends Helper {	
	var $helpers = array('Session');
	
	// Retrieves and deletes messages set by 
	// Controller::flash() and by authentication errors
	function getMessages() {
		$messages = $this->Session->read('messages');
		$this->Session->delete('messages');
		if ($this->Session->read('suppress_auth_error_msgs')) {
			$this->Session->delete('suppress_auth_error_msgs');
			$this->Session->delete('Message.auth');
		} elseif ($auth_error = $this->Session->read('Message.auth')) {
			$messages['error'][] = $auth_error;
			$this->Session->delete('Message.auth');
		}
		return $messages;
	}
	
	function show() {
		$messages = $this->getMessages();
		if (empty($messages)) {
			return '';
		}
		$html = '';
		$n = 0;
		foreach ($messages as $msg) {
			if (empty($msg['message'])) {
				continue;
			}
			$html .= '<li class="'.$msg['class'].'" id="notification_'.$n.'">';
			$html .= '<span class="close" title="Click to dismiss" onclick="Effect.Fade(\'notification_'.$n.'\')">X</span>';
			$html .= '<p><img src="'.$this->getIcon($msg['class']).'" />';
			$html .= $msg['message'];
			$html .= '</p></li>';
			$n++;
		}
		return "<ul>$html</ul>";
	}
	
	function getIcon($type) {
		switch ($type) {
			case 'error':
				return "/img/icons/cross-circle.png";						
			case 'success':
				return "/img/icons/tick-circle.png";						
			case 'notification':
			default:
				return "/img/icons/information.png";	
		}
	}
}
?>