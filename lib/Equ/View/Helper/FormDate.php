<?php
namespace Equ\View\Helper;

class FormDate extends \Zend_View_Helper_FormElement
{

    public function formDate($name, $value = null, $attribs = null)
    {
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

        if (isset($attribs['class']))
{
            $dayAttribs['class'] = $attribs['class'];
        }
        if (isset($attribs['class']))
{
            $monthAttribs['class'] = $attribs['class'];
        }
        if (isset($attribs['class']))
{
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

        $startYear = date('Y');
        if (isset($attribs['startYear'])) {
            $startYear = $attribs['startYear'];
            unset($attribs['startYear']);
        }

        $stopYear = 1910;
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
        $res = '';
        if (!array_key_exists('format', $attribs)) {
            $res = $this->view->formSelect(
                $name . '[day]', $day, $dayAttribs, $dayMultiOptions) .
            $this->view->formSelect(
                $name . '[month]', $month, $monthAttribs, $monthMultiOptions) .
            $this->view->formSelect(
                $name . '[year]', $year, $yearAttribs, $yearMultiOptions
            );
        } else {
            for ($i = 0; $i < strlen($attribs['format']); $i++) {
                switch ($attribs['format'][$i]) {
                    case 'y':
                        $res .= $this->view->formSelect($name . '[year]', $year, $yearAttribs, $yearMultiOptions);
                        break;
                    case 'm':
                        $res .= $this->view->formSelect($name . '[month]', $month, $monthAttribs, $monthMultiOptions);
                        break;
                    case 'd':
                        $res .= $this->view->formSelect($name . '[day]', $day, $dayAttribs, $dayMultiOptions);
                        break;
                    default:
                        throw new \InvalidArgumentException("Invalid date format: {$attribs['format']}");
                }
            }
        }
        return $res;
    }

}