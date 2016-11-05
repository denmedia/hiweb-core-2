<?php
	/*
	Plugin Name: hiWeb Core 2
	Plugin URI: http://plugins.hiweb.moscow/core
	Description: Special framework plugin
	Version: 2.7.0.0
	Author: Den Media
	Author URI: http://hiweb.moscow
	*/
	
	require_once 'define.php';
	require_once HIWEB_DIR_INCLUDE . '/core.php';
	
	
	$my_page = hiweb()->post_type('post');
	$repeat = $my_page->add_field('Test','repeat');
	$repeat->add_field('Image','image');
	$repeat->add_field('Text','text');