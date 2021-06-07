<?php

session_start();

if (!defined('URLROOT') || !defined(APPROOT)) {
	define('URLROOT', 'http://stock-app.test');
	define('APPROOT', dirname(dirname(__FILE__)));
}

if (!function_exists('data_get')) {
	function data_get($data, $path, $default = null)
	{
		$paths = explode('.', $path);

		return array_reduce($paths, function ($o, $p) use ($default) {
			if (isset($o->$p)) return (is_object($o->$p) ? (array) $o->$p : $o->$p) ?? $default;
			if (isset($o[$p])) return (is_object($o[$p]) ? (array) $o[$p] : $o[$p])  ?? $default;

			return $default;
		}, (array) $data);
	}
}

require_once 'db_connect.php';

// echo $_SESSION['userId'];

if (!$_SESSION['user_id']) {
	header('location:' . URLROOT . '/index.php');
}
