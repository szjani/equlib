<?php
namespace Equ\Form;

interface IOptionFlaggable {
  
  /**
   * @param OptionFlags $flags
   */
  public function setOptionFlags(OptionFlags $flags);
  
  /**
   * @return OptionFlags
   */
  public function getOptionFlags();
  
}