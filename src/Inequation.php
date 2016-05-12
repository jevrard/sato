<?php
require_once 'PrimitiveComparison.php';
require_once 'LinearTerm.php';

/**
 * Represents an inequation, linear in a first time
 * Form: f(u) <= c, where u is a vector like (x_1, ..., x_n) and c is a constant integer
 * @author Justine Evrard & Mariam Bouzid
 */
class Inequation extends ComparisonBase
{
  /**
   * @var array of LinearTerm       Left-hand side
   * @var array of IntegerVariable  Integer variables of the expression
   */
  private $terms, $variables;

  /**
   * Initializes internal state of Inequation object.
   * @param array of LinearTerm $terms
   * @param array of IntegerVariable $vars
   * @param int $const
   * @param boolean $sign
   */
  public function __construct($terms, $vars, $const, $sign = 1)
  {
    $this->terms = $terms;
    $this->variables = $vars;
    parent::__construct($const, $sign);
  }

  /**
   * Gives the string representation of @this
   * @return string
   */
  public function __toString()
  {
    $output = $this->sign? "" : "-";
    $output .= "(";
    $i = 0;
    for ($i; $i< count($this->terms)-1; $i++)
      $output .= $this->terms[$i]."+";
    $output .= $this->terms[$i]." <= ".$this->const.")";

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
    $n = count($this->terms);
    $sum = $this->const-$n+1; //c-n+1
    $combinaisons = self::computeCombinaisons($this->getTermsDomain(), array()); // constants b_i for the proposition 1
    foreach ($combinaisons as $combi) {
      if (array_sum($combi) != $sum) continue;
      $clause = array();
      for ($i=0; $i<$n; $i++) {
        $term = $this->terms[$i];
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
   * Gives all terms' domain
   * @return array of array of int
   */
  public function getTermsDomain()
  {
    $domains = array();
    foreach ($this->terms as $term) {
      $domain = array();
      for ($i=$term->getLowerBound()-1; $i<=$term->getUpperBound(); $i++)
        $domain[] = $i;
      $domains[] = $domain;
    }

    return $domains;
  }

  /**
   * Tests if @this has only on term on the left
   * @return boolean
   */
  public function isOneTerm()
  {
    return count($this->terms) == 1;
  }

  /**
	 * Normalizes an inequation
	 * @param string $expression
	 * @param boolean $sign
	 */
	public static function normalizeExpression(&$expression, &$sign) {
    $match = array();
    preg_match("/^\-\((.*)\)$/", $expression, $match);
    if (count($match) == 2) {
      $expression = $match[1];
      $sign = abs($sign-1);
    }

    if (preg_match("/<=/", $expression)) return;
    if (preg_match("/>/", $expression)) $sign = abs($sign-1);
    if (preg_match("/>=|<[^=]/", $expression)) $expression = "1+".$expression;
    $exp = preg_replace(["/</", "/>=/", "/>/"], "<=", $expression);
    if ($exp === $expression) throw new Exception("Inequation class : invalid expression given.\n");
    $expression = $exp;

    self::normalizeExpression($expression, $sign);
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
    $sign = 1;
    $expression = trim($expression);
    self::normalizeExpression($expression, $sign);
    $split = preg_split("/<=/", $expression);
    if (count($split) != 2) throw new Exception("Inequation class : invalid expression given.\n");

    $terms = array();
    $splitTerms = preg_split("/\+/", preg_replace("/-/", "+-", $split[0]), null, PREG_SPLIT_NO_EMPTY); // array of a_i.x_i
    foreach ($splitTerms as $term) {
      $coeff = 1;
      $match = array();
      $x = $term;
      if (preg_match("/^-?\d+/", $term, $match)) {
        $coeff = (int)$match[0];
        $x = preg_replace("/^$coeff/", "", $term);
      } else if (preg_match("/^-/", $term, $match)) {
        $coeff = -1;
        $x = preg_replace("/^-/", "", $term);
      }

      if ($x == NULL) {
        $split[1] += -1*$coeff;
        continue;
      }
      if ($coeff === 0) throw new Exception("Inequation class : coefficient cannot equal 0.\n");
      if (($var = IntegerVariable::varExistsInArray($x, $vars)) === false) throw new Exception("Inequation class : variable $x in expression is not in the array of variables.\n");
      if (($key = LinearTerm::varExistsInArray($var, $terms)) === false) $terms[] = new LinearTerm($var, $coeff);
      else $terms[$key]->addCoeff($coeff);
    }

    return new Inequation($terms, $vars, (int)$split[1], $sign);
  }
}
