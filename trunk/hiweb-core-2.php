<?php
	/*
	Plugin Name: hiWeb Core 2
	Plugin URI: http://plugins.hiweb.moscow/core
	Description: Special framework plugin
	Version: 2.4.0.0
	Author: Den Media
	Author URI: http://hiweb.moscow
	*/
	
	require_once 'define.php';
	require_once HIWEB_DIR_INCLUDE . '/core.php';
	
	//todo
	$pt = hiweb()->wp()->cpt( 'test_cpt' );
	