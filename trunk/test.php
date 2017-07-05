<?php

	hiweb()->errors();

	add_admin_menu_page( 'Test Option', 'theme' );

	$repeat = add_field( 'test', 'repeat', 'Повтор поле' )->location()->admin_menu( 'theme' )->get_field();
	$repeat->add_col( 'test123', 'editor' )->width( .5 );
	$repeat2 = $repeat->add_col( 'test324', 'repeat' )->input();
	$repeat2->add_col( 'test45', 'checkbox' );
	$repeat2->add_col( 'test54643', 'editor' );
	$repeat2->add_col( 'tewtewr', 'post' );