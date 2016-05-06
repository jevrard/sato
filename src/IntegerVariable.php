<?php

/**
 * Represents an integer variable with its domain
 * @author Justine Evrard & Mariam Bouzid
 */
class IntegerVariable
{
  /**
   * @var string        Integer variable's name
   * @var array of int  Integer variable's domain ; Ex: ['l' => 1, 'u' => 5]
   */
  private $name, $domain;

  /**
   * Initializes internal state of IntegerVariable object.
   * @param string $name
   * @param int $l
   * @param int $u
   */
  public function __construct($name, $l, $u)
  {
    $this->name = $name;
    $this->domain = array('l' => $l, 'u' => $u);
  }

  /**
   * Gives the string representation of @this
   * @return string
   */
  public function __toString()
  {
    return $this->name;
  }

  /**
   * Gives the lower bound of the variable's domain
   * @return int
   */
  public function getLowerBound()
  {
    return $this->domain['l'];
  }

  /**
   * Gives the variable's name
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Gives the upper bound of the variable's domain
   * @return int
   */
  public function getUpperBound()
  {
    return $this->domain['u'];
  }

  /**
   * Parses a string expression in IntegerVariable objects
   * @param string $expression
   * @return IntegerVariable
   * @throws Exception
   */
  public static function parseExpression($expression)
  {
    $split = preg_split("/\s+/", $expression, null, PREG_SPLIT_NO_EMPTY);
    if (count($split) != 3 || $split[2] < $split[1]) throw new Exception("IntegerVariable class : invalid expression given.\n");

    return new IntegerVariable($split[0], (int)$split[1], (int)$split[2]);
  }

  /**
   * Gives the integer variable's bounds composed of predicates
   * Adds in $boolVars all new boolean variables created
   * @param array of string $boolVars
   * @return array of array of string
   */
  public function predicateBounds(&$boolVars)
  {
    $lower = new PrimitiveComparison($this, $this->domain['l']-1, 0);
    $upper = new PrimitiveComparison($this, $this->domain['u']);
    if (!in_array($lower->booleanEquivalent(), $boolVars)) $boolVars[] = $lower->booleanEquivalent();
    if (!in_array($upper->booleanEquivalent(), $boolVars)) $boolVars[] = $upper->booleanEquivalent();

    return array([$lower->predicateEquivalent()], [$upper->predicateEquivalent()]);
  }

  /**
   * Gives the integer variable's order relation composed of predicates
   * Referring to the 8th definition
   * Adds in $boolVars all new boolean variables created
   * @param array of string $boolVars
   * @return array of array string
   */
  public function predicateOrderRelation(&$boolVars)
  {
    $relation = array();
    for ($i=$this->domain['l']; $i<=$this->domain['u']; $i++) {
      $lower = new PrimitiveComparison($this, $i-1, 0);
      $upper = new PrimitiveComparison($this, $i);
      if (!in_array($lower->booleanEquivalent(), $boolVars)) $boolVars[] = $lower->booleanEquivalent();
      if (!in_array($upper->booleanEquivalent(), $boolVars)) $boolVars[] = $upper->booleanEquivalent();
      $relation[] = [$lower->predicateEquivalent(), $upper->predicateEquivalent()];
    }

    return $relation;
  }

  /**
   * Reseachs in array of IntegerVariable by a variable name
   * @param string $varName
   * @param array of IntegerVariable $vars
   * @return IntegerVariable | boolean
   */
  public static function varExistsInArray($varName, $vars)
  {
    foreach ($vars as $var)
      if ($var->getName() === $varName) return $var;

    return false;
  }
}
