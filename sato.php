#!/usr/bin/php
<?php
//foreach(glob("src/*.php") as $file) require_once $file;
//print_r($argv);

$command = escapeshellcmd('./glucose-syrup/simp/glucose_static example.dimacs output.txt');
$output = shell_exec($command);
echo $output;
