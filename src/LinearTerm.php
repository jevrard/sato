<?php
require_once 'IntegerVariable.php';

/**
 * Represents a linear term
 * Form: a.x where a is a non null relative coefficient and x an integer variable
 * @author Justine Evrard
 * @author Mariam Bouzid
 */
class LinearTerm {

  /**
   * Integer variable
   * @var IntegerVariable
   */
  private $var;

  /**
   * Non null relative coefficient (!=0)
   * @var int
   */
  private $coeff;

  /**
   * Initializes internal state of LinearTerm object
   * @param IntegerVariable $var
   * @param int $coeff
   */
  public function __construct(IntegerVariable $var, $coeff) {
    $this->var = $var;
    $this->coeff = (int) $coeff;
  }

  /**
   * Gives the term's integer variable
   * @return IntegerVariable
   */
  public function getVar() {
    return $this->var;
  }

  /**
   * Gives the term's coefficient
   * @return int
   */
  public function getCoeff() {
    return $this->coeff;
  }

  /**
   * Gives the lower bound of the term
   * @return int
   */
  public function getLowerBound() {
    if($this->coeff > 0) return $this->coeff*$this->var->getLowerBound();
    else return $this->coeff*$this->var->getUpperBound();
  }

  /**
   * Gives the upper bound of the term
   * @return int
   */
  public function getUpperBound() {
    if($this->coeff > 0) return $this->coeff*$this->var->getUpperBound();
    else return $this->coeff*$this->var->getLowerBound();
  }

  /**
   * Compares two LinearTerm objetcs
   * @return boolean
   */
  public function equal(LinearTerm $term) {
    return $this->coeff == $term->coeff && $this->var->equal($term->var);
  }

  /**
   * Gives the string representation of @this
   * @return string
   */
  public function __toString() {
    switch($this->coeff) {
      case 1: return "".$this->var;
      case -1: return "(-".$this->var.")";
      default:
        if($this->coeff > 0) return $this->coeff.$this->var;
        else return "(".$this->coeff.$this->var.")";
    }
  }
}
