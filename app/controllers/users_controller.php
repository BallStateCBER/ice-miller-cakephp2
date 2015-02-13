<?php
App::import('Sanitize');
class UsersController extends AppController {
	var $name = 'Users';
	var $auto_approve = false; // false puts new additions into moderation queue
	var $components = array('Cookie', 'Email');
	var $helpers = array('Time');
	var $require_captcha;
	var $require_activation_via_email;

	function beforeFilter() {
		parent::beforeFilter();
		//$this->Auth->allow('view', 'index', 'login', 'add', 'logout', 'authors');
		$this->Auth->allow('*');
		$this->Auth->deny('edit', 'add', 'my_account');
		$this->require_captcha = false;
		$this->require_activation_via_email = false;
	}

	function beforeRender() {
		parent::beforeRender();
	}

	function edit($id = null) {
		if (! $this->__permitted('User', 'edit')) {
			$this->flash('Sorry, you do not have permission to edit users\' profile information.', 'error');
			$this->redirect($this->referer());
		}
		if (! $id || ! is_numeric($id)) {
			$this->flash('Invalid user ID specified. Whose info do you want to edit?', 'error');
			$this->redirect($this->referer());
		}
		$this->User->id = $id;
		if (empty($this->data)) {
			$this->data = $this->User->read();
		} else {
			$this->User->set($this->data);
			if ($this->User->validates()) {
				if ($this->User->save()) {
					$this->flash('Profile updated.', 'success');
					$this->redirect('/users/view/'.$id);
				} else {
					$this->flash('Error updating profile.', 'error');
				}
			}
		}
		$user_name = $this->User->field('name');
		$this->__setBreadcrumbs(array(
			"/user/$id" => $user_name,
			"/users/edit/$id" => 'Edit Profile'
		));
		$this->set(array(
			'title_for_layout' => "Edit $user_name's Info"
		));
	}

	function login() {
		// Redirect user if they're already registered and logged in
		if ($this->Auth->user()) {
			$this->redirect('/');
		}

		if (! empty($this->data)) {
			if ($this->Auth->login($this->data)) {
				$this->flash('You are now logged in.', 'success');
				if (isset($this->data['User']['back'])) {
					$this->redirect($this->data['User']['back']);
				} else {
					$this->redirect($this->Auth->redirect());
				}
			} else {
				if ($this->User->find('list', array('conditions' => array('email' => $this->data['User']['email'])))) {
					$this->set('password_incorrect', true);
				} else {
					$this->set('email_not_found', true);
				}
				$this->data = array();
			}
		}
		$this->set(array(
			'title_for_layout' => 'Log in'
		));
		$this->__setBreadcrumbs(array(
			"/login" => 'Log in'
		));
	}


    function logout() {
    	$this->Session->delete('Permissions');
    	$this->flash('Thanks for stopping by!', 'success');
    	$this->Auth->logout();
    	$this->redirect('/');
    }

    /*
	function add() {
		$this->require_captcha = false;
		if (! empty($this->data)) {
			$this->User->set($this->data);
			$validates = $this->User->validates();
			if ($this->__recaptchaIsValid()) {
				if ($validates) {
					$this->data['User']['email'] = strtolower(trim($this->data['User']['email']));
					$this->User->data['User']['password'] = $this->Auth->password($this->User->data['User']['new_password']);
					if ($this->User->save($this->data)) {

						// Give this user the Registered User role
						$this->User->habtmAdd('Role', $this->User->id, 7);

						$this->flash('New user added.', 'success');
						$this->data = array();
					} else {
						$this->flash('There was an error adding this new user.', 'error');
					}
				}
			} else {
				$this->set('recaptcha_error', 'CAPTCHA challenge response incorrect. Please try again.');
			}
		}

		// So the password fields aren't filled out automatically when the user
		// is bounced back to the page by a validation error
		$this->data['User']['new_password'] = null;
	    $this->data['User']['confirm_password'] = null;

	    $this->__setBreadcrumbs(array(
			"/register" => 'Register New User'
		));
		$this->set(array(
			'title_for_layout' => 'Register New User'
		));
	}
	*/

	function register() {
		$this->require_captcha = true;
		if (! empty($this->data)) {
			$this->User->set($this->data);
			$validates = $this->User->validates();
			if ($this->__recaptchaIsValid()) {
				if ($validates) {
					$this->data['User']['email'] = strtolower(trim($this->data['User']['email']));
					$this->User->data['User']['password'] = $this->Auth->password($this->User->data['User']['new_password']);
					if ($this->User->save($this->data)) {

						// Give this user the Registered User role
						$this->User->habtmAdd('Role', $this->User->id, 7);

						// Attempt to log the user in
						$credentials = array(
							'User' => array(
								'email' => $this->data['User']['email'],
								'password' => $this->Auth->password($this->data['User']['new_password'])
							)
						);
						if ($this->Auth->login($credentials)) {
							$this->flash('Registered and logged in. Welcome!', 'success');
							$this->redirect('/');
						} else {
							$this->flash('Your account has been registered. You may now log in', 'success');
							$this->redirect('/login');
						}
						$this->data = array();
					} else {
						$this->flash('There was an error registering this account.', 'error');
					}
				} else {
					$this->flash('Please go back and correct the indicated errors before registering.', 'error');
				}
			} else {
				$this->set('recaptcha_error', 'CAPTCHA challenge response incorrect. Please try again.');
			}
		}

		// So the password fields aren't filled out automatically when the user
		// is bounced back to the page by a validation error
		$this->data['User']['new_password'] = null;
	    $this->data['User']['confirm_password'] = null;

	    $this->__setBreadcrumbs(array(
			"/register" => 'Register New User'
		));
		$this->set(array(
			'title_for_layout' => 'Register New User'
		));
	}

