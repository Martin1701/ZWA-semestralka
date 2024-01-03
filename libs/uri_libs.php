<?php

function parseUri($params) : array {
	$to_return = array(0 => "index", 1 => "basic");
		
	@list($controller, $action, $pars) = explode("/", $params, 3);
	
	if (!empty($controller)) {
		$to_return[0] = $controller;
	}
	if (!empty($action)) {
		$to_return[1] = $action;
	}
	if (!empty($pars)) {
		$to_return = array_merge($to_return ,explode("/", $pars));
	}
	return $to_return;
 }