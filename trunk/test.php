<?php

	hiweb()->errors();

	add_admin_menu_page( 'Test Option', 'theme' );

	$repeat = add_field( 'test', 'repeat', 'Повтор поле' )->location()->admin_menu( 'theme' )->get_field();
	$repeat->add_col( 'test123', 'editor' );