<?php

/**
 * @throws Exception
 */
function db()
{
	static $pdo;

	if (!$pdo) {
		$path = ROOT_PATH . '/database/database.sqlite';
		if (!file_exists($path)) {
			throw new Exception("Database not found at: $path");
		}

		$pdo = new PDO("sqlite:$path");
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	return $pdo;
}
