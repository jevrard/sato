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
   * Substitutes primitive comparision by boolean variables
   * E.g. : 'x <= c' is replaced by 'p(x,c)'
   * Referring to the a. part of 'Encodage' part
   * @return array of array of string
   */
  public function substitutions() {
  	$predicates = array();
  	try {
		foreach ($this->csp->getClauses() as $clauses)
	  		foreach($clauses->getFNC() as $clause) {
	  			$predicate = array();
	  			foreach($clause as $pc) {
		  			$x = $pc->getLinearExpression()->getTerm(0)->getVar()->getName();
		  			$c = $pc->getConst();
		  			$sign = $pc->getSign();
		  			$predicate[] = ( $sign ? "" : "-")."p(".$x.",".$c.")";
		  			/* TODO
		  			 * check if 'p' is not expended yet
		  			 */
		  		}
		  		$predicates[] = $predicate ;
		  	}
  	} catch (Exception $e) {
  		throw $e ;
  	}
  	return $predicates;
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
   * Gives a sort of SAT representation of the CSP
   * @return array of array of string
   */
  public function encode() {
  	return array_merge($this->substitutions(), $this->bounds());
  }

}
