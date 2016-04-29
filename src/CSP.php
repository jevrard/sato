
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
    $varPattern = "/\s[a-z]\w*\s\s\d+\s\s\d+\s/i";
    $varMatches = array();
    preg_match_all($varPattern, $expression, $varMatches);
    $vars = array();
    try {
      foreach ($varMatches[0] as $varExpression)
        $vars[] = IntegerVariable::parseExpression($varExpression);
    } catch (Exception $e) {
      die($e->getMessage());
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
      die($e->getMessage());
    }
    return new CSP($vars,$constraints);
  }

  /**
   * Computes the boolean variables compound FNC of a clause at the rank $index
   * Adds in $predicates all new created predicates
   * Referring to the 9th definition
   * @return array of string
   */
  private function computeClauseFNC($index, &$predicates) {
    $clauseFNC = array(array());
    $c = $this->constraints[$index];
    for($i=0; $i<count($c); $i++) {
      $q = "q".$index.$i;
      $clauseFNC[0][] = $q;
      $predicates[] = $q;
      $literalFNC = $c[$i]->computeFNC();
      foreach($literalFNC as &$clause) {
        /* substitutes primitive comparison by boolean variable */
        foreach($clause as $key => $ineq) {
          $clause[$key] = $ineq->predicateEquivalent();
          $predicates[] = preg_replace("/^-/", "", $clause[$key]);
        }
        $clauseFNC[] = array_merge(["-".$q], $clause);
      }
    }
    $predicates = array_unique($predicates);
    return $clauseFNC;
  }

  /**
   * Computes the global FNC of the $constraints
   * Adds in $predicates all new created predicates
   * Referring to the 10th definition
   * @return array of array of string
   */
  public function computeGlobalFNC(&$predicates) {
    $globalFNC = array();
    for($i=0; $i<count($this->constraints); $i++)
      $globalFNC = array_merge($globalFNC, $this->computeClauseFNC($i, $predicates));
    return $globalFNC;
  }

  /**
   * Gives the set of integer variables
   * @return array of IntegerVariables
   */
   public function getVariables(){
  	return $this->variables ;
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
    return $output."}\n";
  }
}
