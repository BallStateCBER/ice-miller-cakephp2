<?php
class Tag extends AppModel {  
	var $name = 'Tag';
	var $actAs = array('Tree');
	var $displayField = 'name';
	
	var $hasAndBelongsToMany = array(
		'Article' => array(
			'className'             => 'Article',
			'joinTable'             => 'articles_tags',
			'foreignKey'            => 'tag_id',
			'associationForeignKey' => 'article_id',
			'order'                 => 'Article.published_date DESC',
			'fields'				=> array('Article.id', 'Article.title', 'Article.published_date'),
			'conditions'			=> array('Article.is_published' => 1)
		)
	);
	
	// The $model parameter is a leftover from the tagging system developed for TheMuncieScene.com,
	// which separated tags into separate lists based on  
	function getList($model, $id = null) {
		$tree = $this->find('threaded', array( 
			'recursive' => 0,
			'fields' => array('Tag.name', 'Tag.id', 'Tag.parent_id', 'Tag.selectable'),
			'order' => array('Tag.name ASC')
		));
		return $tree;
	}
	
	function getTop($model, $limit = 5) {
		$plural_model = strtolower(Inflector::pluralize(strtolower($model)));
		$table = "{$plural_model}_tags";
		return $this->query("			
			SELECT $table.tag_id, tags.name, COUNT($table.tag_id) 
			AS occurrences 
			FROM $table, tags
			WHERE tags.id = $table.tag_id
			GROUP BY $table.tag_id
			ORDER BY occurrences DESC
			LIMIT $limit
		");	
	}
	
	function getCloud($model = null, $tag_limit = null) {
		if ($model == null) {
			$associated_models = array_keys($this->hasAndBelongsToMany);
		} else {
			$associated_models = array($model);
		}
		$tag_cloud = array();
		$highest_count = 0;
		foreach ($associated_models as $model) {
			$plural_model = strtolower(Inflector::pluralize(strtolower($model)));
			$table = "{$plural_model}_tags";
			$query = "SELECT $table.tag_id, tags.name FROM $table, tags WHERE tags.id = $table.tag_id";
			$query = "
				SELECT 
					articles_tags.tag_id, 
					tags.name, 
					COUNT(*) as occurrences 
				FROM 
					articles_tags, 
					tags 
				WHERE 
					tags.id = articles_tags.tag_id 
				GROUP BY 
					tags.name 
			";
			$query .= $tag_limit ? "ORDER BY occurrences DESC LIMIT $tag_limit" : 'ORDER BY tags.name ASC'; 
			$result = $this->query($query);
			foreach ($result as $row) {
				$tag_cloud[$row['tags']['name']] = array(
					'id' => $row[$table]['tag_id'],
					'count' => $row[0]['occurrences']
				);
				if ($highest_count < $row[0]['occurrences']) {
					$highest_count = $row[0]['occurrences'];
				}
			}
			foreach ($tag_cloud as $tag_name => $tag_array) {
				$tag_cloud[$tag_name]['size_percent'] = $tag_cloud[$tag_name]['count'] / $highest_count;
			}
		}
		ksort($tag_cloud);
		return $tag_cloud;
	}
	
	// This combined-output method prevents redundant database queries 
	function getListAndCloud($table) {
		$tag_cloud = array();		//array($tag_name => array('id' => $id, 'count' => $count), ...)  
		$tag_list = array();		//array($first_letter => array($tag_name => array('id' => $id, 'count' => $count), ...), ...)
		$result = $this->query("SELECT $table.tag_id, tags.name FROM $table, tags WHERE tags.id = $table.tag_id");
		$highest_count = 0;
		foreach ($result as $row) {
			$tag_name = $row['tags']['name'];
			$first_letter = $tag_name[0];
			if (! ctype_alpha($first_letter)) {
				$first_letter = '#';
			}
			if (isset($tag_cloud[$tag_name])) {
				$tag_cloud[$tag_name]['count']++;
				$tag_list[$first_letter][$tag_name]['count']++;
			} else {
				$tag_cloud[$tag_name] = array(
					'id' => $row[$table]['tag_id'],
					'count' => 1
				);
				$tag_list[$first_letter][$tag_name] = array(
					'id' => $row[$table]['tag_id'],
					'count' => 1
				);
			}
			if ($highest_count < $tag_cloud[$tag_name]['count']) {
				$highest_count = $tag_cloud[$tag_name]['count'];
			}
		}
		foreach ($tag_list as $letter => $tags) {
			ksort($tag_list[$letter]);
		}
		ksort($tag_list);
		foreach ($tag_cloud as $tag_name => $tag_array) {
			$tag_cloud[$tag_name]['size_percent'] = $tag_cloud[$tag_name]['count'] / $highest_count;
		}
		ksort($tag_cloud);
		return array($tag_list, $tag_cloud);
	}
	
	function getCountRange($tag_cloud) {
		$count_min = $count_max = null;
		foreach ($tag_cloud as $tag_name => $tag_info) {
			$count = $tag_info['count'];
			if (! isset($count_min)) {
				$count_min = $count_max = $count;	
			} else {
				if ($count > $count_max) {
					$count_max = $count;
				}
				if ($count < $count_min) {
					$count_min = $count;
				}
			}
		}	
		return array($count_min, $count_max);
	}
	
	// Takes a tag name, adds it to the database if not found, and returns its ID
	function getId($tag_name = '') {
		
		// Look up tag
		$tag_name = strtolower(trim($tag_name));
		if ($tag_name == '') {
			return false;	
		}
		$result = $this->find('list', array(
			'conditions' => array('name' => $tag_name)
		));
		
		// Create new tag
		if (empty($result)) {
			$this->create(array('Tag' => array('name' => $tag_name)));
			$this->save();
			return $this->id;
		// Return ID of existing tag
		} else {
			return array_pop(array_keys($result));
		}
	}
}