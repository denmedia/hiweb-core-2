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


	$meta = hiweb()->meta_box('product_settings','Настройки товара');
	$meta->screen()->post_type('post');
	$meta->add_field('product_image','image')->title('Изображение товара')->description('Выберите изображение для своего продукта')->width(35)->preview_size(400,150);
	$meta->add_field('product_price')->title('Стоимость товара')->label(' руб.')->default_value(0)->width(35);
	$meta->add_field('product_price_2')->title('Добавочная стоимость')->label(' руб.')->default_value(0)->width(35);
	$meta->add_field('product_gallery','gallery')->title('Остальные фотографии');
	$meta->add_field('product_addition','repeat')->title('Дополнительно к товару')->cols(array(
		hiweb()->input('enable','checkbox'),
		hiweb()->input('color')->title('Цвет')->placholder('Название цвета'),
		hiweb()->input('image','image')->title('Изорбражение цвета'),
		hiweb()->input('price')->title('Стоимость')->default_value(0)
	));