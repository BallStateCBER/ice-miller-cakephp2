<?php
App::import('Sanitize');

class ArticlesController extends AppController {
	var $name = 'Articles';
	var $components = array(); //'Search.Prg'
	var $helpers = array('Time', 'Tree');
	// Article pagination options are defined in AppController
		
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('*');
		$this->Auth->deny('add', 'edit', 'delete', 'publish', 'drafts', 'reindex');
		$this->require_captcha = false;
		$this->paginate['Article']['conditions']['Article.published_date <='] = date('Y-m-d').' 23:59:59';
	}
	
	function beforeRender() {
		parent::beforeRender();
	}
	
	/* A special method is needed here because edit, delete, etc. permissions
	 * are only given to administrators and apply to all articles. But non-administrators
	 * may also perform those actions, provided they are the articles' authors. */
	function isAuthorized() {
		switch ($this->action) {
			case 'edit':
			case 'delete':
			case 'publish':
			case 'unpublish':
				$this->Article->id = $this->params['pass'][0];
				$author_id = $this->Article->field('user_id');
				$user_id = $this->Auth->user('id');
				return $author_id == $user_id || $this->__permitted($this->name, $this->action);
			default:
				return $this->__permitted($this->name, $this->action); 
		}
	}
	
	/* Returns TRUE if the currently logged-in user is the author of the Article
	 * currently selected (requires $this->Article->id to be set) or FALSE otherwise. */
	function __isUserAuthor() {
		if (! $user_id = $this->Auth->user('id')) {
			return false;
		}
		if (isset($this->Article->data['Article']['user_id'])) {
			$owner_id = $this->Article->data['Article']['user_id'];
		} elseif ($this->Article->id) {
			$owner_id = $this->Article->field('user_id');
		} else {
			$owner_id = null;
		}
		return ($user_id == $owner_id);
	}
    
	function index() {
		if ($this->RequestHandler->isRss()) {
			$articles = $this->Article->find('all', array(
				'limit' => 20, 
				'order' => 'Article.published_date DESC',
				'conditions' => array(
					'Article.is_published' => 1,
					'Article.published_date <=' => date('Y-m-d').' 23:59:59'
				),
				'contain' => array(
					'User' => array(
						'fields' => array(
							'User.id', 'User.name'
						)
					)
				)
			));
		} else {
			$articles = $this->paginate('Article');
		}
		
        // If an array is being requested by an element
        if (isset($this->params['requested'])) {
            return $articles;
        }
        
        $this->__setBreadcrumbs(array());
		$this->set(array(
			'title_for_layout' => false,
			'articles' => $articles
		));
    }
	
	function view() {
		if (! isset($this->params['pass'][0])) {
			$this->flash('Sorry, that article was not found. Details: Unspecified article ID.', 'error');
			$this->redirect('/');	
		}
		if (is_numeric($this->params['pass'][0])) {
			$this->Article->id = $id = $this->params['pass'][0];
			$article = $this->Article->find('first', array(
				'conditions' => array('Article.id' => $id),
				'contain' => array('User', 'Tag')
			));
		} else {
			$slug = $this->params['pass'][0];
			$article = $this->Article->findBySlug($slug);
			$id = $this->Article->id;	
		}
		if (! isset($article) || ! $article) {
			$this->flash('Sorry, the specified article does not exist.', 'error');
			$this->redirect('/');
		}
		
		// If an article is a draft (is_published = 0), 
		// only users with edit permission or the author can view it
		if (! $article['Article']['is_published']) {
			$may_edit = $this->__permitted('Article', 'edit');
			$is_author = $this->Auth->user('id') == $article['Article']['user_id'];
			if (! $may_edit && ! $is_author) {
				$this->flash('Sorry, that article is currently unavailable.', 'error');
				$this->redirect('/');
			}
		}
		
		$title = $article['Article']['title'];
		$this->__setBreadcrumbs(array(
			"/article/$id" => $title
		));
		$this->set(array(
			'article' => $article,
			'title_for_layout' => $title,
			'require_captcha' => false,
			'comments' => $this->Article->Comment->find('threaded', array(
				'conditions' => array('Comment.article_id' => $id),
				'order' => array('Comment.created DESC'),
				'limit' => false
			)),
			'comments_count' => count($this->Article->Comment->find('all', array('fields' => array('Comment.id')))),
			'facebook_og_meta_tags' => array(
				'title' => $title,
				'type' => 'article',
				'url' => "http://icemiller.cberdata.org/article/$id"
			)
		));
	}
	
	function add() {
		if (! empty($this->data)) {
			$this->data['Article']['user_id'] = $this->Auth->user('id');
			$this->processCustomTags();
			$this->Article->set($this->data);
			$validates = $this->Article->validates();
			if ($this->__recaptchaIsValid()) {
				if ($validates) {
					if ($this->data = $this->Article->save($this->data, true)) {
						$this->redirect(Router::url(array(
							'controller' => 'articles',
							'action' => 'view',
							'slug' => $this->data['Article']['slug']
						)));
					}
				} else {
					$this->flash('Please correct the errors indicated below before submitting this article.', 'error');	
				}
			} else {
				$this->flash('Invalid CAPTCHA response. Please try again.', 'error');
			}
		}
		$this->setupPreselectedTags();
		
		$this->__setBreadcrumbs(array(
			'/articles/add' => 'Submit an Article'
		));
		$this->set(array(
			'available_tags' => $this->getTags(),
			'title_for_layout' => 'Submit an Article'
		));
	}
	
	function edit($id = null) {
		// Make sure ID is specified
		if (! $id) {
			$this->flash('No ID specified. Which article do you want to edit?', 'error');
			$this->redirect($this->referer());
			return false;
		}

		// Make sure ID is valid
		$this->Article->id = $id;
		$this->Article->read();
		if (! $this->Article->data) {
			$this->flash('Invalid article selected.', 'error');
			$this->redirect($this->referer());
			return false;
		}
		
		$title = $this->Article->data['Article']['title'];
		$was_published = $this->Article->data['Article']['is_published'];
		
		// Make sure user has permission
		if (! ($this->__isUserAuthor() || $this->__permitted('articles', 'edit'))) {
			$this->flash('You don\'t have permission to edit that article.', 'error');
			$this->redirect($this->referer());
			return false;
		}
		
		if (empty($this->data)) {
			$this->data = $this->Article->data;
		} else {
			$this->processCustomTags();
			$this->Article->set($this->data);
			$validates = $this->Article->validates();
			if ($this->__recaptchaIsValid()) {
				if ($validates) {
					if ($this->Article->save($this->data)) {
						$this->flash("Article updated.", 'success');
						$is_published = $this->data['Article']['is_published'];
						if ($is_published > $was_published) {
							if ($this->Article->publish()) {
								$this->flash('Article published.', 'success');
							} else {
								$this->flash('Your article was entered into the database, but there was an error publishing it to the website.', 'error');
							}
						} elseif ($is_published < $was_published) {
							$this->flash('Article moved to Drafts.', 'success');
						}
						$this->redirect("/article/$id");
					} else {
						$this->flash('There was an error updating this article.', 'error');
					}
				} else {
					$this->flash('Please correct the errors indicated below before submitting this article.', 'error');	
				}
			} else {
				$this->flash('Invalid CAPTCHA response. Please try again.', 'error');
			}
		}
		$this->setupPreselectedTags();
		
		$this->__setBreadcrumbs(array(
			Router::url(array(
				'controller' => 'articles',
				'action' => 'view',
				'slug' => $this->Article->data['Article']['slug']
			)) => $title,
			Router::url(array(
				'controller' => 'articles',
				'action' => 'edit',
				'id' => $id
			)) => 'Edit'
		));
		$this->set(array(
			'available_tags' => $this->getTags('Article', $id),
			'title_for_layout' => "Edit $title"
		));
	}
		
	function drafts() {
		
		// Get author ID
		if (! $user_id = $this->Auth->user('id')) {
			// If user is not logged in
			$this->flash('You\'ll need to <a href="/login">log in</a> before accessing your article drafts.', 'error');
			if (isset($this->params['requested'])) {
				return array();
			} else {
				$this->redirect('/');
			}
		}
		
		// Get articles
		$articles = $this->Article->find('all', array(
			'conditions' => array('Article.user_id' => $user_id, 'Article.is_published' => 0),
			'order' => array('Article.modified DESC'),
			'fields' => array('Article.id', 'Article.title', 'Article.modified', 'Article.slug'),
			'contain' => false
		));
		
		// Either return them as an array or set them as view variables
        if (isset($this->params['requested'])) {
            return $articles;
        }
        $this->__setBreadcrumbs(array(
			Router::url(array(
				'controller' => 'articles',
				'action' => 'drafts'
			)) => 'Article Drafts' 
		));
		$this->set(array(
			'articles' => $articles,
			'title_for_layout' => 'My Article Drafts'
		));
	}
	
	function publish($id = null) {
		$this->Article->id = $id;
		$this->Article->read(array('is_published', 'title'));
		$is_published = $this->Article->data['Article']['is_published'];
		$title = $this->Article->data['Article']['title'];
		if ($is_published) {
			$this->flash("<em>$title</em> is already published.");
		} elseif ($this->Article->publish()) {
			$this->flash("<em>$title</em> has been published.", 'success');
		} else {
			$this->flash("There was an error publishing <em>$title</em>.", 'error');
		}
		$this->redirect($this->referer());
	}
	
	function unpublish($id = null) {
		$this->Article->id = $id;
		$this->Article->read(array('is_published', 'title'));
		$is_published = $this->Article->data['Article']['is_published'];
		$title = $this->Article->data['Article']['title'];
		if (! $is_published) {
			$this->flash("<em>$title</em> can't be unpublished because it hasn't been published yet. Would you like to <a href=\"/articles/publish/$id\">publish it</a> instead?");
		} elseif ($this->Article->unpublish()) {
			$this->flash("<em>$title</em> has been unpublished and moved to <a href=\"/articles/drafts\">drafts</a>.", 'success');
		} else {
			$this->flash("There was an error unpublishing <em>$title</em>.", 'error');
		} 
		$this->redirect($this->referer());
	}
    
	function tagged($tag_id = null) {
		$this->Article->Tag->id = $tag_id;
		if (! is_numeric($tag_id) || ! $tag_name = ucwords($this->Article->Tag->field('name'))) {
			$this->flash('Sorry, but the specified tag was not found.', 'error');
			$this->redirect('/articles/tags');
		}

		// Set up the pagination query
		$this->Article->bindModel(
			array(
				'hasOne' => array(
					'ArticlesTag',
					'FilterTag' => array(
						'className' => 'Tag',
						'foreignKey' => false,
						'conditions' => array('FilterTag.id = ArticlesTag.tag_id')
					)
				)
			),
			false
		);
		$this->paginate['Article'] = array(
			'conditions' => array(
				'Article.is_published' => 1,
				'ArticlesTag.tag_id' => $tag_id
			),
			'fields' => array(
				'Article.id',
				'Article.title',
				'Article.published_date',
				'Article.slug'
			),
			'order' => 'Article.published_date DESC',
			'limit' => 20,
			'contain' => array(
				'ArticlesTag'
			)
		);
		
		$this->__setBreadcrumbs(array(
			'/tags' => 'Tags',
			"/tag/$tag_id" => $tag_name 
		));
		$this->set(array(
			'title_for_layout' => 'Articles Tagged With: '.ucwords($tag_name),
			'tagName' => $tag_name,
			'tagID' => $tag_id,
			'articles' => $this->paginate('Article')
		));
	}
	
	function getTopTags($limit = 5) {
		if (! is_numeric($limit)) {
			return;
		}
		return $this->Article->query("			
			SELECT
				articles_tags.tag_id, 
				tags.name, 
				COUNT(articles_tags.tag_id) AS occurrences 
			FROM 
				tags	
				INNER JOIN articles_tags ON (tags.id = articles_tags.tag_id)
				INNER JOIN articles ON (articles_tags.article_id = articles.id)
			WHERE 
				articles.is_published = 1
			GROUP BY 
				articles_tags.tag_id
			ORDER BY 
				occurrences DESC
			LIMIT $limit
		");
	}
	
	function delete($id = null) {
		if (! $id) {
			$this->flash('No ID specified. Which '.strtolower($model_class).' do you want to delete?', 'error');
			$this->redirect($this->referer());
			return false;
		}
		if ($this->Article->delete($id)) {
			$this->flash('Article deleted.', 'success');
		} else {
			$this->flash('There was an error removing this article.', 'error');
		}
		$this->redirect($this->referer());
	}
	
	function mine() {
		$this->__setBreadcrumbs(array(
			"/articles/mine" => 'My Published Articles'
		));
		$this->set(array(
			'title_for_layout' => 'My Published Articles',
			'articles' => $this->Article->find('all', array(
				'conditions' => array(
					'Article.user_id' => $this->Auth->user('id')
				),
				'order' => array(
					'Article.published_date DESC', 'Article.modified DESC'
				),
				'fields' => array(
					'Article.id', 'Article.title', 'Article.published_date', 'Article.slug'
				),
				'contain' => false
			))
		));
	}
	
	function search($query = '') {		
		if (! $query && isset($this->data['Article']['query'])) {
			$query = $this->data['Article']['query'];
		}
		
		$list_start_number = 1;
		$results = array();
		$authors = array();
		
		if ($query) {
			
			// Append asterisks to the end of each word
			if (strpos($query, ' ') === false) {
				$query = "$query*";
			} else {
				$query_split = explode(' ', $query);
				$query = implode('* ', $query_split); 
			}
			
			$this->loadModel('SearchIndex');
			$this->SearchIndex->searchModels(array('Article'));
			$results_per_page = 20;
			$search_mode = 'IN BOOLEAN MODE'; //IN NATURAL LANGUAGE MODE WITH QUERY EXPANSION
			$this->paginate = array(
				'limit' => $results_per_page,
				'conditions' =>  array(
					"MATCH(SearchIndex.data) AGAINST('$query' $search_mode)",
					'Article.is_published' => 1,
					'Article.published_date <=' => date('Y-m-d').' 23:59:59'
				),
				'fields' => array('SearchIndex.id'),
				'contain' => array(
					'Article' => array(
						'fields' => array(
							'Article.id', 
							'Article.title', 
							'Article.published_date', 
							'Article.user_id', 
							'Article.body', 
							'Article.slug'
						)
					)
				)
			);
			$results = $this->paginate('SearchIndex');
			
			// Remove asterisks from query
			$query = str_replace('*', '', $query);
			
			if (isset($this->params['paging']['SearchIndex']['page'])) {
				$page = $this->params['paging']['SearchIndex']['page'];
				if ($page > 1) {
					$list_start_number = ($page * $results_per_page) - 1;
				}
			}
			
			foreach ($results as $result) {
				$author_id = $result['Article']['user_id'];
				if (! isset($authors[$author_id])) {
					$this->Article->User->id = $author_id;
					$authors[$author_id] = $this->Article->User->field('name');
				}
			}
		}
        
		//echo '<pre>'.print_r($results, true).'</pre>';
		
		$this->__setBreadcrumbs(array(
			'/search' => 'Search' 
		));
		$this->set(array(
			'title_for_layout' => "Search: $query",
			'articles' => $results,
			'authors' => $authors,
			'list_start_number' => $list_start_number,
			'query' => $query
		));
	}
	
	// Writes (or rewrites) the rows corresponding to articles in the search_index table
	function reindex() {
		$this->Article->reindexAll();
		$this->flash('Articles reindexed in search index table.', 'success');
		$this->redirect('/');
	}
	
	// Shows a page with all of the (paginated) articles written by the selected author
	function by($id = null) {
		$this->paginate['Article']['conditions']['Article.user_id'] = $id;
		$articles = $this->paginate('Article');
		
		$this->Article->User->id = $id;
		if ($user_name = $this->Article->User->field('name')) {
	        $this->__setBreadcrumbs(array(
	        	Router::url(array('controller' => 'users', 'action' => 'view', 'id' => $id)) => $user_name,
	        	Router::url(array('controller' => 'articles', 'action' => 'by', 'id' => $id)) => "Articles" 
	        ));
			$this->set(array(
				'title_for_layout' => "Articles by $user_name",
				'user_name' => $user_name,
				'user_id' => $id,
				'articles' => $articles
			));
		} else {
			$this->flash("Sorry, but that author (id: $id) was not found in the database.", 'error');
			$this->redirect('/');	
		}
	}
	
	/* Shows a list of articles for the selected year, organized by month, 
	 * with links to the previous and next years when appropriate. */
	function dated($year = null) {
		if (is_numeric($year)) {
			$years = null;
			$articles = $this->Article->find('all', array(
				'order' => array(
					'Article.published_date ASC',
					'Article.id ASC',
				),
				'conditions' => array(
					'Article.is_published' => 1,
					'Article.published_date LIKE' => "$year-%"
				),
				'fields' => array(
					'Article.id', 'Article.title', 'Article.published_date', 'Article.slug'
				),
				'contain' => false
			));
			
			// Group by month
			$articles_by_month = array();
			foreach ($articles as $article) {
				$month = substr($article['Article']['published_date'], 5, 2);
				$articles_by_month[$month][] = $article['Article'];
			}
			$articles = $articles_by_month;
			
			// Determine if links should appear for the next and/or previous years
			$result = $this->Article->find('first', array(
				'conditions' => array(
					'Article.is_published' => 1,
					'Article.published_date LIKE' => ($year - 1).'-%'
				),
				'fields' => array('Article.id'),
				'contain' => false
			));
			$has_prev = $result != false;
			$result = $this->Article->find('first', array(
				'conditions' => array(
					'Article.is_published' => 1,
					'Article.published_date LIKE' => ($year + 1).'-%'
				),
				'fields' => array('Article.id'),
				'contain' => false
			));
			$has_next = $result != false;
			
			$this->__setBreadcrumbs(array(
	        	Router::url(array('controller' => 'articles', 'action' => 'dated')) => 'Articles by Year',
	        	Router::url(array('controller' => 'articles', 'action' => 'dated', 'year' => $year)) => $year 
	        ));
		} else {
			$has_prev = $has_next = $articles = false;
			$result = $this->Article->find('first', array(
				'conditions' => array('Article.is_published' => 1),
				'order' => array('Article.published_date DESC'),
				'fields' => array('Article.published_date'),
				'contain' => false
			));
			$max_year = substr($result['Article']['published_date'], 0, 4);
			$result = $this->Article->find('first', array(
				'conditions' => array('Article.is_published' => 1),
				'order' => array('Article.published_date ASC'),
				'fields' => array('Article.published_date'),
				'contain' => false
			));
			$min_year = substr($result['Article']['published_date'], 0, 4);
			$years = range($max_year, $min_year);
			
			$this->__setBreadcrumbs(array(
	        	Router::url(array('controller' => 'articles', 'action' => 'dated')) => 'Articles by Year' 
	        ));
		}
		$this->set(array(
			'title_for_layout' => "Articles from $year",
			'articles' => $articles,
			'year' => $year,
			'years' => $years,
			'has_prev' => $has_prev,
			'has_next' => $has_next
		));
	}

	/* Uses the Weekly Commentaries website's /commentaries/export page to read all of the published
	 * commentaries and feed them into this database as new articles. */
	function import_commentaries($id = null) {
		// Development server
		if (stripos($_SERVER['SERVER_NAME'], 'localhost') !== false) {
			$url = 'http://commentaries.localhost/commentaries/export';
		// Production server
		} else {
			$url = 'http://commentaries.cberdata.org/commentaries/export';	
		}
		if ($id) {
			$url .= '/'.$id;	
		}
		$results = file_get_contents($url);
		$commentaries = unserialize($results);
		$error_flag = false;
		foreach ($commentaries as $commentary) {
			// Collect data
			$title = $commentary['Commentary']['title'];
			$body = $commentary['Commentary']['body'];
			$published_date = $commentary['Commentary']['published_date'];
			$author = $commentary['User']['name'];
			$tags = $commentary['Tag'];
			$is_published = 1;
			$comments_enabled = 1;
			
			// Check to see if this commentary is already in the database (based on the published date)
			$result = $this->Article->find('list', array('conditions' => array('published_date' => $published_date)));
			if (! empty($result)) {
				continue;
			}
			
			// Find author ID
			$result = $this->Article->User->find('list', array('conditions' => array('name' => $author)));
			if (empty($result)) {
				// Unrecognized author
				continue;
			}
			$user_id = array_pop(array_keys($result));
			
			// Find tag IDs
			$tag_ids = array();
			foreach ($tags as &$tag) {
				$tag_ids[] = $this->Article->Tag->getId($tag['name']);	
			}
			
			// Add commentary to database as a new article
			$this->Article->create(array(
				'Article' => compact('title', 'body', 'published_date', 'user_id', 'is_published', 'comments_enabled'),
				'Tag' => array_unique($tag_ids)
			));
			if (! $this->Article->save()) {
				$error_flag = true;
			}
		}
		$this->layout = 'ajax';
		$this->set(compact('error_flag'));
	}
}