<?php

	hiweb()->inputs()->register_type( 'editor', 'hw_input_editor' );
	hiweb()->fields()->register_content_type( 'editor', function( $value, $nl2br = false ){
		return $nl2br ? nl2br($value) : apply_filters('the_content', $value);
	} );

	class hw_input_editor extends hw_input{

		use hw_hidden_methods_props;


		public function html(){
			hiweb()->js(hiweb()->dir_js.'/input-editor.js');
			ob_start();
			add_filter( 'the_editor', [ $this, 'editor_html_filter' ], 10, 2 );
			wp_editor( $this->get_value(), $this->id(), $this->attributes() );
			remove_filter( 'the_editor', [ $this, 'editor_html_filter' ] );
			return ob_get_clean();
		}


		protected function editor_html_filter( $html ){
			ob_start();
			?>
			<div id="wp-<?=$this->id()?>-editor-container" class="wp-editor-container">
				<div id="qt_<?=$this->id()?>_toolbar" class="quicktags-toolbar"></div>
				<textarea class="wp-editor-area" rows="10" autocomplete="off" cols="40" name="<?=$this->name()?>" id="<?=$this->id()?>">%s</textarea>
			</div>
			<?php
			return ob_get_clean();
		}

	}