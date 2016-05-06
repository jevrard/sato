#!/usr/bin/php
<?php
require_once 'src/Encoder.php';

echo "-- Project SATO using glucose 4.0 based on MiniSAT --\n";
echo "-- Mariam Bouzid and Justine Evrard --\n\n";

unset($argv[0]);

$verbose = false;
$dimacsFilePath = 'sat.dimacs';
$outputFilePath = 'output.txt';
$comment = "";

/* Parse options */
if(in_array("-h", $argv) !== false || in_array("--help", $argv) !== false) {
  printDoc();
  exit;
}

if(in_array("-v", $argv) !== false) {
  $verbose = true;
}

if(($key = array_search("-f", $argv)) !== false) {
  unset($argv[$key]);
  if(!isset($argv[$key+1]) || !preg_match("/\.dimacs$/", $argv[$key+1])) die("Invalid argument ! Print help -h.\n");
  $dimacsFilePath = $argv[$key+1];
  unset($argv[$key+1]);
}

if(($key = array_search("-o", $argv)) !== false) {
  unset($argv[$key]);
  if(!isset($argv[$key+1])) die("Invalid argument ! Print help -h.\n");
  $outputFilePath = $argv[$key+1];
  unset($argv[$key+1]);
}

if(($key = array_search("-c", $argv)) !== false) {
  unset($argv[$key]);
  if(!isset($argv[$key+1])) die("Invalid argument ! Print help -h.\n");
  $comment = $argv[$key+1];
  unset($argv[$key+1]);
}

/* Create the SAT problem from the CSP */
$argv = " ".implode("  ", $argv)." ";
try {
  $csp = CSP::parseExpression($argv);
  echo $csp;
  $encoder = new Encoder($csp);
  $sat = $encoder->encode();
  echo $sat;
  $sat->exportToDimacs($dimacsFilePath, $comment);
} catch (Exception $e) {
  die($e->getMessage());
}
echo "\n";

/* Use of the SAT solver */
$command = escapeshellcmd("./glucose-syrup/simp/glucose_static $dimacsFilePath $outputFilePath");
$output = shell_exec($command);
if($verbose) echo $output."\n";

try {
  $encoder->interprete($outputFilePath);
} catch (Exception $e) {
  die($e->getMessage());
}

/* functions */
function printDoc() {
  echo <<<DOC

USAGE EXAMPLES:
  ./sato.php x 0 2 y 0 2 ['x-y<=-1' '-x+y<=-1'] ['x+y<=7']
  ./sato.php x 0 2 y 0 2 ['x-y<=-1' '-x+y<=-1']
  ./sato.php x 2 6 y 2 6 ['x+y<=7']

AVAILABLE OPTIONS:
  -h or --help : display this documentation
  -v : verbose - display solver SAT output
  -f <file_name.dimacs> : generated dimacs file's name (default 'sat.dimacs')
  -o <output_file_name> : generated output file's name (default 'output.txt')
  -c <comment> : generated dimacs file's comment (default '')


DOC;
}
