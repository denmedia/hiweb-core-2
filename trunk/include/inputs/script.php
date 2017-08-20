<?php

	hiweb()->inputs()->register_type( 'script', 'hw_input_script' );


	class hw_input_script extends hw_input{

		public function html(){
			hiweb()->css( hiweb()->dir_vendors . '/codemirror/lib/codemirror.css' );
			hiweb()->css( hiweb()->dir_vendors . '/codemirror/addon/hint/show-hint.css' );
			hiweb()->js( hiweb()->dir_vendors . '/codemirror/lib/codemirror.js' );
			hiweb()->js( hiweb()->dir_vendors . '/codemirror/addon/hint/show-hint.js' );
			hiweb()->js( hiweb()->dir_vendors . '/codemirror/addon/hint/xml-hint.js' );
			hiweb()->js( hiweb()->dir_vendors . '/codemirror/addon/hint/html-hint.js' );
			hiweb()->js( hiweb()->dir_vendors . '/codemirror/mode/xml/xml.js' );
			hiweb()->js( hiweb()->dir_vendors . '/codemirror/mode/javascript/javascript.js' );
			hiweb()->js( hiweb()->dir_vendors . '/codemirror/mode/css/css.js' );
			hiweb()->js( hiweb()->dir_vendors . '/codemirror/mode/htmlmixed/htmlmixed.js' );
			//hiweb()->js( hiweb()->dir_vendors . '/codemirror/mode/htmlmixed/htmlmixed.js' );
			ob_start();
			?>
			<textarea name="<?= $this->name() ?>" id="<?= $this->id() ?>"><?= $this->value() ?></textarea>
			<script type="text/javascript">
                window.onload = function () {
                    var editor = CodeMirror.fromTextArea(document.getElementById("<?= $this->id() ?>"), {
                        mode: "text/html",
                        lineWrapping: true,
                        lineNumbers: true,
                        inputStyle: 'textarea',
                        extraKeys: {"Ctrl-Space": "autocomplete"}
                    });
                };
			</script>
			<?php
			return ob_get_clean();
		}

	}