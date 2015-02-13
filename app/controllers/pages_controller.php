<?php
class PagesController extends AppController {
	var $components = array('Email');
	var $helpers = array('Html', 'Paginator', 'Time', 'Text');
	var $name = 'Pages';
	var $uses = array();
	// Article pagination options are defined in AppController
	
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('*');
		$this->Auth->deny('admin', 'clear_cache');
		$this->Auth->authError = 'Sorry, you need to log in first.';
		$this->paginate['Article']['conditions']['Article.published_date <='] = date('Y-m-d').' 99:99:99';
	}
	
	function beforeRender() {
		parent::beforeRender();
	}
	
	function home() {
		$this->loadModel('Article');
		$this->__setBreadcrumbs(array());
		$this->set(array(
			'articles' => $this->paginate('Article'),
			'title_for_layout' => false
		));
	}
	
	function about() {
		$this->set(array(
			'title_for_layout' => 'About',
		));
	}
	
	function contact() {
		$this->helpers[] = 'Recaptcha';
		$this->helpers[] = 'Form';
		$categories = array('General', 'Website errors');
		$this->set(array(
			'title_for_layout' => 'Contact Us',
			'categories' => $categories
		));
		if (empty($this->data)) {
			return;
		}
		if (! $this->__recaptchaIsValid()) {
			$this->set('recaptcha_error', 'Try typing those CAPTCHA challenge words again. There was an error with your previous attempt.');
			return;
		}
		$message = $this->data['message']['body'];
		$this->set('message', $message);
		$sender_name = $this->data['message']['name'];
		$sender_email = $this->data['message']['email'];
		$category = $categories[$this->data['message']['category']];
		$subject = "Ice Miller Edge Contact: $category";
		
		// Determine who the email recipient should be
		switch ($category) {
			case 'Website errors':
				$recipient = 'gtwatson@bsu.edu';
				break;
			case 'General':
			default:
				$recipient = 'gtwatson@bsu.edu';
				break;
		}		

		$this->Email->to = $recipient;
		$this->Email->from = "$sender_name <$sender_email>";
		$this->Email->subject = $subject;
		$this->Email->template = 'default';
		$this->Email->sendAs = 'both';
		if ($this->Email->send()) {
			$this->set('success', true);
		} else {
			$formatted_message = str_replace('+', ' ', urlencode("$message\n \n \n$sender_name"));
			$formatted_subject = str_replace('+', ' ', urlencode($subject));
			$email_link = "<a href=\"mailto:$recipient?subject=$formatted_subject&body=$formatted_message\">$recipient</a>";
			$printed_message = nl2br($message);
			$this->set(
				'error', 
				'Sorry, but there was an error sending your message. You can try again, or use your 
				own email client to send the message instead. For your convenience, the following email link
				will automatically fill in the message that you have written: '.$email_link.'.<br />
				<br />
				But as a backup, your message is also displayed below for you to copy and paste into your email:<br />
				<br />
				<blockquote>
					'.$printed_message.'
				</blockquote>'
			);	
		}
	}
	
	function clear_cache() {
		Cache::clear();
		clearCache();
		$this->flash('Cache cleared.', 'success');
		$this->redirect($this->referer());
	}
	
	function sitemap() {
		$this->loadModel('Article');
		$this->loadModel('User');
		$this->loadModel('Tag');
		$this->set(array(
		
			'articles' => $this->Article->find('all', array(
				'conditions' => array(
					'Article.published_date <=' => date('Y-m-d').' 99:99:99',
					'Article.is_published' => 1
				),
				'order' => array(
					'Article.published_date DESC'
				),
				'fields' => array(
					'Article.id', 'Article.published_date'
				),
				'contain' => array()
			)),
			
			'authors' => $this->User->Role->find('all', array(
				'conditions' => array('Role.id' => 8),
				'fields' => array('Role.id'),
				'contain' => array(
					'User' => array(
						'fields' => array('User.id'),
						'conditions' => array('User.active' => 1),
						'order' => array('User.name ASC')
					),
				)
			)),
			
			'tags' => $this->Tag->find('list'),
			
			'pages' => array(
				array('url' => Router::url(array('controller' => 'pages', 'action' => 'about'), true)),
				array('url' => Router::url(array('controller' => 'pages', 'action' => 'contact'), true)),
			) 
			
		));
	}
	
	function test() {
		App::import('Xml');
		$parsed_xml =& new XML('http://cber.localhost/commentaries/index.rss');
		$rss_item = $parsed_xml->toArray();
		$commentaries = $rss_item['Rss']['Channel']['Item'];
		echo '<pre>'.print_r($commentaries, true).'</pre>';
	}
}