<?php

	hiweb()->inputs()->register_type( 'repeat', 'hw_input_repeat' );


	class hw_input_repeat extends hw_input{


		public function _init(){
			parent::_init();
			$this->dimension = 2;
		}


		public function get_value(){
			if( !is_array( $this->value ) ){
				return array();
			} else {
				return array_slice( $this->value, 1 );
			}
		}


		private function the_head_html( $thead = true ){
			force_ssl_admin()
			?>
			<?= $thead ? '<thead>' : '<tfoot>' ?>
			<tr>
				<th></th>
				<?php if( $this->have_cols() ){
					$width_full = 0;
					foreach( $this->get_cols() as $col ){
						$width_full += $col->width();
					}
					foreach( $this->get_cols() as $col ){
						$width = ( $col->width() / $width_full * 100 ) . '%';
						?>
						<th data-col="<?= $col->id() ?>" style="width:<?= $width ?>">
							<?= $col->name() . ( $col->description() != '' ? '<p class="description">'.$col->description().'</p>' : '' ) ?>
						</th>
						<?php
					}
				} ?>
				<th class="nowrap" data-ctrl>
					<button class="dashicons dashicons-plus-alt" data-action-add="<?= $thead ?>" title="<?= $thead ? 'Add row to top' : 'Add row to bottom' ?>"></button>
					<button title="Clear all table rows..." class="dashicons dashicons-marker" data-action-clear=""></button>
				</th>
			</tr>
			<?= $thead ? '</thead>' : '</tfoot>' ?>
			<?php
		}


		private function the_row_html( $row_id = null, $row = [] ){
			?>
			<tr data-row="<?= $row_id ?>">
				<td data-drag>
					<i class="dashicons dashicons-sort"></i>
				</td>
				<?php if( $this->have_cols() ){
					foreach( $this->get_cols() as $col ){
						?>
						<td data-col="<?= $col->id() ?>">
							<?php $col->input_by_row( $row_id, $row )->the() ?>
						</td>
						<?php
					}
				} ?>
				<td data-ctrl>
					<!--<button title="Duplicate row" class="dashicons dashicons-admin-page" data-action-duplicate="<?= $row_id ?>"></button>-->
					<button title="Remove row..." class="dashicons dashicons-trash" data-action-remove="<?= $row_id ?>"></button>
				</td>
			</tr>
			<?php
		}

		public function ajax_html_row($input_name){
			ob_start();
			$this->name( $input_name );
			$this->the_row_html(0);
			return ob_get_clean();
		}


		public function html( $arguments = null ){
			if( !hiweb()->context()->is_backend_page() ){
				hiweb()->console()->error( __( 'Can not display INPUT [IMAGE], it works only in the back-End' ) );
				return '';
			}
			hiweb()->css( hiweb()->url_css . '/input_repeat.css' );
			hiweb()->js( hiweb()->url_js . '/input_repeat.js', array( 'jquery-ui-sortable' ) );
			///
			ob_start();
			$this->tag_add('data-input-name', $this->name());
			?>
			<div class="hw-input-repeat" <?= $this->tags_html() ?>>
				<?php if( !$this->have_cols() ){
					?><p class="empty-message"><?= sprintf( __( 'For repeat input [%s] not add col fields. For that do this: <code>$field->add_col(\'col-id\')</code>' ), $this->id() ) ?></p><?php
				} else {
					?>
					<table class="widefat striped"><?php
					$this->the_head_html( true );
					?>
					<tbody data-rows-source><?php $this->the_row_html( 0 ) ?></tbody>
					<tbody data-rows-list>
					<?php
						if( $this->have_value_rows() ){
							foreach( $this->value as $row_id => $row ){
								if( intval( $row_id ) != 0 ){
									$this->the_row_html( $row_id, $row );
								}
							}
						}
					?>
					</tbody>
					<tbody data-rows-message>
					<tr data-row-empty="<?= $this->have_rows() ? '1' : '0' ?>">
						<td colspan="<?= ( count( $this->get_cols() ) + 2 ) ?>"><p class="message"><?= 'Таблица пуста. Для добавления хотя бы одног поля, кликните по кнопке "+"' ?></p></td>
					</tr>
					</tbody>

					<?php
					$this->the_head_html( false );
					?></table><?php
				} ?>
			</div>
			<?php
			return ob_get_clean();
		}
	}