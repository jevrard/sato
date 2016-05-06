<?php
require_once 'Inequation.php';

/**
 * Represents CSP problem
 * @author Justine Evrard
 * @author Mariam Bouzid
 */
class CSP {

  /**
   * Set of integer variables
   * @var array of IntegerVariable
   */
  private $variables;

  /**
   * Set of clauses of inequation constraints
   * @var array of array of Inequation
   */
  private $constraints;

  /**
   * Initializes internal state of CSP object.
   * @param array of IntegerVariable $setV
   * @param array of array of Inequation $setS
   */
  public function __construct($setV, $setS) {
    $this->variables = $setV;
    $this->constraints = $setS;
  }

  /**
   * Parses a string expression in CSP object
   * Ex: " x  0  2  y  0  2  [x-y<=-1  -x+y<=-1]  [x+y<=0] "
   * @param string $expression
   * @return CSP
   */
  public static function parseExpression($expression) {
    /* Parse integer variables with domains */
    $varPattern = "/\s[a-z]\w*\s\s\-?\d+\s\s\-?\d+\s/i";
    $varMatches = array();
    preg_match_all($varPattern, $expression, $varMatches);
    $vars = array();
    try {
      foreach ($varMatches[0] as $varExpression)
        $vars[] = IntegerVariable::parseExpression($varExpression);
    } catch (Exception $e) {
      throw new Exception($e->getMessage()."CSP class : cannot parse expression.\n");
    }

    /* Parse inequations */
    $constraints = array();
    $expression = preg_replace($varPattern, "", $expression);
    $clausePattern = "/\[[^\[]+\]/";
    $clauses = array();
    preg_match_all($clausePattern, $expression, $clauses);
    try {
      foreach ($clauses[0] as $clause) {
        $inequations = array();
        $clause = preg_split("/\s+/", preg_replace(["/\[/", "/\]/"], "", $clause), null, PREG_SPLIT_NO_EMPTY);
        foreach ($clause as $ineqExpression)
          $inequations[] = Inequation::parseExpression($ineqExpression,$vars);
        $constraints[] = $inequations;
      }
    } catch (Exception $e) {
      throw new Exception($e->getMessage()."CSP class : cannot parse expression.\n");
    }
    return new CSP($vars,$constraints);
  }

  /**
   * Computes the boolean variables compound FNC of a clause at the rank $index
   * Adds in $boolVars all new boolean variables created
   * Adds in $orderRelations the order relation of each primitive comparison
   * Referring to the 9th definition
   * @param int $index
   * @param array of string $boolVars
   * @return array of array of string
   */
  private function computeClauseFNC($index, &$boolVars) {
    $clauseFNC = array(array());
    $c = $this->constraints[$index];
    for($i=0; $i<count($c); $i++) {
      $q = "q".$index.$i;
      $clauseFNC[0][] = $q;
      $boolVars[] = $q;
      $literalFNC = $c[$i]->computeFNC(); // called F_i in definition
      foreach($literalFNC as &$clause) {
        /* substitutes primitive comparison by a predicate */
        foreach($clause as $key => $primComp)
          $clause[$key] = $primComp->predicateEquivalent();
        $clauseFNC[] = array_merge(["-".$q], $clause);
      }
    }
    return $clauseFNC;
  }

  /**
   * Computes the global FNC of the $constraints
   * Adds in $boolVars all new boolean variables created
   * Adds in $orderRelations the order relation of each primitive comparison
   * Referring to the 10th definition
   * @param array of string $boolVars
   * @return array of array of string
   */
  public function computeGlobalFNC(&$boolVars) {
    $globalFNC = array();
    for($i=0; $i<count($this->constraints); $i++)
      $globalFNC = array_merge($globalFNC, $this->computeClauseFNC($i, $boolVars));
    return $globalFNC;
  }

  /**
   * Creates bound and order clauses used as axioms for each variable
   * @param array of string $boolVars
   * @return array of array of string
   */
  public function predicateOrderRelations(&$boolVars) {
		$relations = array();
		foreach($this->variables as $variable)
      $relations = array_merge($relations, $variable->predicateBounds($boolVars), $variable->predicateOrderRelation($boolVars));
		return $relations;
  }

  /**
   * Gives the string representation of @this
   * @return string
   */
  public function __toString() {
    $output = "CSP {\n";
    foreach($this->constraints as $clause) {
      $output .= "\t[";
      foreach($clause as $ineq)
        $output .= $ineq."; ";
      $output .= "],\n";
    }
    return $output."}\n\n";
  }
}
