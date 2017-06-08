<?php

	hiweb()->errors();

	add_admin_menu_page( 'Test Option', 'theme' );

	$repeat = add_field( 'test', 'repeat', 'Повтор поле' )->location()->admin_menu( 'theme' )->get_field();
	$repeat->add_col( 'test1', 'images' )->width(2);
	$repeat->add_col( 'test2', 'text' );
	$input = $repeat->add_col('test3','repeat','Вложенный повтор')->width(4)->input();
	$input->add_col('b1','text');
	$input->add_col('b2','images');


	add_action( 'admin_footer', function(){
		if( have_rows( 'test', 'theme' ) ){
			while( have_rows( 'test', 'theme' ) ){
				$row = the_row();
				hiweb()->console( get_sub_field( 'test2' ) );
			}
		}
	} );