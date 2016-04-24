<?php

/**
 * Representes SAT problem
 * @author Justine Evrard
 */
class SAT {

  /**
   * Set of boolean variable names
   * @var array
   */
  private $setB;

  /**
   * Set of clauses
   * @var array
   */
  private $setS;

  /**
   * Initializes internal state of SAT object.
   * @param array $setB
   * @param array $setS
   */
  public function __construct($setB, $setS) {
    $this->setB = $setB;
    $this->setS = $setS;
  }

  /**
   * Exportes the SAT problem into dimacs file
   * @param string $filename
   */
  public function exportToDimacs($filename, $comment = "") {
    $content = "c Created from SAT object\n";
    $content .= "c ".$comment."\n";
    $content .= "c\n";
    $content .= "p cnf ".count($this->setB)." ".count($this->setS)."\n";
    foreach($this->setS as $clause) {
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
    return $sign.$this->getVarIndexNumber(preg_replace("/^-/", "", $literal));
  }

  /**
   * Gives the index number of a boolean variable
   * @param string $var
   * @return int | throw
   */
  private function getVarIndexNumber($var) {
    if(($key = array_search($var, $this->setB)) === false) throw new Exception("SAT object : Boolean variable $var does not exist in set B !\n");
    return $key+1; // index number need to start at 1, not 0
  }
}
