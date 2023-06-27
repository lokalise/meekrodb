#!/usr/bin/php
<?php
class SimpleTest {
  public function assert($boolean) {
    if (! $boolean) $this->fail();
  }

  protected function fail($msg = '') {
    echo "FAILURE! $msg\n";
    debug_print_backtrace();
    die;
  }
}

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

ini_set('date.timezone', 'America/Los_Angeles');

error_reporting(E_ALL | E_STRICT);
require_once __DIR__ . '/bootstrap.php';
DB::$user = getenv('DB_USER');
DB::$password = getenv('DB_PASSWORD');
DB::$dbName = getenv('DB_NAME');
DB::$host = getenv('DB_HOST');
TestKernel::boot();

// WARNING: ALL tables in the database will be dropped before the tests, including non-test related tables.
require_once __DIR__ . '/BasicTest.php';
require_once __DIR__ . '/WalkTest.php';
require_once __DIR__ . '/CallTest.php';
require_once __DIR__ . '/ObjectTest.php';
require_once __DIR__ . '/WhereClauseTest.php';
require_once __DIR__ . '/HookTest.php';
require_once __DIR__ . '/TransactionTest.php';

$classes_to_test = array(
  'BasicTest',
  'WalkTest',
  'CallTest',
  'WhereClauseTest',
  'ObjectTest',
  'HookTest',
  'TransactionTest',
);

$mysql_version = DB::serverVersion();
if ($mysql_version >= '5.5') {
  require_once __DIR__ . '/TransactionTest_55.php';
  $classes_to_test[] = 'TransactionTest_55';
} else {
  echo "MySQL 5.5 not available (version is $mysql_version) -- skipping MySQL 5.5 tests\n";
}
$time_start = microtime_float();
foreach ($classes_to_test as $class) {
  $object = new $class();
  
  foreach (get_class_methods($object) as $method) {
    if (substr($method, 0, 4) != 'test') continue;
    echo "Running $class::$method..\n";
    $object->$method();
  }
}
$time_end = microtime_float();
$time = round($time_end - $time_start, 2);

echo "Completed in $time seconds\n";


?>
