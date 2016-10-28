<?php
	/**
	 * Created by PhpStorm.
	 * User: hiweb
	 * Date: 30.06.2016
	 * Time: 15:44
	 */
	
	function hw_path_base_url(){
		//if(hiweb()->cacheExists()) return hiweb()->cache();
		$root = ltrim( rtrim( current( explode( "wp-", getcwd() ) ), '\\/' ), '/' );
		$query = ltrim( str_replace( '\\', '/', dirname( $_SERVER['PHP_SELF'] ) ), '/' );
		$rootArr = array();
		$queryArr = array();
		foreach( array_reverse( explode( '/', $root ) ) as $dir ){
			$rootArr[] = rtrim( $dir . '/' . end( $rootArr ), '/' );
		}
		foreach( explode( '/', $query ) as $dir ){
			$queryArr[] = ltrim( end( $queryArr ) . '/' . $dir, '/' );
		}
		$rootArr = array_reverse( $rootArr );
		$queryArr = array_reverse( $queryArr );
		$r = '';
		foreach( $queryArr as $dir ){
			foreach( $rootArr as $rootDir ){
				if( $dir == $rootDir ){
					$r = $dir;
					break 2;
				}
			}
		}
		$https = ( !empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' ) || $_SERVER['SERVER_PORT'] == 443;
		$R = rtrim( 'http' . ( $https ? 's' : '' ) . '://' . $_SERVER['HTTP_HOST'] . '/' . $r, '/' );
		return $R;
	}

	if( !defined( 'HIWEB_DIR_ROOT' ) )
		define( 'HIWEB_DIR_ROOT', rtrim( current( explode( "wp-", getcwd() ) ), '\\/' ) );
	if( !defined( 'HIWEB_URL_ROOT' ) )
		define( 'HIWEB_URL_ROOT', hw_path_base_url() );
	if( !defined( 'HIWEB_DIR_BASE' ) )
		define( 'HIWEB_DIR_BASE', dirname( __FILE__ ) );
	if( !defined( 'HIWEB_URL_BASE' ) )
		define( 'HIWEB_URL_BASE', str_replace( HIWEB_DIR_ROOT, HIWEB_URL_ROOT, HIWEB_DIR_BASE ) );
	if( !defined( 'HIWEB_URL_CSS' ) )
		define( 'HIWEB_URL_CSS', HIWEB_URL_BASE . '/css' );
	if( !defined( 'HIWEB_URL_JS' ) )
		define( 'HIWEB_URL_JS', HIWEB_URL_BASE . '/js' );
	if( !defined( 'HIWEB_DIR_INCLUDE' ) )
		define( 'HIWEB_DIR_INCLUDE', HIWEB_DIR_BASE . '/include' );
	if( !defined( 'HIWEB_DIR_MODULES' ) )
		define( 'HIWEB_DIR_MODULES', HIWEB_DIR_INCLUDE . '/modules' );