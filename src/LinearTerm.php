<?php
require_once 'IntegerVariable.php';

/**
 * This file is part of the sato package.
 * (c) 2016 Justine Evrard & Mariam Bouzid
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represents a linear term
 * Form: a.x where a is a non null relative coefficient and x an integer variable
 * @author Justine Evrard & Mariam Bouzid
 */
class LinearTerm
{
  /**
   * @var IntegerVariable  Integer variable
   * @var int              Non null relative coefficient (!=0)
   */
  private $var, $coeff;

  /**
   * Initializes internal state of LinearTerm object
   * @param IntegerVariable $var
   * @param int $coeff
   */
  public function __construct(IntegerVariable $var, $coeff)
  {
    $this->var = $var;
    $this->coeff = (int) $coeff;
  }

  /**
   * Gives the string representation of @this
   * @return string
   */
  public function __toString()
  {
    switch ($this->coeff) {
      case 1: return "".$this->var;
      case -1: return "(-".$this->var.")";
      default:
        if ($this->coeff > 0) return $this->coeff.$this->var;
        else return "(".$this->coeff.$this->var.")";
    }
  }

  /**
   * Adds a value to the coefficient
   * @param int
   */
  public function addCoeff($value)
  {
    $this->coeff += $value;
  }

  /**
   * Gives the term's coefficient
   * @return int
   */
  public function getCoeff()
  {
    return $this->coeff;
  }

  /**
   * Gives the lower bound of the term
   * @return int
   */
  public function getLowerBound()
  {
    if ($this->coeff > 0) return $this->coeff*$this->var->getLowerBound();
    else return $this->coeff*$this->var->getUpperBound();
  }

  /**
   * Gives the upper bound of the term
   * @return int
   */
  public function getUpperBound()
  {
    if ($this->coeff > 0) return $this->coeff*$this->var->getUpperBound();
    else return $this->coeff*$this->var->getLowerBound();
  }

  /**
   * Gives the term's integer variable
   * @return IntegerVariable
   */
  public function getVar()
  {
    return $this->var;
  }

  /**
   * Reseachs in array of LinearTerm by a variable
   * @param IntegerVariable $var
   * @param array of LinearTerm $terms
   * @return int | boolean
   */
  public static function varExistsInArray($var, $terms)
  {
    foreach ($terms as $key => $term)
      if ($term->getVar() === $var) return $key;

    return false;
  }
}
