<?php
// check current php version to ensure it meets 2300's requirements
function check_php_version()
{
  if (version_compare(phpversion(), '7.0', '<')) {
    define('VERSION_MESSAGE', "PHP version 7.0 or higher is required for 2300. Make sure you have installed PHP 7 on your computer and have set the correct PHP path in VS Code.");
    echo VERSION_MESSAGE;
    throw VERSION_MESSAGE;
  }
}
check_php_version();

function config_php_errors()
{
  ini_set('display_startup_errors', 1);
  ini_set('display_errors', 0);
  error_reporting(E_ALL);
}
config_php_errors();

// Open a connection to an SQLite database stored in filename: $db_filename.
// If database does not exists, will execute .sql file from $init_sql_filename
// to create and initialize the database. No database is created if there is
// an error the initialization SQL.
// Returns: Connection to database.
// Example: $db = open_or_init_sqlite_db('secure/gallery.sqlite', 'secure/init.sql');
function open_or_init_sqlite_db($db_filename, $init_sql_filename)
{
  // If the init SQL script does not exist, quit!
  if (!file_exists($init_sql_filename)) {
    throw new Exception("No such file: " . $init_sql_filename);
  }

  // create checksum of initialization script.
  $init_sql = file_get_contents($init_sql_filename);
  $init_checksum = md5($init_sql);

  // checksum used to create the database
  $init_checksum_filename = $init_sql_filename . ".checksum";

  // If the database doesn't exist, then the existing checksum is invalid. Delete it.
  if (!file_exists($db_filename)) {
    unlink($init_checksum_filename);
  }

  // If the database exists, but no checksum file exists, then we have a consistency problem with the DB.
  if (file_exists($db_filename) && !file_exists($init_checksum_filename)) {
    throw new Exception("No checksum for existing database. Please regenerate your database (delete .sqlite file).");
  }

  // Get the existing checksum and compare it the init checksum.
  if (file_exists($init_checksum_filename)) {
    $current_checksum = file_get_contents($init_checksum_filename);

    if ($init_checksum != $current_checksum) {
      throw new Exception("Database initialization script has changed. Please regenerate your database (delete .sqlite file).");
    }
  }

  // If the database does not exist, create it!
  if (!file_exists($db_filename)) {
    // Create new database
    $db = new PDO('sqlite:' . $db_filename);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
      // initialize database using .sql script
      $result = $db->exec($init_sql);
      if ($result) {
        file_put_contents($init_checksum_filename, $init_checksum);
        return $db;
      }
    } catch (PDOException $exception) {
      // If we had an error, then the DB did not initialize properly,
      // so let's delete it!
      unlink($db_filename);
      throw $exception;
    }
  } else {
    // database was already initialized. Just open it!
    $db = new PDO('sqlite:' . $db_filename);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
  }

  return null;
}

// Execute a query ($sql) against a datbase ($db).
// Returns query results if query was successful.
// Returns null if query was not successful.
function exec_sql_query($db, $sql, $params = array())
{
  $query = $db->prepare($sql);
  if ($query and $query->execute($params)) {
    return $query;
  }
  return null;
}

$db = open_or_init_sqlite_db('secure/gallery.sqlite', 'secure/init.sql');
