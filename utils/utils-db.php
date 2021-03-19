<?php

if (!defined('DBHOST'))
	define('DBHOST', 'localhost');
if (!defined('DBUSER'))
	define('DBUSER', 'id13454401_admin');
if (!defined('DBPASS'))
	define('DBPASS', 'hTwobN9Jq_kdB/D[');
if (!defined('DBNAME'))
	define('DBNAME', 'id13454401_okdb');

function getDB()
{
	$connection = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
	if (mysqli_connect_error()) {
		die("Could not connect to database: " . mysqli_connect_error());
	}
	return $connection;
}

function runQuery($db, $query)
{
	$result = mysqli_query($db, $query);
	if (mysqli_connect_error()) {
		die("Could not run query: " . mysqli_connect_error());
	}

	$resultArr = array();
	while ($row = mysqli_fetch_assoc($result)) {
		array_push($resultArr, $row);
	}

	mysqli_free_result($result);
	return $resultArr;
}

function execQueryStmt($statement)
{
	// Set the mode to show exceptions.
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

	mysqli_stmt_execute($statement);
	$result = mysqli_stmt_get_result($statement);
	mysqli_stmt_close($statement);
	return $result;
}


function execInsertStmt($statement)
{
	// Set the mode to show exceptions.
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

	$stmtSuccess = mysqli_stmt_execute($statement);
	if (mysqli_connect_error()) {
		die("Could not run query: " . mysqli_connect_error());
	}
	if (!$stmtSuccess) {
		return false;
	}

	$result = mysqli_stmt_affected_rows($statement);
	return $result;
}
