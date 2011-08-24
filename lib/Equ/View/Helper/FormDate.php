<?php
namespace Equ\View\Helper;

class FormDate extends \Zend_View_Helper_FormElement {

  public function formDate($name, $value = null, $attribs = null) {
    // separate value into day, month and year
    $day = '';
    $month = '';
    $year = '';
    if (is_array($value)) {
      $day = $value['day'];
      $month = $value['month'];
      $year = $value['year'];
    } elseif (strtotime($value)) {
      list($year, $month, $day) = explode('-', date('Y-m-d', strtotime($value)));
    }

    // build select options
    $dayAttribs = isset($attribs['dayAttribs']) ? $attribs['dayAttribs'] : array();
    $monthAttribs = isset($attribs['monthAttribs']) ? $attribs['monthAttribs'] : array();
    $yearAttribs = isset($attribs['yearAttribs']) ? $attribs['yearAttribs'] : array();
    
    if (isset($attribs['class'])) {
      $dayAttribs['class'] = $attribs['class'];
    }
    if (isset($attribs['class'])) {
      $monthAttribs['class'] = $attribs['class'];
    }
    if (isset($attribs['class'])) {
      $yearAttribs['class'] = $attribs['class'];
    }

    $dayMultiOptions = array('' => '');
    for ($i = 1; $i < 32; $i++) {
      $index = str_pad($i, 2, '0', STR_PAD_LEFT);
      $dayMultiOptions[$index] = str_pad($i, 2, '0', STR_PAD_LEFT);
    }
    $monthMultiOptions = array('' => '');
    $months = \Zend_Locale::getTranslationList('Months');
    foreach ($months['format']['abbreviated'] as $key => $value) {
      $index = str_pad($key, 2, '0', STR_PAD_LEFT);
      $monthMultiOptions[$index] = $value;
    } 

    $startYear = 1910;
    if (isset($attribs['startYear'])) {
      $startYear = $attribs['startYear'];
      unset($attribs['startYear']);
    }

    $stopYear = date('Y');
    if (isset($attribs['stopYear'])) {
      $stopYear = $attribs['stopYear'];
      unset($attribs['stopYear']);
    }

    $yearMultiOptions = array('' => '');

    if ($stopYear < $startYear) {
      for ($i = $startYear; $i >= $stopYear; $i--) {
        $yearMultiOptions[$i] = $i;
      }
    } else {
      for ($i = $startYear; $i <= $stopYear; $i++) {
        $yearMultiOptions[$i] = $i;
      }
    }

    // return the 3 selects separated by &nbsp;
    return
      $this->view->formSelect(
        $name . '[day]', $day, $dayAttribs, $dayMultiOptions) .
      $this->view->formSelect(
        $name . '[month]', $month, $monthAttribs, $monthMultiOptions) .
      $this->view->formSelect(
        $name . '[year]', $year, $yearAttribs, $yearMultiOptions
    );
  }

}