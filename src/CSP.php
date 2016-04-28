
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
   * @param array of Inequation $setS
   */
  public function __construct($setV, $setS) {
    $this->variables = $setV;
    $this->constraints = $setS;
  }

  /**
   * Parses a string expression in CSP object
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
    $expression = preg_replace($varPattern, "", $expression);
    $inequations = array();
    $split = preg_split("/\s+/", $expression, null, PREG_SPLIT_NO_EMPTY);
    try {
      foreach ($split as $ineqExpression)
        $inequations[] = Inequation::parseExpression($ineqExpression,$vars);
    } catch (Exception $e) {
      die($e->getMessage());
    }

    return new CSP($vars,$inequations);
  }

  /**
   * Gives the set of integer variables
   * @return array of IntegerVariables
   */
   public function getVariables(){
  	return $this->variables ;
  }

  /**
   * Gives the constraints
   * @return array of array of Inequation
   */
  public function getConstraints(){
  	return $this->constraints;
  }

  /**
   * Gives the clause at the rank $index
   * @return array of Inequation
   */
  public function getClause($index){
  	return $this->constraints[$index];
  }
}
