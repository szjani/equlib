<?php
namespace Equ\Form;

interface IMappedType
{

    /**
      * @param IBuilder $builder
      */
    public function buildForm(IBuilder $builder);

    /**
      * @return string
      */
    public function getObjectClass();

}