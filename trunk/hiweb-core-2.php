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

	$page = hiweb()->options()->page('Photobank')->show_in_admin_menu('dashicons-format-gallery', 4);
	$page->add_option('Test 1');
	$page->add_option('Test 2','image');
	$page->add_option('Test 3','gallery');


	$page = hiweb()->options()->page('Photobank settings')->show_in_admin_submenu('options-general.php');
	$page->add_option('sett_1');
	$page->add_option('sett_2','repeat')->input()->cols(array(
		hiweb()->input('img','image'),
		hiweb()->input('text')
	));