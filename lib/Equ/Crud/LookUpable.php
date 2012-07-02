<?php
namespace Equ\Crud;

/**
  * @category    Equ
  * @package     Crud
  * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
  * @author      Szurovecz János <szjani@szjani.hu>
  */
interface LookUpable
{

    /**
      * array(
      *   array(
      *     $key => '1',
      *     $value => 'One',
      *   ),
      *   array(
      *     $key => '2',
      *     $value => 'Two',
      *   ),
      * )
      *
      * @param string $search
      * @param string $key
      * @param string $value
      * @return array
      */
    public function findForLookUp($search, $key, $value);

    /**
      * array(
      *   $key => 1,
      *   $value => 'One'
      * )
      *
      * @param string $id
      * @param string $key
      * @param string $value
      * @return array
      */
    public function findOneForLookUp($id, $key, $value);

}