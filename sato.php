#!/usr/bin/php
<?php
require_once 'src/Encoder.php';
require_once 'src/Solver.php';

echo "\n-- Project SATO using glucose 4.0 based on MiniSAT --\n";
echo "-- Mariam Bouzid & Justine Evrard --\n\n";

unset($argv[0]);

$verbose = false;
$interprete = false;

/* Parse options */
if(in_array("-h", $argv) !== false || in_array("--help", $argv) !== false) {
  printDoc();
  exit;
}

if(in_array("-v", $argv) !== false) {
  $verbose = true;
}

if(($key = array_search("-i", $argv)) !== false) {
  unset($argv[$key]);
  $interprete = true;
}

if(($key = array_search("-f", $argv)) !== false) {
  unset($argv[$key]);
  if(!isset($argv[$key+1]) || !preg_match("/\.dimacs$/", $argv[$key+1])) die("Invalid argument for -f ! Print help -h.\n");
  $dimacsFilePath = $argv[$key+1];
  unset($argv[$key+1]);
  $comment = "";
  if(isset($argv[$key+2])) {
    $comment = $argv[$key+2];
    unset($argv[$key+2]);
  }
}

/* Create and solve the SAT problem from the CSP */
$argv = " ".implode("  ", $argv)." ";
try {
  $csp = CSP::parseExpression($argv);
  echo $csp;

  $encoder = new Encoder($csp);
  $sat = $encoder->encode();
  echo $sat;

  $solver = new Solver($sat);
  if (isset($dimacsFilePath)) {
    $solver->prepare($dimacsFilePath, $comment);
    die("--> $dimacsFilePath file is created !\n\n");
  }
  $solver->solve($verbose, $interprete);
} catch (Exception $e) {
  die($e->getMessage()."\n");
}
echo "\n";

/* functions */
function printDoc() {
  echo <<<DOC

USAGE EXAMPLES:
  ~ SATISFIABLE ~
  ./sato.php x 0 2 y 0 2 ['x-y<=-1' '-x+y<=-1']
  ./sato.php x 1 3 y -1 0 ['x+y<1'] ['-x+y>=-3']
  ./sato.php x 2 6 y 2 6 ['x+y<=7']
  ./sato.php x 0 1 y 0 1 ['x!=y' 'x=y']
  ~ UNSATISFIABLE ~
  ./sato.php x 1 3 y -1 0 ['x+y<1'] ['-(-x+y>=-3)']
  ./sato.php x 1 3 y 0 1 ['x+y<1'] ['-x+y>=-3']
  ./sato.php x 0 2 y 0 2 ['x!=y'] ['x=y']

AVAILABLE OPTIONS:
  -h or --help : display this documentation
  -v : verbose - display solver SAT output
  -i : interprete and display the solver's solutions
  -f <file_name.dimacs> <comment>: only generate the dimacs file with name 'file_name.dimacs' and a comment 'comment'


DOC;
}
