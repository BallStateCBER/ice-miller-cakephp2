<?php
App::import('Sanitize');

class CommentsController extends AppController {
	var $name = 'Comments';
	var $components = array();
	var $helpers = array();
		
	function beforeFilter() {
		parent::beforeFilter();
		$this->require_captcha = false;
	}
	
	function beforeRender() {
		parent::beforeRender();
	}
		
	function add() {
		
		// Set AJAX layout if appropriate
		if (isset($this->passedArgs['ajax']) || isset($this->params['isAjax']) && $this->params['isAjax']) {
			$this->layout = 'ajax';
		}
		
		// If the form has not yet been submitted
		if (empty($this->data)) {
			$this->set(array(
				'article_id' => $this->passedArgs['article_id'],
				'parent_id' => $this->passedArgs['parent_id'],
				'title_for_layout' => 'Post Comment' 
			));
		
		// If the form has been submitted
		} else {
			$article_id = $this->data['Comment']['article_id'];
			$this->data['Comment']['user_id'] = $this->Auth->user('id');
			$this->Comment->set($this->data);
			$validates = $this->Comment->validates();
			if ($this->__recaptchaIsValid()) {
				if ($validates) {
					
					// Save comment and load a view of the comment
					$this->Comment->save($this->data);
					if ($this->layout == 'ajax') {
						$this->set(array(
							'article_id' => $article_id,
							'comment' => $this->Comment->data
						));
						//$this->render('/comments/view');
						$this->redirect('/comments/view/'.$this->Comment->id);
						return;
					} else {
						$this->redirect("/article/$article_id#comment".$this->Comment->id);
					}
					
				} else {
					$this->flash('Please correct the errors indicated below before posting your comment.', 'error');	
				}
			} else {
				$this->flash('Invalid CAPTCHA response. Please try again.', 'error');
			}
			if (isset($this->data['Comment']['article_id'])) {
				$article_id = $this->data['Comment']['article_id'];
				$this->redirect("/article/$article_id#comments");
			}
		}
	}
	
	function delete() {
		$this->Comment->id = $this->passedArgs['comment_id'];
		$children = $this->Comment->find('all', array(
			'conditions' => array('Comment.parent_id' => $this->Comment->id),
			'fields' => array('Comment.id'),
			'recursive' => -1
		));
		if (empty($children)) {
			if ($this->Comment->delete()) {
				$this->flash('Comment deleted.', 'success');
			} else {
				$this->flash('There was an error deleting that comment.', 'error');
			}
		} else {
			if ($this->Comment->saveField('body', '(this comment has been deleted)')) {
				$this->flash('The body of that comment has been removed, but the comment was not deleted because it has replies.', 'success');
			} else {
				$this->flash('There was an error deleting that comment.', 'error');
			}
		}
		$this->redirect('/article/'.$this->passedArgs['article_id'].'#comments');
	}
	
	function view($id) {
		$this->Comment->id = $id;
		$this->set(array(
			'comment' => $this->Comment->read(),
			'article_id' => $this->Comment->data['Comment']['article_id']
		));
	}
}
?>