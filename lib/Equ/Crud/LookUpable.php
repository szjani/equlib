<?php
namespace Equ\Crud;

interface LookUpable {
  
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