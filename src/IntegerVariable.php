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
   * @return IntegerVariable|boolean
   */
  public static function varExistsInArray($varName, $vars) {
    foreach ($vars as $var)
      if($var->getName() === $varName) return $var;
    return false;
  }
}
