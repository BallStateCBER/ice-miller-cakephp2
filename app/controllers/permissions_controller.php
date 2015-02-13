<?php
class PermissionsController extends AppController {
	var $name = 'Permissions';
	var $scaffold;

	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('*');
		$this->require_captcha = false;
	}
	
	function index() {
		$this->redirect('/roles');
	}
	
	function add() {
		if (! empty($this->data)) {
			$this->Permission->set($this->data);
			if ($this->Permission->validates()) {
				if ($this->Permission->save($this->data, true)) {
					$this->flash('Permission added.', 'success');
				}
			}
			$this->redirect('/roles');
		}
	}
}
?>