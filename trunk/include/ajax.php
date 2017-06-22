<?php

	///TOOLS THUMBNAIL UPLOAD
	add_action( 'wp_ajax_hw_thumbnail_post_upload', function(){
		$post_id = intval( $_SERVER['HTTP_POSTID'] );
		$file = $_FILES['file'];
		if( $post_id > 0 ){
			///Upload File
			$attachment_id = hiweb()->tools()->thumbnail_upload()->upload( $file );
			if( $attachment_id <= 0 ){
				wp_die( json_encode( [ false, 'не удалось загрузит  файл' ] ) );
			} else {
				if( $_SERVER['HTTP_POSTTYPE'] == 'taxonomy' ){
					$R = update_term_meta( $post_id, 'thumbnail_id', $attachment_id );
				} else {
					$R = set_post_thumbnail( $post_id, $attachment_id );
				}
				if( $R == false ){
					wp_die( json_encode( [ false, 'не удалось установить миниатюру для товара' ] ) );
				} else {
					$img_src = wp_get_attachment_image_url( $attachment_id, hiweb()->tools()->thumbnail_upload()->default_preview_size );
					wp_die( json_encode( [ true, $img_src ] ) );
				}
			}
		}
		wp_die( json_encode( [ false, 'Не верный ID товара' ] ) );
	} );
	add_action( 'wp_ajax_hw_thumbnail_post_remove', function(){
		$post_id = intval( $_POST['post_id'] );
		if( $post_id > 0 ){
			$R = delete_post_thumbnail( $post_id );
			if( $R ) wp_die( json_encode( [ true, 'Миниатюра удалена' ] ) ); else wp_die( json_encode( [ false, 'Не удалось удалить миниатюру' ] ) );
		} else {
			wp_die( json_encode( [ false, 'Не верный ID товара' ] ) );
		}
	} );

	add_action( 'wp_ajax_hw_thumbnail_post_set', function(){
		$post_id = intval( $_POST['post_id'] );
		$attachment_id = intval( $_POST['thumbnail_id'] );
		if( $attachment_id == 0 ){
			wp_die( json_encode( [ false, 'Не указан индификатор миниатюры post:[thumbnail_id]' ] ) );
		} elseif( $post_id > 0 ) {
			if( $_POST['type'] == 'taxonomy' ){
				$R = update_term_meta( $post_id, 'thumbnail_id', $attachment_id );
			} else {
				$R = set_post_thumbnail( $post_id, $attachment_id );
			}
			if( $R == false ){
				wp_die( json_encode( [ false, 'не удалось установить миниатюру для товара' ] ) );
			} else {
				$img_src = wp_get_attachment_image_url( $attachment_id, hiweb()->tools()->thumbnail_upload()->default_preview_size );
				wp_die( json_encode( [ true, $img_src ] ) );
			}
		}
		wp_die( json_encode( [ false, 'Не верный ID товара' ] ) );
	} );

	///FIELDS
	add_action( 'wp_ajax_hw_get_field', function(){
		$field_id = hiweb()->path()->request( 'id' );
		$method = hiweb()->path()->request( 'method', 'html' );
		$params = hiweb()->path()->request( 'params' );
		$value = hiweb()->path()->request( 'value' );
		$R = [ 'result' => false ];
		///
		if( !is_string( $field_id ) || trim( $field_id ) == '' ){
			$R['error'] = 'Не передан параметр id поля. Необходимо указать $_POST[id] или $_GET[id].';
		} else {
			if( !hiweb()->fields()->home()->is_exists( $field_id ) ){
				$R['error'] = 'Поле с id[' . $field_id . '] не найдено!';
			} else {
				$field = hiweb()->fields()->home()->get( $field_id );
				if( !is_null( $value ) ) $field->value( $value );
				if( method_exists( $field, $method ) ){
					$R['result'] = true;
					$R['data'] = call_user_func( [ $field, $method ], $params );
				} elseif( method_exists( $field->input(), $method ) ) {
					$R['result'] = true;
					$R['data'] = call_user_func( [ $field->input(), $method ], $params );
				} else {
					$R['error'] = 'В поле id[' . $field_id . '] метод [' . $method . '] не найден!';
				}
			}
		}
		if( !$R['result'] && !isset( $R['error'] ) ) $R['error'] = 'Неизвестная ошибка';
		echo json_encode( $R, JSON_UNESCAPED_UNICODE );
		die;
	} );


	///INPUTS
	add_action('wp_ajax_hw_get_input', function(){
		$input_global_id = hiweb()->path()->request('id');
		$method = hiweb()->path()->request( 'method', 'html' );
		$params = hiweb()->path()->request( 'params' );
		$value = hiweb()->path()->request( 'value' );
		$R = [ 'result' => false ];
		///
		if( !is_string( $input_global_id ) || trim( $input_global_id ) == '' ){
			$R['error'] = 'Не передан параметр id инпута. Необходимо указать $_POST[id] или $_GET[id].';
		} else {
			if( !isset(hiweb()->inputs()->inputs[$input_global_id]) || !hiweb()->inputs()->inputs[$input_global_id] instanceof hw_input){
				$R['error'] = 'Инпут с id[' . $input_global_id . '] не найден!';
			} else {
				$input = hiweb()->inputs()->inputs[$input_global_id];
				if( !is_null( $value ) ) $input->value( $value );
				if( method_exists( $input, $method ) ){
					$R['result'] = true;
					$R['data'] = call_user_func( [ $input, $method ], $params );
				} else {
					$R['error'] = 'В поле id[' . $input_global_id . '] метод [' . $method . '] не найден!';
				}
			}
		}
		if( !$R['result'] && !isset( $R['error'] ) ) $R['error'] = 'Неизвестная ошибка';
		echo json_encode( $R, JSON_UNESCAPED_UNICODE );
		die;
	});