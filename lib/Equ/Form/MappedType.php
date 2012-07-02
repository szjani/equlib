<?php
namespace Equ\Form;

abstract class MappedType implements IMappedType
{

    /**
      * @return OptionFlags
      */
    public function getOptionFlags()
    {
        return new OptionFlags(
              OptionFlags::LABEL
            |OptionFlags::EXPLICIT_VALIDATORS
            |OptionFlags::IMPLICIT_VALIDATORS
        );
    }

}