<?php 
/** 
 * auto_login.php
 *
 * A CakePHP Component that will automatically login the Auth session for a duration if the user requested to (saves data to cookies). 
 *
 * Copyright 2006-2009, Miles Johnson - www.milesj.me
 * Licensed under The MIT License - Modification, Redistribution allowed but must retain the above copyright notice
 * @link 		http://www.opensource.org/licenses/mit-license.php
 *
 * @package		AutoLogin Component
 * @created		May 27th 2009
 * @version 	1.4
 * @link		www.milesj.me/resources/script/auto-login
 * @changelog	www.milesj.me/files/logs/auto-login 
 */

class AutoLoginComponent extends Object {

	// For Phantom's phuckery
	var $debug = false;
	
	/**
	 * Current version: www.milesj.me/files/logs/auto-login
	 * @var string
	 */
	var $version = '1.4';

	/**
	 * Components
	 * @var array 
	 */
	var $components = array('Cookie');
	
	/**
	 * Cookie name 
	 * @var string
	 */
	var $cookieName = 'autoLogin';
	
	/**
	 * Cookie length (strtotime())
	 * @var string
	 */
	var $expires = '+2 weeks';   
	
	/**
	 * Settings
	 * @var array
	 */
	var $settings = array(
		'controller' => 'users',
		'loginAction' => 'login',
		'logoutAction' => 'logout'
	);
	
	/**
	 * Automatically login existent Auth session; called after controllers beforeFilter() so that Auth is initialized
	 * @param object $Controller 
	 * @return void 
	 */
	function startup(&$Controller) { 
		$cookie = $this->Cookie->read($this->cookieName);   

		if ($this->debug) {
			$Controller->flash('(Script debugging, please ignore) Cookie contents: <pre>'.print_r($cookie, true).'</pre>', 'notification');
		}
		
		if (!is_array($cookie) || $Controller->Auth->user()) {
			return;
		}
	
		if (! isset($cookie['hash'])) {
			if ($this->debug) {
				$Controller->flash('(Script debugging, please ignore) Cookie being deleted because hash value is missing.', 'notification');
			}
			$this->delete();
			return;
		} elseif ($cookie['hash'] != $Controller->Auth->password($cookie[$Controller->Auth->fields['username']] . $cookie['time'])) {
			if ($this->debug) {
				$Controller->flash('(Script debugging, please ignore) Cookie being deleted because '.$cookie['hash'].' != '.$Controller->Auth->password($cookie[$Controller->Auth->fields['username']] . $cookie['time']), 'notification');
			}
			$this->delete();
			return;
		}
		
		/*
		if (! isset($cookie['hash']) || $cookie['hash'] != $Controller->Auth->password($cookie[$Controller->Auth->fields['username']] . $cookie['time'])) {
			$this->delete();
			return;
		}
		*/
		
		if ($Controller->Auth->login($cookie)) {
			if (in_array('_autoLogin', get_class_methods($Controller))) {
				call_user_func_array(array(&$Controller, '_autoLogin'), array($Controller->Auth->user()));
			}
		} else {
			if (in_array('_autoLoginError', get_class_methods($Controller))) {
				call_user_func_array(array(&$Controller, '_autoLoginError'), array($cookie));
			}
		}
		
		return true;
	}
	
	/**
	 * Automatically process logic when hitting login/logout actions
	 * @param object $Controller  
	 * @param array $url
	 * @param boolean $status
	 * @param boolean $exit
	 * @return void
	 */
	function beforeRedirect(&$Controller, $url, $status = null, $exit = true) { 
		$controller 	= $this->settings['controller'];
		$loginAction 	= $this->settings['loginAction'];
		$logoutAction 	= $this->settings['logoutAction'];
		
		if (is_array($Controller->Auth->loginAction)) {
			if (!empty($Controller->Auth->loginAction['controller'])) {
				$controller = Inflector::camelize($Controller->Auth->loginAction['controller']);
			}
			
			if (!empty($Controller->Auth->loginAction['action'])) {
				$loginAction = $Controller->Auth->loginAction['action'];
			}
		}
		
		if (!empty($Controller->Auth->userModel) && empty($controller)) {
			$controller = Inflector::pluralize($Controller->Auth->userModel);
		}
		
		if (empty($loginAction)) {
			$loginAction = 'login';
		}
		
		if (empty($logoutAction)) {
			$logoutAction = 'logout';
		}
		
		// Is called after user login/logout validates, but befire auth redirects
		if ($Controller->name == $controller) {
			$data = $Controller->data;
			
			switch ($Controller->action) {
				case $loginAction:
					$username = $data[$Controller->Auth->userModel][$Controller->Auth->fields['username']];			
					$password = $data[$Controller->Auth->userModel][$Controller->Auth->fields['password']];
					
					if (!empty($username) && !empty($password) && $data[$Controller->Auth->userModel]['auto_login'] == 1) {
						if ($this->debug) {
							$Controller->flash('(Script debugging, please ignore) Login requested, cookie being saved.', 'notification');
						}
						$this->save($username, $password, $Controller);
					} else if ($data[$Controller->Auth->userModel]['auto_login'] == 0) {
						if ($this->debug) {
							$Controller->flash('(Script debugging, please ignore) Cookie deleted because auto-login is off.', 'notification');
						}
						$this->delete();
					}
				break;
				
				case $logoutAction:
					if ($this->debug) {
						$Controller->flash('(Script debugging, please ignore) Logout requested, cookie being deleted.', 'notification');
					}
					$this->delete();
				break;
			}
		}
	}

	/**
	 * Remember the user information
	 * @param string $username
	 * @param string $password
	 * @param object $Controller
	 * @return void
	 */
	function save($username, $password, $Controller) {
		$time = time();
		$cookie = array();
		if ($this->debug) {
			$Controller->flash('(Script debugging, please ignore) Cookie saved with email address "'.$username.'"', 'notification');
		}		
		$cookie[$Controller->Auth->fields['username']] = $username;
		$cookie[$Controller->Auth->fields['password']] = $password; // already hashed from auth
		$cookie['hash'] = $Controller->Auth->password($username . $time);
		$cookie['time'] = $time;
		
		/* Phantom's hack to make this work on the production server:
		 * For some reason, setting encryption to true here (third parameter) makes the cookie's keys and values
		 * both into gibberish, meaning the proper key values are not found, and occasionally keys have invalid characters
		 * in them (=,; \t\r\n\013\014).
		 * This will need to be fixed so cookies can be encrypted. */
		$this->Cookie->write($this->cookieName, $cookie, false, $this->expires);
	}

	/**
	 * Delete the cookie
	 * @return void
	 */
	function delete() {
		$this->Cookie->delete($this->cookieName);
	}
	
}
?>