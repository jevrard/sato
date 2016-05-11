<?php
require_once 'LinearTerm.php';

/**
 * Represents a linear expression
 * Form: a_0.x_0+...+a_n.x_n where the a_i are non null relative coefficients
 * @author Justine Evrard & Mariam Bouzid
 */
class LinearExpression
{
  /**
   * @var array of LinearTerm       Terms of the expression
   * @var array of IntegerVariable  Integer variables of the expression
   */
  private $terms, $variables;

  /**
   * Initializes internal state of LinearExpression object.
   * @param array of LinearTerm $expression
   * @param array of IntegerVariable $vars
   */
  public function __construct($terms, $vars)
  {
    $this->terms = $terms;
    $this->variables = $vars;
  }

  /**
   * Gives the string representation of @this
   * @return string
   */
  public function __toString()
  {
    $output = "";
    $i = 0;
    for ($i; $i< $this->getNumberOfTerms()-1; $i++)
      $output .= $this->terms[$i]."+";
    $output .= $this->terms[$i];

    return $output;
  }

  /**
   * Gives the number of terms in the expression
   * @return int
   */
  public function getNumberOfTerms()
  {
    return count($this->terms);
  }

  /**
   * Gives the term at the rank $index
   * @param int $index
   * @return LinearTerm
   * @throws Exception
   */
  public function getTerm($index)
  {
    if (!in_array($index, array_keys($this->terms))) throw new Exception("LinearExpression object : invalid index given.\n");

    return $this->terms[$index];
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
   * Parses a string expression in LinearTerm objects
   * @param string $expression
   * @param array of IntegerVariable $vars
   * @return array of LinearTerm
   * @throws Exception
   */
  public static function parseExpression($expression, $vars)
  {
    $terms = array();
    $split = preg_split("/\+/", preg_replace("/-/", "+-", $expression), null, PREG_SPLIT_NO_EMPTY); // array of a_i.x_i
    foreach ($split as $term) {
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
      if ($coeff === 0) throw new Exception("LinearExpression class : coefficient cannot equal 0.\n");
      if (!$var = IntegerVariable::varExistsInArray($x, $vars)) throw new Exception("LinearExpression class : variable $x in expression is not in the array of variables.\n");
      $terms[] = new LinearTerm($var, $coeff);
    }

    return new LinearExpression($terms, $vars);
  }
}
