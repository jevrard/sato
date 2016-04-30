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
   * Initializes internal state of Encoder object.
   * @param CSP $csp
   */
  public function __construct(CSP $csp) {
    $this->csp = $csp;
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
    return new SAT($boolVars, array_merge($globalFNC,$orderRelations));
  }

}
