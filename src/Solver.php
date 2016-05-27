<?php
require_once 'SAT.php';

/**
 * This file is part of the sato package.
 * (c) 2016 Justine Evrard & Mariam Bouzid
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Solves a SAT problem
 * @author Justine Evrard & Mariam Bouzid
 */
class Solver
{
  /**
   * @var SAT $sat SAT problem encoded
   */
  private $sat;

  /**
   * Default names of the I/O files for the SAT solver
   * @var string $inputFile
   * @var string $outputFile
   */
  static private $inputFile = "sat.dimacs", $outputFile = "output.txt";

  /**
   * Initializes internal state of Solver object.
   * @param SAT $sat
   */
  public function __construct(SAT $sat)
  {
    $this->sat = $sat;
  }

  /**
   * Adds the new result in the DIMACS file
   */
  private function addResultInDimacs()
  {
    if (($output = file_get_contents(self::$outputFile)) === false) throw new Exception("Solver object : cannot read the file ".self::$outputFile.".\n");
    $split = explode(" ", $output);
    foreach ($split as &$value)
      $value = -1 * (int) $value;

    if (($dimacs = file_get_contents(self::$inputFile)) === false) throw new Exception("Solver object : cannot read the file ".self::$inputFile.".\n");

    preg_match("/(p\scnf\s\d+\s)(\d+)\s/", $dimacs, $header);
    $dimacs = preg_replace("/p\scnf\s\d+\s(\d+)/", $header[1].((int)$header[2]+1), $dimacs).implode(" ", $split)."\n";

    if (file_put_contents(self::$inputFile, $dimacs) === false) throw new Exception("Solver object : cannot write in the file ".self::$inputFile.".\n");
  }

  /**
   * Executes the SAT solver glucose
   * @param boolean $verbose Displays or not the SAT solver's output
   */
  private function execute($verbose = false)
  {
    $command = escapeshellcmd("./glucose-syrup/simp/glucose_static ".self::$inputFile." ".self::$outputFile);
    $output = shell_exec($command);
    if ($verbose) echo $output."\n";
  }

  /**
   * Displays the interpretation of the SAT solver's result
   * @return NULL|array of int
   * @throws Exception
   */
  private function findAllSolutions()
  {
    $solution = array();
    try {
      while ($this->isSatisfiable()) {
        $solutions[] = $this->interprete();
        $this->addResultInDimacs();
        $this->execute();
      }
    } catch (Exception $e) {
      throw new Exception($e->getMessage()."Solver class : cannot interprete the result.\n");
    }

    return array_unique($solutions, SORT_REGULAR);
  }

  /**
   * Displays the interpretation of the SAT solver's result
   * @return NULL|array of int
   * @throws Exception
   */
  private function interprete()
  {
    if (($content = file_get_contents(self::$outputFile)) === false) throw new Exception("Solver object : cannot read the file ".self::$outputFile.".\n");
    $content = explode(" ", $content);
    unset($content[count($content)-1]); // removes end line 0
    if (empty($content)) return NULL;

    $interpretation = array();
    try {
      foreach ($content as $value) {
        $booleanVar = $this->sat->literalFromNumber($value);
        if (preg_match("/^q/", $booleanVar)) continue; // ignores non primitive comparison boolean variable
        if ($value < 0) continue; // ignores negative boolean variable
        $parse = PrimitiveComparison::parseExpression($booleanVar);
        $interpretation[$parse['name']][] = $parse['const'];
      }
    } catch (Exception $e) {
      throw new Exception($e->getMessage()."Solver class : cannot interprete the result.\n");
    }

    foreach ($interpretation as $key => $values)
      $interpretation[$key] = min($values);

    return $interpretation;
  }

  /**
   * Determines the satisfiability of the current state of .dimacs file
   * @return boolean
   */
  private function isSatisfiable()
  {
    if (($content = file_get_contents(self::$outputFile)) === false) throw new Exception("Solver object : cannot read the file ".self::$outputFile.".\n");
    if (preg_match("/^UNSAT/", $content)) return false;
    return true;
  }

  /**
   * Makes the DIMACS file
   * @param string $filePath
   * @throws Exception
   */
  public function prepare($filePath, $comment = "")
  {
    try {
      $this->sat->exportToDimacs($filePath, $comment);
    } catch (Exception $e) {
      throw new Exception($e->getMessage()."Solver object : cannot prepare the DIMACS file.\n");
    }
  }

  /**
   * Calls SAT solver on the SAT problem
   * @param boolean $verbose Displays or not the SAT solver's output
   * @param boolean $interprete Interpretes or not the SAT solver's result
   * @throws Exception
   */
  public function solve($verbose, $interprete)
  {
    $this->prepare(self::$inputFile);
    $this->execute($verbose);
    if (!$this->isSatisfiable())
      echo "--> The SAT problem is unsatisfiable.\n";
    else {
      echo "--> The SAT problem is satisfiable.\n";
      if ($interprete) {
        try {
          $solutions = $this->findAllSolutions();
          echo "\nThere are ".count($solutions). " solution(s) :\n";
          foreach ($solutions as $interpretation) {
            foreach ($interpretation as $name => $value) echo "\t$name = $value ";
            echo "\n";
          }
        } catch (Exception $e) {
          throw new Exception($e->getMessage()."Solver object : cannot solve the SAT problem.\n");
        }
      }
    }
  }
}
