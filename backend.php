<?php

error_reporting(-1);
ini_set('display_errors', 'On');
date_default_timezone_set('America/Chicago');

// Database
define('DB_PATH', getcwd() . '/data/prt.sqlite');
$db = new PDO('sqlite:' . DB_PATH);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create table, if necessary
$db->exec(
  'CREATE TABLE IF NOT EXISTS prt (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    version TEXT,
    participant_id TEXT,
    session_id TEXT,
    timestamp TEXT,
    csv BLOB
  )'
);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Decode POST data
  $data = json_decode(file_get_contents('php://input'), true);

  if (is_array($data)) {
    try {
      $insert =
        'INSERT INTO prt (version, participant_id, session_id, timestamp, csv)
        VALUES (:version, :participant_id, :session_id, :timestamp, :csv)';
      $stmt = $db->prepare($insert);

      $stmt->execute(array(
        ':version' => isset($data['version']) ? $data['version'] : 'none',
        ':participant_id' => isset($data['participant_id']) ? $data['participant_id'] : 'NA',
        ':session_id' => isset($data['session_id']) ? $data['session_id'] : 'NA',
        ':timestamp' => date('c'),
        ':csv' => isset($data['csv']) ? $data['csv'] : '',
      ));
      echo "{}\n";
      http_response_code(200);
    } catch (exception $e) {
      http_response_code(500);
      echo "{error:'" . $e->getMessage();
      echo ", trace: ";
      echo $e->getTraceAsString();
      echo "}";
    }

  } else {
    http_response_code(400);
  }

} else {
  http_response_code(405);
}

?>
