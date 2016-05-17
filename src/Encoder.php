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
   */
  private $csp;

  /**
   * Initializes internal state of Encoder object.
   * @param CSP $csp
   */
  public function __construct(CSP $csp)
  {
    $this->csp = $csp;
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
      $globalCNF = $this->csp->computeGlobalCNF($boolVars);
  	} catch (Exception $e) {
      throw new Exception($e->getMessage()."Encoder class : cannot encode CSP.\n");
  	}
    $orderRelations = $this->csp->predicateOrderRelations($boolVars);

    return new SAT($boolVars, array_merge($globalCNF, $orderRelations));
  }
}
