<?php

/**
 * Represents an integer variable with its domain
 * @author Justine Evrard
 * @author Mariam Bouzid
 */
class IntegerVariable {

  /**
   * Integer variable's name
   * @var string
   */
  private $name;

  /**
   * Integer variable's domain
   * Ex: ['l' => 1, 'u' => 5]
   * @var array of int
   */
  private $domain;

  /**
   * Initializes internal state of IntegerVariable object.
   * @param string $name
   * @param int $l
   * @param int $u
   */
  public function __construct($name, $l, $u) {
    $this->name = $name;
    $this->domain = array('l' => $l, 'u' => $u);
  }

  /**
   * Parses a string expression in IntegerVariable objects
   * @param string $expression
   * @return IntegerVariable | throw
   */
  public static function parseExpression($expression) {
    $split = preg_split("/\s+/", $expression, null, PREG_SPLIT_NO_EMPTY);
    if(count($split) != 3 || $split[2] < $split[1]) throw new Exception("IntegerVariable class : invalid expression given.");
    return new IntegerVariable($split[0], (int)$split[1], (int)$split[2]);
  }

  /**
   * Gives the variable's name
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Gives the lower bound of the variable's domain
   * @return int
   */
  public function getLowerBound() {
    return $this->domain['l'];
  }

  /**
   * Gives the upper bound of the variable's domain
   * @return int
   */
  public function getUpperBound() {
    return $this->domain['u'];
  }

  /**
   * Gives the predicate bounds of the integer variable
   * Adds in $boolVars all new boolean variables created
   * @param array of string $boolVars
   * @return array of array of string
   */
  public function predicateBounds(&$boolVars) {
    $lower = "p".$this->name.($this->domain['l']-1);
    $upper = "p".$this->name.$this->domain['u'];
    if(!in_array($lower, $boolVars)) $boolVars[] = $lower;
    if(!in_array($upper, $boolVars)) $boolVars[] = $upper;
    return array(["-".$lower], [$upper]);
  }

  /**
   * Compares two IntegerVariable objetcs
   * @return boolean
   */
  public function equal(IntegerVariable $intVar) {
    return $this->name == $intVar->name && $this->domain['l'] == $intVar->domain['l'] && $this->domain['u'] == $intVar->domain['u'];
  }

  /**
   * Reseachs in array of IntegerVariable by a variable name
   * @param string $varName
   * @param array of IntegerVariable $vars
   * @return IntegerVariable | boolean
   */
  public static function varExistsInArray($varName, $vars) {
    foreach ($vars as $var)
      if($var->getName() === $varName) return $var;
    return false;
  }

  /**
   * Gives the string representation of @this
   * @return string
   */
  public function __toString() {
    return $this->name;
  }
}
