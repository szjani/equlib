<?php
namespace Equ\Form;

class OptionFlags {
  
  const PLACEHOLDER          = 0x1;
  const LABEL                = 0x2;
  const IMPLICIT_VALIDATORS  = 0x4;
  const EXPLICIT_VALIDATORS  = 0x8;
  const ARRAY_ELEMENTS       = 0x10;
  const HORIZONTAL           = 0x20;
  const ALL                  = 0x3F;

  private $flags;
  
  /**
   * @param int $flags 
   */
  public function __construct($flags = 0) {
    $this->setFlags($flags);
  }
  
  public function addFlag($const) {
    $this->flags = $this->flags | (int)$const;
    return $this;
  }

  public function removeFlag($const) {
    $this->flags = $this->flags & ~$const;
    return $this;
  }

  public function hasFlag($const) {
    return 0 < ($this->flags & $const);
  }

  public function setFlag($const, $boolean) {
    return $boolean ? $this->addFlag($const) : $this->removeFlag($const);
  }

  public function setFlags($flags) {
    $this->flags = $flags;
    return $this;
  }
  
}