<?php


	/**
	 * Created by PhpStorm.
	 * User: denmedia
	 * Date: 15.04.2017
	 * Time: 8:30
	 */
	trait hw_add_fields{

		/**
		 * @param        $fieldId
		 * @param string $type
		 * @param null   $fieldName
		 * @param null   $position
		 * @return hw_field
		 */
		public function add_field( $fieldId, $type = 'text', $fieldName = null, $position = null ){
			$field = hiweb()->fields()->make( $fieldId, $type, $fieldName );
			///Join to context
			switch( true ){
				case $this instanceof hw_post_type:
					return $field->location()->post_type( $this->type(), $position )->get_field();
					break;
				case $this instanceof hw_admin_menu_abstract:
					return $field->location()->admin_menu( $this->menu_slug() )->get_field();
					break;
				case $this instanceof hw_taxonomy:
					return $field->location()->taxonomy( $this->name() )->get_field();
					break;
				case $this instanceof hw_users:
					return $field->location()->users();
					break;
				default:
					hiweb()->console()->warn( 'Не известный объект для локации поля [' . get_class( $this ) . ']', true );
					return hiweb()->fields()->make('');
					break;
			}
		}

	}