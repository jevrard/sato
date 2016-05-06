<?php

/**
 * Representes SAT problem
 * @author Justine Evrard & Mariam Bouzid
 */
class SAT
{
  /**
   * @var array of string           Set of boolean variable names
   * @var array of array of string  Set of clauses
   */
  private $variables, $clauses;

  /**
   * Initializes internal state of SAT object.
   * @param array of string $setB
   * @param array of array of string $setS
   */
  public function __construct($setB, $setS)
  {
    $this->variables = $setB;
    $this->clauses = $setS;
  }

  /**
   * Gives the string representation of @this
   * @return string
   */
  public function __toString()
  {
    $output = "SAT {\n";
    foreach ($this->clauses as $clause) {
      $output .= "\t[";
      foreach ($clause as $ineq)
        $output .= $ineq."; ";
      $output .= "],\n";
    }
    $output .= "}\n";
    $output .= "Variables : ".implode(" ", $this->variables)."\n\n";

    return $output;
  }

  /**
   * Exportes the SAT problem into dimacs file
   * @param string $filename
   * @param string $comment
   * @throws Exception
   */
  public function exportToDimacs($filename, $comment = "")
  {
    $content = "c Created from SAT object\n";
    $content .= "c ".$comment."\n";
    $content .= "c\n";
    $content .= "p cnf ".count($this->variables)." ".count($this->clauses)."\n";
    foreach ($this->clauses as $clause) {
      foreach ($clause as $literal)
        try {
          $content .= $this->literalToNumber($literal)." ";
        } catch (Exception $e) {
          throw new Exception($e->getMessage()."SAT object : SAT problem cannot be exported.\n");
        }
      $content .= "0\n";
    }
    file_put_contents($filename, $content);
  }

  /**
   * Transforms a number into a literal
   * @param int $number
   * @return string
   * @throws Exception
   */
  public function literalFromNumber($number)
  {
    $index = abs($number)-1;
    if (!in_array($index, array_keys($this->variables))) throw new Exception("SAT object : invalid number given.\n");

    return $this->variables[$index];
  }

  /**
   * Transforms a literal into a number for dimacs file
   * @param string $filename
   * @return int
   * @throws Exception
   */
  private function literalToNumber($literal)
  {
    $sign = "";
    if (preg_match("/^-/", $literal)) $sign = "-";

    return (int)($sign.$this->varIndexNumber(preg_replace("/^-/", "", $literal)));
  }

  /**
   * Gives the index number of a boolean variable
   * @param string $var
   * @return int
   * @throws Exception
   */
  private function varIndexNumber($var)
  {
    if (($key = array_search($var, $this->variables)) === false) throw new Exception("SAT object : boolean variable $var does not exist in set B.\n");

    return $key+1; // index number need to start at 1, not 0
  }
}
