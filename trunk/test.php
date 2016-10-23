<div class="wrap"><h1>Test WORK!!!!</h1></div>
<?php

	$form = hiweb()->form( 'form1' );

	$input[] = hiweb()->inputs()->make( 'test3' )->label( 'Название пункта' );
	$input[] = hiweb()->inputs()->make( 'test' )->placholder( 'Check is...' )->label( 'Test LABEL' );
	$input[] = hiweb()->inputs()->make( 'test2', 'checkbox' )->label( 'Test LABEL 2' );

	$form->field( 'test3', 'repeat' )->fields($input);
	echo $form->get();

?>