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
   * Determines the bounds axioms
   * Referring to the b. part , particularly the 8th definition
   * @return array of array of string
   */
  public function bounds() {
  	$bounds = array();
  	foreach ($this->csp->getVariables() as $var) {
  		$lower = "-p(".$var->getName().",".($var->getLowerBound()-1).")";
  		$upper = "p(".$var->getName().",".$var->getUpperBound().")";
  		$bounds[] = [ $lower ];
  		$bounds[] = [ $upper ];
  	}
  	return $bounds;
  }

  /**
   * Gives the SAT representation of the CSP
   * @return SAT
   */
  public function encode() {
  	return array_merge($this->substitutions(), $this->bounds());
  }

}
