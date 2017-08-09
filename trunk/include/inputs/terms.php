<?php

	hiweb()->inputs()->register_type( 'terms', 'hw_input_terms' );

	class hw_input_terms extends hw_input{


		protected $attributes = [
			'taxonomy' => 'category',
			'hide_empty' => false
		];


		public function _init(){
			parent::_init(); // TODO: Change the autogenerated stub
			$this->tag_add( 'multiple', 'multiple' );
			$this->tag_add( 'placeholder', 'Выберите категорию' );
			$this->tag_add( 'no_results_text', 'Ничего не найдено' );
		}


		/**
		 * @return array
		 */
		private function get_terms_by_taxonomy(){
			$terms_by_taxonomy = [];
			$taxonomies = $this->attributes( 'taxonomy' );
			if( is_array( $taxonomies ) ){
				foreach( $taxonomies as $taxonomy ){
					if( !taxonomy_exists( $taxonomy ) ) continue;
					$attributes = $this->attributes();
					$attributes['taxonomy'] = $taxonomy;
					$terms = get_terms( $attributes );
					if( is_array( $terms ) ) $terms_by_taxonomy[ $taxonomy ] = $terms;
				}
			}
			return $terms_by_taxonomy;
		}


		public function html(){
			hiweb()->css( hiweb()->dir_css . '/input-terms.css' );
			hiweb()->js( hiweb()->dir_js . '/input-terms.js', [ 'jquery' ] );
			hiweb()->css( hiweb()->dir_vendors . '/chosen/chosen.min.css' );
			hiweb()->js( hiweb()->dir_vendors . '/chosen/chosen.jquery.min.js', [ 'jquery' ] );
			ob_start();
			$terms_by_taxonomy = $this->get_terms_by_taxonomy();
			?>
			<div class="hw-input-terms">
				<select class="hw-input-terms-select" name="<?= $this->name() ?>[]" <?= !is_null( $this->tag_get( 'multiple' ) ) ? 'multiple="true"' : '' ?> data-placeholder="<?= $this->tag_get( 'placeholder' ) ?>" data-no_results_text="<?= $this->tag_get( 'no_results_text' ) ?>">
					<?php

						foreach( $terms_by_taxonomy as $taxonomy_name => $terms ){
							if( !is_array( $terms ) ) continue;
							$taxonomy = get_taxonomy( $taxonomy_name );
							if( $taxonomy instanceof WP_Taxonomy ){
								?>
								<optgroup label="<?= $taxonomy->label ?>">
									<?php
										/** @var WP_Term $wp_term */
										foreach( $terms as $wp_term ){
											$selected = is_array($this->value()) ? in_array($wp_term->term_id, $this->value()) : ($wp_term->term_id == $this->value());
											?>
											<option <?=$selected?'selected':''?> value="<?= $wp_term->term_taxonomy_id ?>"><?= $wp_term->name ?></option>
											<?php
										}
									?>
								</optgroup>
								<?php
							}
						}
					?>
				</select>
			</div>
			<?php
			return ob_get_clean();
		}

	}