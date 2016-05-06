<?php
require_once 'SAT.php';
require_once 'CSP.php';

/**
 * Encodes CSP into SAT problem
 * @author Justine Evrard & Mariam Bouzid
 */
class Encoder
{
  /**
   * @var CSP $csp  CSP problem to encode
   * @var SAT|null $sat  SAT problem encoded
   */
  private $csp, $sat;

  /**
   * Initializes internal state of Encoder object.
   * @param CSP $csp
   */
  public function __construct(CSP $csp)
  {
    $this->csp = $csp;
    $this->sat = null;
  }

  /**
   * Gives the SAT representation of the CSP
   * @return SAT
   * @throws Exception
   */
  public function encode()
  {
    $boolVars = array();
    try {
      $globalFNC = $this->csp->computeGlobalFNC($boolVars);
  	} catch (Exception $e) {
      throw new Exception($e->getMessage()."Encoder class : cannot encode CSP.\n");
  	}
    $orderRelations = $this->csp->predicateOrderRelations($boolVars);
    $this->sat = new SAT($boolVars, array_merge($globalFNC, $orderRelations));

    return $this->sat;
  }

  /**
   * Displays the interpretation of the SAT solver result from the output file
   * @param string $filePath
   * @throws Exception
   */
  public function interprete($filePath)
  {
    if (!$this->sat) throw new Exception("Encoder object : cannot interprete a result if the SAT problem does not exist.\n");
    if (($content = file_get_contents($filePath)) === false) throw new Exception("Encoder object : cannot read the file $filePath.\n");
    $content = explode(" ", $content);
    unset($content[count($content)-1]); // removes end line 0
    if (empty($content)) throw new Exception("--> The SAT problem is unsatisfiable.\n");
    $booleanValues = array();
    foreach ($content as $value)
      $booleanValues[$this->sat->literalFromNumber($value)] = $value < 0 ? "0" : "1";
    echo "--> Interpretation of the solver SAT's result :\n";
    print_r($booleanValues);
  }
}
