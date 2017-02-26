<?php
	/*
	Plugin Name: hiWeb Core 2
	Plugin URI: http://plugins.hiweb.moscow/core
	Description: Special framework plugin
	Version: 2.0.0.0
	Author: Den Media
	Author URI: http://hiweb.moscow
	*/

	///Localization
	function hw_core_load_theme_textdomain(){
		global $l10n;
		$mo_file_path = __DIR__ . '/languages/hw-core-2-' . get_locale() . '.mo';
		$B = load_textdomain( 'hw-core-2', $mo_file_path );
	}

	add_action( 'after_setup_theme', 'hw_core_load_theme_textdomain' );

	if( version_compare( PHP_VERSION, '5.4.0' ) >= 0 ){
		///
		require_once 'include/core.php';
		require_once 'include/short_functions.php';
		///

	} else {
		function hw_core_php_version_error(){
			include 'views/core-error-php-version.php';
		}

		add_action( 'admin_notices', 'hw_core_php_version_error' );
	}

	///TODO-
	add_admin_menu_page('Тестовая страница','theme');
	add_field( 'test', 'text', 'Првоерка поля и контекста' )->location()->admin_menu('theme');