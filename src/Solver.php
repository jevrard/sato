<?php
require_once 'SAT.php';

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
    try {
      $this->prepare(self::$inputFile);
      $command = escapeshellcmd("./glucose-syrup/simp/glucose_static ".self::$inputFile." ".self::$outputFile);
      $output = shell_exec($command);
      if ($verbose) echo $output."\n";
      if (($interpretation = $this->interprete()) === NULL)
        echo "--> The SAT problem is unsatisfiable.\n";
      else {
        echo "--> The SAT problem is satisfiable.\n";
        if ($interprete) {
          echo "\nThe solution tuple is :\n";
          foreach ($interpretation as $name => $value) echo "\t$name = $value ";
        }
      }
    } catch (Exception $e) {
      throw new Exception($e->getMessage()."Solver object : cannot solve the SAT problem.\n");
    }
  }
}
