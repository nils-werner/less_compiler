<?php

	Class extension_LESS_Compiler extends Extension{

		public function getSubscribedDelegates(){
			return array();
		}

		public function install(){
			General::realiseDirectory(CACHE . '/less_compiler/', Symphony::Configuration()->get('write_mode', 'directory'));
			
			$htaccess = @file_get_contents(DOCROOT . '/.htaccess');

			if($htaccess === false) return false;

			## Cannot use $1 in a preg_replace replacement string, so using a token instead
			$token = md5(time());

			$rule = "
	### LESS RULES
	RewriteRule ^less\/(.+\.less)\$ extensions/less_compiler/lib/less.php?param={$token} [L,NC]\n\n";

			## Remove existing the rules
			$htaccess = self::__removeLessRules($htaccess);

			if(preg_match('/### LESS RULES/', $htaccess)){
				$htaccess = preg_replace('/### LESS RULES/', $rule, $htaccess);
			}
			else{
				$htaccess = preg_replace('/RewriteRule .\* - \[S=14\]\s*/i', "RewriteRule .* - [S=14]\n{$rule}\t", $htaccess);
			}

			## Replace the token with the real value
			$htaccess = str_replace($token, '$1', $htaccess);

			return @file_put_contents(DOCROOT . '/.htaccess', $htaccess);
		}

		public function uninstall(){
			$htaccess = @file_get_contents(DOCROOT . '/.htaccess');

			if($htaccess === false) return false;

			$htaccess = self::__removeLessRules($htaccess);
			$htaccess = preg_replace('/### LESS RULES/', NULL, $htaccess);

			return @file_put_contents(DOCROOT . '/.htaccess', $htaccess);
		}

		public function enable(){
			return $this->install();
		}

		public function disable(){
			$htaccess = @file_get_contents(DOCROOT . '/.htaccess');

			if($htaccess === false) return false;

			$htaccess = self::__removeLessRules($htaccess);
			$htaccess = preg_replace('/### LESS RULES/', NULL, $htaccess);

			return @file_put_contents(DOCROOT . '/.htaccess', $htaccess);
		}

	/*-------------------------------------------------------------------------
		Utilities:
	-------------------------------------------------------------------------*/

		private static function __removeLessRules($string){
			return preg_replace('/RewriteRule \^less[^\r\n]+[\r\n]?/i', NULL, $string);
		}

	}
