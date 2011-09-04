<?php

	@ini_set('display_errors', 'off');

	define('DOCROOT', rtrim(realpath(dirname(__FILE__) . '/../../../'), '/'));
	define('DOMAIN', rtrim(rtrim($_SERVER['HTTP_HOST'], '/') . str_replace('/extensions/less_compiler/lib', NULL, dirname($_SERVER['PHP_SELF'])), '/'));

	// Include some parts of the engine
	require_once(DOCROOT . '/symphony/lib/boot/bundle.php');
	require_once('dist/lessc.inc.php');
	
	function processParams($string){
		$param = (object)array(
			'file' => 0
		);

		if(preg_match_all('/^(.+)$/i', $string, $matches, PREG_SET_ORDER)){
			$param->file = $matches[0][1];
		}
		
		return $param;
	}
	
	$param = processParams($_GET['param']);
	
	header('Content-type: text/css');
	
	$lc = new lessc(WORKSPACE . '/' . $param->file);
	echo $lc->parse();
		
	exit;
