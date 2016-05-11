<?php
require_once 'PrimitiveComparison.php';
require_once 'LinearExpression.php';

/**
 * Represents an inequation, linear in a first time
 * Form: f(u) <= c, where u is a vector like (x_1, ..., x_n) and c is a constant integer
 * @author Justine Evrard & Mariam Bouzid
 */
class Inequation extends ComparisonBase
{
  /**
   * @var LinearExpression  Left-hand side
   */
  private $linearE;

  /**
   * Initializes internal state of Inequation object.
   * @param LinearExpression $expression
   * @param int $const
   * @param boolean $sign
   */
  public function __construct(LinearExpression $expression, $const, $sign = 1)
  {
    $this->linearE = $expression;
    parent::__construct($const, $sign);
  }

  /**
   * Gives the string representation of @this
   * @return string
   */
  public function __toString()
  {
    $output = $this->sign? "" : "-";
    $output .= "(".$this->linearE." <= ".$this->const.")";

    return $output;
  }

  /**
   * Computes the CNF composed of primitive comparaisons 'x <= c'
   * Corresponds to proposition 1
   * @return array of array of PrimitiveComparison
   */
  public function computeCNF()
  {
    $cnf = array();
    $n = $this->linearE->getNumberOfTerms();
    $sum = $this->const-$n+1; //c-n+1
    $combinaisons = self::computeCombinaisons($this->linearE->getTermsDomain(), array()); // constants b_i for the proposition 1
    foreach ($combinaisons as $combi) {
      if (array_sum($combi) != $sum) continue;
      $clause = array();
      for ($i=0; $i<$n; $i++) {
        $term = $this->linearE->getTerm($i);
        $primComp = new PrimitiveComparison($term->getVar(), $combi[$i]);
        $primComp->hashTranslation($term->getCoeff());
        if (!$this->sign) $primComp->inverseSign();
        $clause[] = $primComp;
      }
      $cnf[] = $clause;
    }

    $cnf = array_unique($cnf, SORT_REGULAR);

    if (!$this->sign) {
      $cnf = array_unique(self::computeCombinaisons($cnf, array()), SORT_REGULAR);
      foreach ($cnf as $key => $clause)
        $cnf[$key] = array_unique($clause, SORT_REGULAR);
    }

    /* print FNC */
    echo $this." {\n";
    foreach ($cnf as $clause) {
      echo "\t[";
      foreach ($clause as $literal) {
        echo $literal."; ";
      }
      echo "],\n";
    }
    echo "}\n\n";

    return $cnf;
  }

  /**
   * Tests if @this has only on term on the left
   * @return boolean
   */
  public function isOneTerm()
  {
    return $this->linearE->getNumberOfTerms() == 1;
  }

  /**
   * Parses a string expression in Inequation objects
   * @param string $expression
   * @param array of IntegerVariable $vars
   * @return Inequation
   * @throws Exception
   */
  public static function parseExpression($expression,$vars)
  {
    $expression = trim($expression);
    $split = preg_split("/<=/", $expression);
    if (count($split) != 2) throw new Exception("Inequation class : invalid expression given.\n");
    try {
      $linearExpression = LinearExpression::parseExpression($split[0], $vars);
    } catch (Exception $e) {
      throw new Exception($e->getMessage()."Inequation class : cannot parse expression.\n");
    }

    return new Inequation($linearExpression, (int)$split[1]);
  }
}
