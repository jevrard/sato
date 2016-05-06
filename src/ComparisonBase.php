<?php

/**
 * Represents the base of a comparison
 * @author Justine Evrard & Mariam Bouzid
 */
abstract class ComparisonBase
{
  /**
   * @var int      Right-hand side of comparison
   * @var boolean  Logical sign of comparison, i.e. 0 is negative form, 1 is positive form (default)
   */
  protected $const, $sign;

  /**
   * Initializes internal state of ComparisonBase object.
   * @param int $const
   * @param boolean $sign
   */
  public function __construct($const, $sign = 1)
  {
    $this->const = $const;
    $this->sign = $sign;
  }
}
