<?php
require_once 'SAT.php';
require_once 'CSP.php';

/**
 * Encodes CSP into SAT problem
 * @author Justine Evrard
 * @author Mariam Bouzid
 */
class Encoder {

  /**
   * CSP problem to encode
   * @var CSP
   */
  private $csp;

  /**
   * SAT problem
   * @var SAT
   */
  private $sat;

  /**
   * Initializes internal state of Encoder object.
   * @param CSP $csp
   */
  public function __construct(CSP $csp) {
    $this->csp = $csp;
    $this->sat = null;
  }

  /**
   * Gives the SAT representation of the CSP
   * @return SAT
   */
  public function encode() {
    $boolVars = array();
    $orderRelations = array();
    try {
      $globalFNC = $this->csp->computeGlobalFNC($boolVars,$orderRelations);
  	} catch (Exception $e) {
      die($e->getMessage());
  	}
    $this->sat = new SAT($boolVars, array_merge($globalFNC,$orderRelations));
    return $this->sat;
  }

  /**
   * Displays the interpretation of the SAT solver result from the output file
   * @param string $filePath
   */
  public function interprete($filePath) {
    if(!$this->sat) die("Cannot interprete a result if the SAT problem does not exist.");
    $content = explode(" ", file_get_contents($filePath));
    unset($content[count($content)-1]); // removes end line 0
    if(empty($content)) die("The SAT problem is unsatisfiable.\n");
    $booleanValues = array();
    foreach ($content as $value)
      $booleanValues[$this->sat->literalFromNumber($value)] = $value < 0 ? "0" : "1";
  }
}
