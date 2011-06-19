<?php
namespace Equ\Form;

interface IOptionFlaggable {
  
  public function setOptionFlags(OptionFlags $flags);
  
  public function getOptionFlags();
  
}