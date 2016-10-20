<div class="wrap"><h1>Test WORK!!!!</h1></div>
<?php

	$form = hiweb()->form();
	$form->input('test', 'checkbox');
	echo hiweb()->form()->get();

?>