	function view($id = null) {
		$this->User->id = $id;
		if (! $id || ! is_numeric($id) || ! $this->User->read()) {
			$this->flash('Invalid user ID.', 'error');
			$this->redirect($this->referer());
		}
		$this->__setBreadcrumbs(array(
			"/user/$id" => $this->User->data['User']['name']
		));
		$article_limit = 5;
		$this->set(array(
			'title_for_layout' => $this->User->data['User']['name'],
			'user' => $this->User->data,
			'own_profile' => $this->Auth->user('id') == $id,
			'article_limit' => $article_limit,
			'articles' => $this->User->Article->find('all', array(
				'conditions' => array(
					'Article.is_published' => 1,
					'Article.published_date <=' => date('Y-m-d').' 99:99:99',
					'Article.user_id' => $id
				),
				'order' => array('Article.published_date DESC'),
				'fields' => array(
					'Article.id', 'Article.title', 'Article.published_date', 'Article.slug'
				),
				'contain' => false,
				'limit' => $article_limit
			)),
			'article_count' => $this->User->Article->find('count', array(
				'conditions' => array(
					'Article.is_published' => 1,
					'Article.published_date <=' => date('Y-m-d').' 99:99:99',
					'Article.user_id' => $id
				)
			))
		));
	}

	function index() {
		$this->set(array(
			'users' => $this->User->find('all', array(
				'conditions' => array('accessed' => '1'),
				'order' => array('User.name'),
			)),
			'title_for_layout' => 'Users'
		));
		$this->__setBreadcrumbs(array(
			"/users" => 'Users'
		));
	}

	function convertPasswords() {
		if(! empty($this->User->data['User']['new_password']) ){
			$this->User->data['User']['new_password'] = $this->Auth->password($this->User->data['User']['new_password']);
		}
		if(! empty($this->User->data['User']['confirm_password']) ){
			$this->User->data['User']['confirm_password'] = $this->Auth->password($this->User->data['User']['confirm_password']);
		}
	}

	function add() {
		if (! empty($this->data)) {
			$this->User->create();
			if ($this->User->save($this->data)) {
				$this->flash('New user added.', 'success');
				$this->redirect(array('controller' => 'users', 'action' => 'view', $this->User->id));
			} else {
				$this->flash('The user could not be added. Please, try again.', 'error');
			}
		}
		$this->set('title_for_layout', 'Add New User');
	}
	
	function my_account() {
		$id = $this->Auth->user('id');
		$this->User->id = $id;
		if (empty($this->data)) {
			$this->data = $this->User->read();
		} else {
			if ($this->data['User']['new_password'] == '') {
				//Remove this value so it does not go through validation
				unset($this->data['User']['new_password']);
				unset($this->data['User']['confirm_password']);
			}
			$this->User->set($this->data);
			$validates = $this->User->validates();
			if ($this->__recaptchaIsValid()) {
				if ($validates) {
					$this->data['User']['email'] = strtolower(trim($this->data['User']['email']));
					if (isset($this->User->data['User']['new_password'])) {
						$this->User->data['User']['password'] = $this->Auth->password($this->User->data['User']['new_password']);
					}
					if ($this->User->save()) {
						$this->flash('Profile updated.', 'success');
						$this->redirect('/users/view/'.$id);
					} else {
						$this->flash('Error updating profile.', 'error');
					}
				}
			}
		}
		$this->__setBreadcrumbs(array(
			"/user/$id" => $this->User->data['User']['name'],
			'/profile' => 'Edit Profile'
		));
		$this->set(array(
			'title_for_layout' => 'Edit Profile'
		));
	}

	function authors() {
		$results = $this->User->Role->find('all', array(
			'conditions' => array('Role.id' => 8),
			'fields' => array('Role.id'),
			'contain' => array(
				'User' => array(
					'fields' => array('User.id', 'User.name', 'User.picture'),
					'conditions' => array('User.active' => 1),
					'order' => array('User.name ASC'),
					'Article' => array(
						'fields' => array(
							'Article.id', 'Article.title', 'Article.published_date'
						),
						'order' => array(
							'Article.published_date DESC',
							'Article.id DESC'
						),
						'limit' => 1,
						'conditions' => array(
							'Article.is_published' => 1,
							'Article.published_date <=' => date('Y-m-d').' 99:99:99'
						)
					)
				),
			)
		));
		$authors = array();
		foreach ($results[0]['User'] as $key => $author) {
			if (! empty($author['Article'])) {
				$date = $author['Article'][0]['published_date'].$key;
				$authors[$date] = $author;
			}
		}
		krsort($authors);
		return $authors;
	}
}