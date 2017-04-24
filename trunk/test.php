<?php

	hiweb()->errors();

	add_field( 'test', 'text', 'Проверка колонки' )->location()->post_type( 'post' )->columns_manager()->position( 2 );
	add_field( 'test2', 'image', 'Проверка' )->location()->post_type( 'post' )->columns_manager()->position( 1 );