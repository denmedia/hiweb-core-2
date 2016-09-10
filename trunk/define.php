<?php
	/**
	 * Created by PhpStorm.
	 * User: hiweb
	 * Date: 30.06.2016
	 * Time: 15:44
	 */

	if (!defined('HIWEB_DIR_BASE')) define('HIWEB_DIR_BASE', dirname(__FILE__));
	if (!defined('HIWEB_URL_BASE')) define('HIWEB_URL_BASE', plugin_dir_url(__FILE__));
	if (!defined('HIWEB_URL_CSS')) define('HIWEB_URL_CSS', HIWEB_URL_BASE.'/css');
	if (!defined('HIWEB_DIR_INCLUDE')) define('HIWEB_DIR_INCLUDE', HIWEB_DIR_BASE . '/include');
	if (!defined('HIWEB_DIR_MODULES')) define('HIWEB_DIR_MODULES', HIWEB_DIR_BASE . '/modules');