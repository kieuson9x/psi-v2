<?php

$host = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "psi";
$dsn = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8';

if (!defined('URLROOT') || !defined('APPROOT')) {
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

// db connection
try {
  $connect = new PDO($dsn, $username, $password);
  $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  $errorMessage = $e->getMessage();
  echo $errorMessage;
  exit();
}

class Database
{
  private $dbHost = '127.0.0.1';
  private $dbUser = 'root';
  private $dbPass = '';
  private $dbName = 'psi';
  private $charset = 'utf8';
  private $statement;
  private $dbHandler;
  private $error;

  public function __construct()
  {
    $conn = 'mysql:host=' . $this->dbHost . ';dbname=' . $this->dbName . ';charset=' . $this->charset;
    $options = array(
      PDO::ATTR_PERSISTENT => true,
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    );
    try {
      $this->dbHandler = new PDO($conn, $this->dbUser, $this->dbPass, $options);
    } catch (PDOException $e) {
      $this->error = $e->getMessage();
      echo $this->error;
    }
  }

  //Allows us to write queries
  public function query($sql)
  {
    $this->statement = $this->dbHandler->prepare($sql);
  }

  //Bind values
  public function bind($parameter, $value, $type = null)
  {
    switch (is_null($type)) {
      case is_int($value):
        $type = PDO::PARAM_INT;
        break;
      case is_bool($value):
        $type = PDO::PARAM_BOOL;
        break;
      case is_null($value):
        $type = PDO::PARAM_NULL;
        break;
      default:
        $type = PDO::PARAM_STR;
    }
    $this->statement->bindValue($parameter, $value, $type);
  }

  //Execute the prepared statement
  public function execute()
  {
    return $this->statement->execute();
  }

  //Return an array
  public function resultSet()
  {
    $this->execute();
    return $this->statement->fetchAll(PDO::FETCH_OBJ);
  }

  //Return a specific row as an object
  public function single()
  {
    $this->execute();
    return $this->statement->fetch(PDO::FETCH_OBJ);
  }

  //Get's the row count
  public function rowCount()
  {
    return $this->statement->rowCount();
  }
}

$db = new Database();
