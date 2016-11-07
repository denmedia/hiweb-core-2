<?php
	/*
	Plugin Name: hiWeb Core 2
	Plugin URI: http://plugins.hiweb.moscow/core
	Description: Special framework plugin
	Version: 2.0.0.0
	Author: Den Media
	Author URI: http://hiweb.moscow
	*/
	
	require_once 'define.php';
	require_once HIWEB_DIR_INCLUDE . '/core.php';
	
	//todo
	hiweb()->post_types()->add_field('test');
	$repeat = hiweb()->post_types()->add_field( 'My test field', 'repeat' );
	$repeat->add_col('test1','image')->width(20);
	$repeat->add_col('test2');
	hiweb()->post_type('post')->add_field('Test isert field');
	hiweb()->post_type('page')->add_field('Test isert field 3');