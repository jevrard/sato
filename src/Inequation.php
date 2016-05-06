<?php
require_once 'PrimitiveComparison.php';
require_once 'LinearExpression.php';

/**
 * Represents an inequation, linear in a first time
 * Form: f(u) <= c, where u is a vector like (x_1, ..., x_n) and c is a constant integer
 * @author Justine Evrard
 * @author Mariam Bouzid
 */
class Inequation extends ComparisonBase {

  /**
   * Left-hand side
   * @var LinearExpression
   */
  private $linearE;

  /**
   * Initializes internal state of Inequation object.
   * @param LinearExpression $expression
   * @param int $const
   * @param boolean $sign
   */
  public function __construct(LinearExpression $expression, $const, $sign = 1) {
    $this->linearE = $expression;
    parent::__construct($const, $sign);
  }

  /**
   * Parses a string expression in Inequation objects
   * @param string $expression
   * @param array of IntegerVariable $vars
   * @return Inequation | throw
   */
  public static function parseExpression($expression,$vars) {
    $expression = trim($expression);
    $split = preg_split("/<=/", $expression);
    if(count($split) != 2) throw new Exception("Inequation class : invalid expression given.\n");
    try {
      $linearExpression = LinearExpression::parseExpression($split[0], $vars);
    } catch (Exception $e) {
      throw new Exception($e->getMessage()."Inequation class : cannot parse expression.\n");
    }
    return new Inequation($linearExpression, (int)$split[1]);
  }

  /**
   * Tests if @this has only on term on the left
   * @return boolean
   */
  public function isOneTerm() {
    return $this->linearE->getNumberOfTerms() == 1;
  }

  /**
   * Computes the FNC composed of primitive comparaisons 'x <= c'
   * Corresponds to proposition 1
   * @return array of array of PrimitiveComparison
   */
  public function computeFNC() {
    $fnc = array();
    $n = $this->linearE->getNumberOfTerms();
    $sum = $this->const-$n+1; //c-n+1
    $combinaisons = LinearExpression::computeCombinaisons($this->linearE->getTermsDomain(), array());
    echo $this." {\n";
    foreach($combinaisons as $combi) {
      if(array_sum($combi) != $sum) continue;
      echo "\t[";
      $clause = array();
      for($i=0; $i<$n; $i++) {
        $term = $this->linearE->getTerm($i);
        $primComp = new PrimitiveComparison($term->getVar(), $combi[$i]);
        $primComp->hashTranslation($term->getCoeff());
        $clause[] = $primComp;
        echo $primComp."; ";
      }
      $fnc[] = $clause;
      echo "],\n";
    }
    echo "}\n\n";
    return $fnc;
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
}
