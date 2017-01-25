<?php
class AppController extends Controller {
	var $helpers = array(
		'Js' => array('Prototype'), 
		'Paginator', 
		'Text', 
		'Time', 
		'Flash', 
		'Tree', 
		'Permission'
	);
	var $components = array( 
		'Auth' => array(
			'ajaxLogin' => '/login',
			'authorize' => 'controller',
			'autoRedirect' => false,
			'fields' => array(
				'username' => 'email', 
				'password' => 'password'
			),
			'loginAction' => array(
				'controller' => 'users',
				'action' => 'login'
			),
			'loginRedirect' => '/',
			'loginError' => 'Login invalid. Email or password incorrect.',
			'logoutRedirect' => '/',
			'userModel' => 'User',
			'userScope' => array(
				'User.active' => 1
			),
		),
		'RequestHandler', 
		'Recaptcha', 
		'Session',
		'AutoLogin' => array(
			'cookieName' => 'rememberMe',
			'expires' => '+1 year',
			'settings' => array(
				'controller' => 'users',
				'loginAction' => 'login',
				'logoutAction' => 'logout'
			)
		)
	);
	var $paginate = array(
		'Article' => array(
			'limit' => 10,
			'page' => 1,
			'url' => array(
				'controller' => 'articles',
				'action' => 'view'
			),
			'order' => array(
				'Article.sticky' => 'desc',
				'Article.published_date' => 'desc',
				'Article.id' => 'desc'
			),
			'conditions' => array(
				'Article.is_published' => 1,
			),
			'fields' => array(
				'Article.id',
				'Article.title',
				'Article.body',
				'Article.published_date',
				'Article.is_published',
				'Article.comments_enabled',
				'Article.user_id',
				'Article.sticky',
				'Article.slug'
			),
			'contain' => array(
				'User' => array('fields' => array('User.id', 'User.name')),
				'Tag' => array('fields' => array('Tag.id', 'Tag.name')),
				'Comment' => array('fields' => array('Comment.id'))
			)
		)
	);
	
	function beforeFilter() {
		if (isset($_GET['flush'])) {
			Cache::clear();
			clearCache();	
		}
		
		/*
		// Enable FireCake
		App::import('Vendor', 'FireCake');
		
		// Disable FirePHP output if debug is set to zero
		if (Configure::read('debug') == 0) {
			FireCake::disable();
		}
		*/
		
		// Set up Recaptcha keys
		switch($_SERVER['SERVER_NAME']){
			case 'icemiller.cberdata.org':
				$this->Recaptcha->publickey = '6LeM9b4SAAAAAMfXEuJwpXf4jcTOCgqffuoXI-Iz';
				$this->Recaptcha->privatekey = '6LeM9b4SAAAAALTyOVxzRThkaE8_Lth-0xQMgLmE';
				break;
			default:
				$this->Recaptcha->publickey = '6Lfc9b4SAAAAANzIsakp1GrIre2QxSRtSx7Fw2bX';
				$this->Recaptcha->privatekey = '6Lfc9b4SAAAAAOrFVRkr4ZVlwlRk04TdMAYbFNJY';
				break;
		}
		
		// Logins don't work if this info is set in $this->components for some reason 
		$this->Auth->fields = array('username' => 'email', 'password' => 'password');
		
		/* Use either 'log in' or 'forbidden' error message as the authError,
		 * depending on whether the user is logged in or not. */
		$this->Auth->authError = ($this->Auth->user()) ? 
			'You do not have permission to access that page.' :
			'Please <a href="/login">log in</a>.';
	}
	
	function isAuthorized() {
		return $this->__permitted($this->name, $this->action);
	}
 
	function beforeRender() {		
		$this->__prepareFlashMessages();
		if (isset($this->require_captcha)) {
			$this->set('require_captcha', $this->require_captcha);
		}
	}
	
	function flash($message, $class = 'notification') {
		$old = $this->Session->read('messages');
		$old[] = compact('message', 'class');
		$this->Session->write('messages', $old );
	}
	
	function __prepareFlashMessages() {
		return;
		$messages = $this->Session->read('messages');
		
		if ($this->Session->read('suppress_auth_error_msgs')) {
			$this->Session->delete('suppress_auth_error_msgs');
			$this->Session->delete('Message.auth');
		} elseif($auth_error = $this->Session->read('Message.auth')) {
			$messages['error'][] = $auth_error;
			$this->Session->delete('Message.auth');
		}
		
		$html = '';
		if ($messages) {
			foreach ($messages as $type => $msgs) {
				foreach ($msgs as $msg) {
					if (! empty($msg)) {
						$img_icon = $this->__getFlashMsgIcon($type);
						if (is_array($msg)) {
							$message = $msg['message'];
						} else {
							$message = $msg;
						}
						$html .= 
							"<li class=\"$type\" title=\"Click to close\" onclick=\"Effect.Fade(this)\">
								<p>
									<img src=\"$img_icon\" />
									$message
								</p>
							</li>";
					}
				}
			}
			if (true || $include_wrapper) {
				$html = "<ul>$html</ul>";
			}
			$this->Session->delete('messages');
			$this->Session->write('prepared_flash_messages', $html);
		}	
	}
	
