<?php


	class hw_cron{

		private function string2array( $jobs = '' ){
			$array = explode( "\r\n", trim( $jobs ) ); // trim() gets rid of the last \r\n
			foreach( $array as $key => $item ){
				if( $item == '' ){
					unset( $array[ $key ] );
				}
			}
			return $array;
		}


		private function array2string( $jobs = [] ){
			$string = implode( "\r\n", $jobs );
			return $string;
		}


		public function get_jobs(){
			$output = shell_exec( 'crontab -l' );
			return self::string2array( $output );
		}


		public function save_jobs( $jobs = [] ){
			$output = shell_exec( 'echo "' . self::array2string( $jobs ) . '" | crontab -' );
			return $output;
		}


		public function job_exists( $job = '' ){
			$jobs = self::get_jobs();
			if( in_array( $job, $jobs ) ){
				return true;
			} else {
				return false;
			}
		}


		public function add_job( $job = '' ){
			if( self::job_exists( $job ) ){
				return false;
			} else {
				$jobs = self::get_jobs();
				$jobs[] = $job;
				return self::save_jobs( $jobs );
			}
		}


		public function remove_job( $job = '' ){
			if( hiweb()->string()->isRegex( $job ) ){
				$jobs = self::get_jobs();
				foreach( $jobs as $j ){
					if( preg_match( $job, $j ) > 0 ) unset( $jobs[ array_search( $job, $jobs ) ] );
				}
				return self::save_jobs( $jobs );
			} else {
				if( self::job_exists( $job ) ){
					$jobs = self::get_jobs();
					unset( $jobs[ array_search( $job, $jobs ) ] );
					return self::save_jobs( $jobs );
				} else {
					return false;
				}
			}
		}


		public function clear_jobs(){
			exec( 'crontab -r', $crontab );
		}
	}