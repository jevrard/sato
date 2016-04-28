<?php
require_once 'LinearExpression.php';

/**
 * Represents an inequation, linear in a first time
 * Form: f(u) <= c, where u is a vector like (x_1, ..., x_n) and c is a constant integer
 * @author Justine Evrard
 * @author Mariam Bouzid
 */
class Inequation {

  /**
   * Left-hand side
   * @var LinearExpression
   */
  private $linearE;

  /**
   * Right-hand side
   * @var int
   */
  private $const;
  /**
   * Logical sign of the Inequation object
   * i.e. 0 is negative form, 1 is positive form (default)
   * @var boolean
   */
  private $sign;

  /**
   * Initializes internal state of Inequation object.
   * @param LinearExpression $expression
   * @param int $const
   * @param boolean $sign
   */
  public function __construct(LinearExpression $expression, $const, $sign = 1) {
    $this->linearE = $expression;
    $this->const = $const;
    $this->sign = $sign;
  }

  /**
   * Parses a string expression in Inequation objects
   * @param string $expression
   * @param array of IntegerVariable $vars
   * @return Inequation
   */
  public static function parseExpression($expression,$vars) {
    $expression = trim($expression);
    $split = preg_split("/<=/", $expression);
    if(count($split) != 2) throw new Exception("Inequation class : invalid expression given.");
    try {
      $linearExpression = LinearExpression::parseExpression($split[0], $vars);
    } catch (Exception $e) {
      throw $e;
    }
    return new Inequation($linearExpression,$split[1]);
  }

  /**
   * Tests if @this has only on term on the left
   * @return boolean
   */
  public function isOneTerm() {
    return $this->linearE->getNumberOfTerms() == 1;
  }

  /**
   * Tests if @this is a primitive comparison
   * @return boolean
   */
  public function isPrimitiveComparison() {
    return $this->isOneTerm() && $linearE->getTerm(0)->getCoeff() == 1 ;
  }

  /**
   * Gives the FNC composed of primitive comparaisons (x <= c)
   * Corresponds to proposition 1
   * @return array of array of Inequation
   */
  public function getFNC() {
    $fnc = array();
    $n = $this->linearE->getNumberOfTerms();
    $sum = $this->const-$n+1; //c-n+1
    $combinaisons = LinearExpression::computeCombinaisons($this->linearE->getTermsDomain(), array());
    foreach($combinaisons as $combi) {
      if(array_sum($combi) != $sum) continue;
      $clause = array();
      for($i=0; $i<$n; $i++) {
        $term = $this->linearE->getTerm($i);
        $expression = new LinearExpression(array($term), array($term->getVar()));
        $ineq = new Inequation($expression, $combi[$i]);
        $ineq->hashTranslation();
        $clause[] = $ineq;
      }
      $fnc[] = $clause;
    }
    return $fnc;
  }

  /**
   * Compute the hash translation, given in proposition 1, of one term inequation
   * i.e. transforms one term inequation into primitive comparison
   */
  private function hashTranslation() {
    if(!$this->isOneTerm()) throw new Exception("Inequation object : impossible to apply hashTranslation() on a non one term inequation.");
    $term = $this->linearE->getTerm(0);
    $this->linearE = new LinearExpression(array(new LinearTerm($term->getVar(), 1)), array($term->getVar()));
    $a = $term->getCoeff();
    $q = (float)$this->const/(float)$a;
    if($a > 0) $this->const = (int) floor($q);
    else {
      $this->const = (int) ceil($q)-1;
      $this->sign = 0;
    }
  }

  /**
   * Gives the string representation of @this
   * @return string
   */
  public function __toString() {
    $output = $this->sign? "" : "-";
    $output .= "(".$this->linearE." <= ".$this->const.")";
    return $output;
  }

  /**
   * Gives the linear expression of @this
   * @return LinearExpression
   */
  public function getLinearExpression() {
    return $this->linearE;
  }

  /**
   * Gives the right-hand side of @this
   * @return int
   */
  public function getConst() {
    return $this->const;
  }

  /**
   * Gives the sign of @this
   * @return boolean
   */
  public function getSign() {
    return $this->sign;
  }
  
}
