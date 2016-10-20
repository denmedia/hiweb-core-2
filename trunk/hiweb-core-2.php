<?php
	/*
	Plugin Name: hiWeb Core 2
	Plugin URI: http://plugins.hiweb.moscow/core
	Description: Special framework plugin
	Version: 2.7.0.0
	Author: Den Media
	Author URI: http://hiweb.moscow
	*/

	//todo!!!
	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	require_once 'define.php';
	require_once HIWEB_DIR_INCLUDE . '/core.php';
	
	//todo!!!
	$cpt = hiweb()->post_type('post');
	$metaBox = $cpt->add_meta_box('test');
	$metaBox->add_field('test_field')->label('Test Filed')->value('My value');
	//
	$pageContent = hiweb()->path()->get_content(HIWEB_DIR_BASE.'/test.php');
	$page = hiweb()->admin()->menu()->add_page('test_page')->menu_title('Test Page')->function_echo($pageContent);
