<?php
namespace Equ\Object;

interface Validatable {
  
  /**
   * @param Validator $validator
   */
  public static function loadValidators(Validator $validator);
  
}