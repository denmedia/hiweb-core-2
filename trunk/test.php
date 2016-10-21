<div class="wrap"><h1>Test WORK!!!!</h1></div>
<?php

	$form = hiweb()->form( 'form1' );
	$form->field('test')->placholder('Check is...')->label('Test LABEL');
	$form->field('test2','checkbox')->label('Test LABEL 2');

	$input[] = hiweb()->input()->make( 'test' )->placholder( 'Check is...' )->label( 'Test LABEL' );
	$input[] = hiweb()->input()->make( 'test2', 'checkbox' )->label( 'Test LABEL 2' );

	$form->field( 'test3', 'repeat' )->fields($input);
	echo $form->get();

?>