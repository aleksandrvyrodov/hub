<?php

function bcrypt($password)
{
	$rounds = 12;
	$salt = sprintf('$2a$%02d$', $rounds) . substr(str_replace('+', '.', base64_encode(pack('N4', mt_rand(), mt_rand(), mt_rand(), mt_rand()))), 0, 22);
	return crypt($password, $salt);
}

global $password;

$run = fn() => bcrypt($password);