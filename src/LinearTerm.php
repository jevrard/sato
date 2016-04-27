<?php
require_once 'IntegerVariable.php';

/**
 * Represents a linear term
 * Form: a.x where a is a non null relative coefficient and x an integer variable
 * @author Justine Evrard
 * @author Mariam Bouzid
 */
class LinearTerm extends IntegerVariable {

  /**
   * Non null relative coefficient (!=0)
   * @var int
   */
  private $coeff;

  /**
   * Initializes internal state of LinearTerm object
   * @param string $name
   * @param int $l
   * @param int $u
   * @param int $coeff
   */
  public function __construct($name, $l, $u, $coeff) {
    parent::__construct($name, $l, $u);
    $this->coeff = (int) $coeff;
  }

  /**
   * Creates a LinearTerm object with an IntegerVariable object
   * @param IntegerVariable $var
   * @param int $coeff
   */
  public static function constructWithIntegerVariable(IntegerVariable $var, $coeff) {
    return new LinearTerm($var->name, $var->domain['l'], $var->domain['u'], $coeff);
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
    if($this->coeff > 0) return $this->coeff*$this->domain['l'];
    else return $this->coeff*$this->domain['u'];
  }

  /**
   * Gives the upper bound of the term
   * @return int
   */
  public function getUpperBound() {
    if($this->coeff > 0) return $this->coeff*$this->domain['u'];
    else return $this->coeff*$this->domain['l'];
  }

  /**
   * Compares two LinearTerm objetcs
   * @return boolean
   */
  public function equal(LinearTerm $term) {
    return $this->coeff == $term->coeff && parent::equal($term);
  }
}
