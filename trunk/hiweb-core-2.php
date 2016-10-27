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
	error_reporting( E_ALL );
	ini_set( 'display_errors', 1 );
	
	require_once 'define.php';
	require_once HIWEB_DIR_INCLUDE . '/core.php';

	//todo

	/*$page = hiweb()->admin()->menu()->add_page( 'test' )->function_echo( function(){
		$form = hiweb()->form( 'test' );
		$form->field( 'test_field_1' )->title( 'Test field...' );
		$form->the();
	} );*/

	$page = hiweb()->admin()->menu()->add_theme_page( 'test' )->menu_title( 'Test' );
	$page->add_option( 'test_option' );