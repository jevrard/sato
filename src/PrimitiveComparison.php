<?php
require_once 'ComparisonBase.php';
require_once 'IntegerVariable.php';

/**
 * Represents a primitive comparison like 'x <= c'
 * @author Justine Evrard & Mariam Bouzid
 */
class PrimitiveComparison extends ComparisonBase
{
  /**
   * @var IntegerVariable  Left-hand side
   */
  private $variable;

  /**
   * Initializes internal state of PrimitiveComparison object.
   * @param IntegerVariable $var
   * @param int $const
   * @param boolean $sign
   */
  public function __construct(IntegerVariable $var, $const, $sign = 1)
  {
    $this->variable = $var;
    parent::__construct($const, $sign);
  }

  /**
   * Gives the string representation of @this
   * @return string
   */
  public function __toString()
  {
    $output = $this->sign? "" : "-";
    $output .= "(".$this->variable." <= ".$this->const.")";

    return $output;
  }

  /**
   * Gives the boolean equivalent of @this (without $sign)
   * i.e. transforms 'x <= c' into 'pxc'
   * @return string
   */
  public function booleanEquivalent()
  {
    return "p".$this->variable->getName().$this->const;
  }

  /**
   * Compute the hash translation, given in proposition 1
   * i.e. eliminates the coefficient before the variable of expression 'ax <= b'
   * @param int $coeff  Coefficient before the variable
   * @throws Exception
   */
  public function hashTranslation($coeff)
  {
    if ($coeff === 0) throw new Exception("PrimitiveComparison object : coefficient cannot equal 0.\n");
    $q = (float)$this->const/(float)$coeff;
    if ($coeff > 0) $this->const = (int) floor($q);
    else {
      $this->const = (int) ceil($q)-1;
      $this->sign = 0;
    }
  }

  /**
   * Gives the predicate equivalent of @this (with $sign)
   * i.e. transforms '-(x <= c)' into '-pxc'
   * @return string
   */
  public function predicateEquivalent()
  {
    $sign = $this->sign ? "" : "-";

    return $sign.$this->booleanEquivalent();
  }

  /**
   * Gives the reverse of @this
   * i.e. transforms 'x <= c' into '-(x <= c-1)' (means 'x > c')
   * @return PrimitiveComparison
   */
  public function reverse()
  {
    return new PrimitiveComparison($this->variable, $this->const-1, abs($this->sign-1));
  }
}
