<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.app.config
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
Router::parseExtensions('rss', 'xml');
Router::connect('/', 			array('controller' => 'pages', 'action' => 'home'));
Router::connect('/about', 		array('controller' => 'pages', 'action' => 'about'));
Router::connect('/contact', 	array('controller' => 'pages', 'action' => 'contact'));
Router::connect('/profile',		array('controller' => 'users', 'action' => 'my_account'));
Router::connect('/login', 		array('controller' => 'users', 'action' => 'login'));
Router::connect('/logout', 		array('controller' => 'users', 'action' => 'logout'));
Router::connect('/register', 	array('controller' => 'users', 'action' => 'register'));
Router::connect('/home/*', 		array('controller' => 'articles', 'action' => 'index'));
Router::connect('/search/*', 	array('controller' => 'articles', 'action' => 'search'));
Router::connect('/sitemap', 	array('controller' => 'pages', 'action' => 'sitemap', 'url' => array('ext' => 'xml')));

// The following content types will have /type/id route to /types/view/id
$content_types = array('article', 'user');
foreach ($content_types as $type) {
	Router::connect(
		"/$type/:id", 
		array('controller' => Inflector::pluralize($type), 'action' => 'view'),
		array('id' => '[0-9]+', 'pass' => array('id'))
	);
}

Router::connectNamed(array('slug'));
Router::connect(
	"/article/:slug", 
	array('controller' => 'articles', 'action' => 'view'),
	array('slug' => '[-_a-z0-9]+', 'pass' => array('slug'))
);

Router::connect(
	"/tag/:id/*", 
	array('controller' => 'articles', 'action' => 'tagged'),
	array('id' => '[0-9]+', 'pass' => array('id'))
);

Router::connect(
	"/articles/dated/:year", 
	array('controller' => 'articles', 'action' => 'dated'),
	array('year' => '[0-9]+', 'pass' => array('year'))
);

// The following content types will have /types/action/id:### route to /types/action/id
$controllers = array('articles');
$actions = array('add', 'edit', 'delete', 'publish', 'unpublish');
foreach ($controllers as $controller) {
	foreach ($actions as $action) {
		Router::connect(
			"/$controller/$action/:id", 
			array('controller' => $controller, 'action' => $action),
			array('id' => '[0-9]+', 'pass' => array('id'))
		);
	}
}