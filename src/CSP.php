<?php
require_once 'Inequation.php';

/**
 * This file is part of the sato package.
 * (c) 2016 Justine Evrard & Mariam Bouzid
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represents CSP problem
 * @author Justine Evrard & Mariam Bouzid
 */
class CSP
{
  /**
   * @var array of IntegerVariable      Set of integer variables
   * @var array of array of Inequation  Set of clauses of inequation constraints
   */
  private $variables, $constraints;

  /**
   * Initializes internal state of CSP object.
   * @param array of IntegerVariable $setV
   * @param array of array of Inequation $setS
   */
  public function __construct($setV, $setS)
  {
    $this->variables = $setV;
    $this->constraints = $setS;
  }

  /**
   * Gives the string representation of @this
   * @return string
   */
  public function __toString()
  {
    $output = "CSP {\n";
    foreach ($this->constraints as $clause) {
      $output .= "\t[";
      foreach ($clause as $ineq)
        $output .= $ineq."; ";
      $output .= "],\n";
    }

    return $output."}\n\n";
  }

  /**
   * Computes the boolean variables compound CNF of a clause at the rank $index
   * Adds in $boolVars all new boolean variables created
   * Adds in $orderRelations the order relation of each primitive comparison
   * Referring to the 9th definition
   * @param int $index
   * @param array of string $boolVars
   * @return array of array of string
   */
  private function computeClauseCNF($index, &$boolVars)
  {
    $clauseCNF = array(array());
    $c = $this->constraints[$index];
    for ($i=0; $i<count($c); $i++) {
      $q = "q".$index.$i;
      $clauseCNF[0][] = $q;
      $boolVars[] = $q;
      $literalCNF = $c[$i]->computeCNF(); // called F_i in definition
      foreach ($literalCNF as &$clause) {
        /* substitutes primitive comparison by a predicate */
        foreach ($clause as $key => $primComp)
          $clause[$key] = $primComp->predicateEquivalent();
        $clauseCNF[] = array_merge(["-".$q], $clause);
      }
    }

    return $clauseCNF;
  }

  /**
   * Computes the global CNF of the $constraints
   * Adds in $boolVars all new boolean variables created
   * Adds in $orderRelations the order relation of each primitive comparison
   * Referring to the 10th definition
   * @param array of string $boolVars
   * @return array of array of string
   */
  public function computeGlobalCNF(&$boolVars)
  {
    $globalCNF = array();
    for($i=0; $i<count($this->constraints); $i++)
      $globalCNF = array_merge($globalCNF, $this->computeClauseCNF($i, $boolVars));

    return $globalCNF;
  }

  /**
	 * Computes the distribution between two CNFs
	 * @param array of array of string $cnf1
   * @param array of array of string $cnf2
   * @return array of array of string
	 */
	public static function distribute($cnf1, $cnf2) {
    if (empty($cnf1)) return $cnf2;
    if (empty($cnf2)) return $cnf1;

    $cnf = array();
    foreach ($cnf1 as $clause1)
      foreach ($cnf2 as $clause2)
        $cnf[] = array_unique(array_merge($clause1, $clause2));

    return $cnf;
  }

  /**
	 * Normalizes a constraint
	 * @param string $expression
   * @return array of array of string
	 */
	public static function normalizeExpression($expression) {
    $patterns = "/!=|<=|>=|=|<|>/";
    $split = preg_split($patterns, $expression);
    if (count($split) != 2) throw new Exception("CSP class : invalid expression given.\n");

    if ($split[1] == "0") $exp = $split[0];
    else {
      if (preg_match("/^[^\-]/", $split[1])) $split[1] = "+".$split[1];
      $split[1] = preg_replace(["/\+/", "/\-/"], ["!", "+"], $split[1]);
      $split[1] = preg_replace("/!/", "-", $split[1]);
      $exp = implode("", $split);
    }

    $mapping = array(
      "/!=/" => [["$exp<=-1", "-($exp<=0)"]],
      "/<=/" => [["$exp<=0"]],
      "/>=/" => [["$exp>=0"]],
      "/=/" => [["$exp<=0"], ["-($exp<=-1)"]],
      "/</" => [["$exp<0"]],
      "/>/" => [["$exp>0"]],
    );

    foreach (array_keys($mapping) as $pattern)
      if (preg_match($pattern, $expression)) return $mapping[$pattern];

    throw new Exception("CSP class : invalid expression given.\n");
	}

  /**
   * Parses a string expression in CSP object
   * Ex: " x  1  3  y  -1  0  ['x+y<1']  ['-(-x+y>=-3)'] "
   * @param string $expression
   * @return CSP
   * @throws Exception
   */
  public static function parseExpression($expression)
  {
    /* Parse integer variables with domains */
    $vars = array();
    $varMatches = array();
    $varPattern = "/\s[a-z]\w*\s\s\-?\d+\s\s\-?\d+\s/i";
    preg_match_all($varPattern, $expression, $varMatches);
    try {
      foreach ($varMatches[0] as $varExpression)
        $vars[] = IntegerVariable::parseExpression($varExpression);
    } catch (Exception $e) {
      throw new Exception($e->getMessage()."CSP class : cannot parse expression.\n");
    }

    /* Parse constraints */
    $clauses = array();
    $clauseMatches = array();
    $constraints = array();
    preg_match_all("/\[[^\[]+\]/", preg_replace($varPattern, "", $expression), $clauseMatches);
    try {
      foreach ($clauseMatches[0] as $clause) {
        $clause = preg_split("/\s+/", preg_replace(["/\[/", "/\]/"], "", $clause), null, PREG_SPLIT_NO_EMPTY);
        $set = array();
        foreach ($clause as $literal)
          $set = self::distribute($set, self::normalizeExpression($literal));
        $constraints = array_merge($constraints, $set);
      }

      foreach ($constraints as $clause) {
        $inequations = array();
        foreach ($clause as $ineqExpression)
          $inequations[] = Inequation::parseExpression($ineqExpression,$vars);
        $clauses[] = $inequations;
      }
    } catch (Exception $e) {
      throw new Exception($e->getMessage()."CSP class : cannot parse expression.\n");
    }

    return new CSP($vars, $clauses);
  }

  /**
   * Creates bound and order clauses used as axioms for each variable
   * @param array of string $boolVars
   * @return array of array of string
   */
  public function predicateOrderRelations(&$boolVars)
  {
		$relations = array();
		foreach ($this->variables as $variable)
      $relations = array_merge($relations, $variable->predicateBounds($boolVars), $variable->predicateOrderRelation($boolVars));

    return $relations;
  }
}