	function __getFlashMsgIcon($type) {
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
	
	function __setBreadcrumbs($breadcrumbs = array()) {
		$breadcrumbs = array_merge(array('/' => 'Home'), $breadcrumbs);
		$links = array();
		foreach ($breadcrumbs as $path => $title) {
			$links[] = '<a href="'.$path.'">'.$title.'</a>';
		}
		$this->set('breadcrumbs', implode(' &raquo; ', $links));	
	}
	
	
	function __recaptchaIsValid() {
		if (! isset($this->require_captcha) || ! $this->require_captcha) {
			return true;
		}
		if ($this->Recaptcha->valid($this->params['form'])) {
			return true;
		}
		$this->set('recaptcha_error', 'Try typing those CAPTCHA challenge words again. And do it way better than last time.');
		return false;
	}
	
	// Should be (nearly) identical to PermissionHelper::__permitted()
	// Pulled from http://www.studiocanaria.com/articles/cakephp_auth_component_users_groups_permissions_revisited
	function __permitted($controllerName, $actionName) {
		// Note that there is an identical function in /app/views/helpers/permission.php
		$debug_output = $dont_read_from_session = Configure::read('permissions_debug_mode');
		
		//Ensure checks are all made lower case
		$controllerName = low($controllerName);
		$actionName = low($actionName);
		//If permissions have not been cached to session...
		if ($dont_read_from_session || ! $this->Session->check('Permissions')){
			$permissions = $this->getPermissions();
			//write the permissions array to session
			$this->Session->write('Permissions', $permissions);
		} else {
			//...they have been cached already, so retrieve them
			$permissions = $this->Session->read('Permissions');
		}
		
		//Now iterate through permissions for a positive match
		foreach ($permissions as $permission) {
			if ($permission == '*') {
				if ($debug_output) $this->flash("User has permissions for $controllerName:$actionName (*)", 'success');
				return true;//Super Admin Bypass Found
			}
			if ($permission == $controllerName.':*') {
				if ($debug_output) $this->flash("User has permissions for $controllerName:$actionName ($controllerName:*)", 'success');
				return true;//Controller Wide Bypass Found
			}
			if ($permission == "$controllerName:$actionName") {
				if ($debug_output) $this->flash("User has permissions for $controllerName:$actionName", 'success');
				return true;//Specific permission found
			}
		}
		if ($debug_output) $this->flash("User DOES NOT have permission for $controllerName:$actionName", 'error');
		return false;
	}
	
	function getPermissions() {
		//...then build permissions array and cache it
		$permissions = array();
		//everyone gets permission to log in and out
		$permissions[]='users:logout';
		$permissions[]='users:login';
		//Import the User Model so we can build up the permission cache
		App::import('Model', 'User');
		$thisUser = new User;
		//Now bring in the current users full record along with groups
		$thisRoles = $thisUser->find(array('User.id' => $this->Auth->user('id')));
		$thisRoles = $thisRoles['Role'];
		foreach($thisRoles as $thisRole) {
			$thisPermissions = $thisUser->Role->find(array('Role.id' => $thisRole['id']));
			$thisPermissions = $thisPermissions['Permission'];
			foreach($thisPermissions as $thisPermission) {
				$permissions[] = $thisPermission['name'];
			}
		}
		return $permissions;
	}
	
	// Set $selected_tags variable to be used to populate the 'selected tags' list
	// in adding/editing forms
	function setupPreselectedTags() {
		$model_class = $this->modelClass;
		$tag_object = $this->$model_class->Tag;
		$selected_tags = array();
		if (isset($this->data['Tag'])) {
			$tags = $this->data['Tag'];
			foreach ($tags as $key => $tag) {
				$tag_id = (is_array($tag)) ? $tag['id'] : $tag;
				$tag_object->id = $tag_id;
				if (is_array($tag) && isset($tag['name'])) {
					$tag_name = $tag['name'];
				} else {
					$tag_name = $tag_object->field('name');
				}
				$selected_tags[] = array(
					'id' => $tag_id,
					'name' => $tag_name
				);
			}
		}
		$this->set('selected_tags', $selected_tags);
	}
	
	function getTags($model = null, $id = null) {
		if (! $model) {
			$model = $this->modelClass;
		}
		
		$Tag =& ClassRegistry::init('Tag');
		return $Tag->getList($model, $id);
	}
	
	// Take an array of custom tag data and put the resulting IDs (found and created) into $this->data['Tag'][]
	function processCustomTags() {
		$model_class = $this->modelClass;
		if (! isset($this->data[$model_class]['custom_tags'])) {
			return;
		}
		if (empty($this->data[$model_class]['custom_tags'])) {
			return;
		}
		
		$custom_tags = $this->data[$model_class]['custom_tags'];
		$custom_tags = explode(',', $custom_tags);
		foreach ($custom_tags as $key => $ct) {
			$custom_tags[$key] = strtolower(trim($custom_tags[$key]));
		}
		$custom_tags = array_unique($custom_tags);
		
		$user_id = $this->Auth->user('id');
		$tag_object = $this->$model_class->Tag;
		foreach ($custom_tags as $ct) {
			$tag_id = $tag_object->field('id', array(
				'name' => $ct, 
				'selectable' => 1
			));
			
			// Create the custom tag if it does not already exist
			if (! $tag_id) {
				$tag_object->create();
				$tag_object->set(array(
					'name' => $ct,
					'person_id' => $user_id
				));
				$tag_object->save();
				$tag_id = $tag_object->id;
			}
			
			$this->data['Tag'][] = $tag_id;
		}
		$this->data['Tag'] = array_unique($this->data['Tag']);
		$this->data[$model_class]['custom_tags'] = '';
	}
	
	/*
	function getTopTags($limit = 5) {
		$model_class = $this->modelClass;
		$Tag =& ClassRegistry::init('Tag');
		return $Tag->getTop($model_class, $limit);
	}
		
    function _autoLogin($user) {
		$this->flash('You have been automatically logged in.', 'success');
	}

    function _autoLoginError($cookie) {
		$this->flash('Error automatically logging you in.', 'error');
    }
    */
}
?>