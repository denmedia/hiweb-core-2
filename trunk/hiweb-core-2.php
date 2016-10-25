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

	///
	$field1 = hiweb()->input( 'test1' )->label( 'Тест-поле 1' );
	$field2 = hiweb()->input( 'test2', 'checkbox' )->label( 'Тест-поле 2' );
	$meta_box = hiweb()->meta_box( 'test' )->title( 'Пробное репит-поле' );
	$field = $meta_box->add_field( 'test', 'repeat' );
	$field->cols( array( $field1, $field2 ) );
	$field = $meta_box->add_field( 'test3', 'repeat' );
	$field->cols( array( $field2, $field1 ) );
	$meta_box->screen()->post_type()->or_in()->taxonomies()->or_in()->user_edit();
	
	add_action( 'wp', function(){



		if( hiweb()->meta( 'test3', 1 )->has_rows() ){
			while( hiweb()->meta( 'test3' )->the_row() ){
				?><p><?php hiweb()->meta('test3')->the_subfield('test1');?></p><?php
			}
		}



	} );
