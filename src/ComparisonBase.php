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

  /**
   * Gives all combinaisons of the data
   * @param array of array of mixed $data
   * @param array of array of mixed $combinaisons
   * @return array of array of mixed
   */
  public static function computeCombinaisons($data, $combinaisons)
  {
    if (empty($data))return $combinaisons;
    if (empty($combinaisons)) {
      foreach (array_shift($data) as $value)
        $combinaisons[] = [$value];

      return self::computeCombinaisons($data, $combinaisons);
    }
    $news = array();
    foreach (array_shift($data) as $value)
      foreach ($combinaisons as $combi)
        $news[] = array_merge($combi, [$value]);

    return self::computeCombinaisons($data, $news);
  }

  /**
   * Inverse sign value of @this
   */
  public function inverseSign()
  {
    $this->sign = abs($this->sign-1);
  }
}
