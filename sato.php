#!/usr/bin/php
<?php
require_once 'src/SAT.php';
//foreach(glob("src/*.php") as $file) require_once $file;
//print_r($argv);

/* Creation of a SAT problem */
$dimacsFilePath = 'example.dimacs';
$comment = "Example of unSAT problem";
$setB = ['px1', 'px5', 'py3'];
$setS = [
  ['-px5'],
  ['-px1', '-py3'],
  ['px1', 'px5'],
  ['px5', 'py3']
];

$sat = new SAT($setB, $setS);
$sat->exportToDimacs($dimacsFilePath, $comment);

/* Use of the SAT solver */
$command = escapeshellcmd("./glucose-syrup/simp/glucose_static $dimacsFilePath output.txt");
$output = shell_exec($command);
echo $output;
