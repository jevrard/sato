<?php

/**
 * Representes SAT problem
 * @author Justine Evrard
 */
class SAT {

  /**
   * Set of boolean variable names
   * @var array of string
   */
  private $variables;

  /**
   * Set of clauses
   * @var array of array of string
   */
  private $clauses;

  /**
   * Initializes internal state of SAT object.
   * @param array of string $setB
   * @param array of array of string $setS
   */
  public function __construct($setB, $setS) {
    $this->variables = $setB;
    $this->clauses = $setS;
  }

  /**
   * Exportes the SAT problem into dimacs file
   * @param string $filename
   */
  public function exportToDimacs($filename, $comment = "") {
    $content = "c Created from SAT object\n";
    $content .= "c ".$comment."\n";
    $content .= "c\n";
    $content .= "p cnf ".count($this->variables)." ".count($this->clauses)."\n";
    foreach($this->clauses as $clause) {
      foreach($clause as $literal)
        try {
          $content .= $this->literalToNumber($literal)." ";
        } catch (Exception $e) {
          echo $e->getMessage();
          die("SAT object : SAT problem cannot be exported. Check your problem !\n");
        }
      $content .= "0\n";
    }
    file_put_contents($filename, $content);
  }

  /**
   * Transforms a literal into a number for dimacs file
   * @param string $filename
   * @return string | throw
   */
  private function literalToNumber($literal) {
    $sign = "";
    if(preg_match("/^-/", $literal)) $sign = "-";
    return $sign.$this->varIndexNumber(preg_replace("/^-/", "", $literal));
  }

  /**
   * Gives the index number of a boolean variable
   * @param string $var
   * @return int | throw
   */
  private function varIndexNumber($var) {
    if(($key = array_search($var, $this->variables)) === false) throw new Exception("SAT object : Boolean variable $var does not exist in set B.\n");
    return $key+1; // index number need to start at 1, not 0
  }

  /**
   * Gives the string representation of @this
   * @return string
   */
  public function __toString() {
    $output = "SAT {\n";
    foreach($this->clauses as $clause) {
      $output .= "\t[";
      foreach($clause as $ineq)
        $output .= $ineq."; ";
      $output .= "],\n";
    }
    $output .= "}\n";
    $output .= "variables : ".implode(" ", $this->variables)."\n\n";
    return $output;
  }
}
