<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package   Datewizard
 * @author    Simon Wohler
 * @license   LGPL
 * @copyright bekanntmacher 2013 http://www.bekanntmacher.ch
 */



/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace Contao;


/**
 * Class DateWizard
 *
 * Provide methods to handle dates.
 */
class DateWizard extends \Widget
{

    /**
     * Submit user input
     * @var boolean
     */
    protected $blnSubmitInput = true;

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'be_widget';



    /**
     * Trim values
     * @param mixed
     * @return mixed
     */

    protected function validator($varInput)
    {
        if (is_array($varInput))
        {
            foreach ($varInput as $k=>$v)
            {
                try
                {
                    $objDate = new \Date($v, $GLOBALS['TL_CONFIG']['dateFormat']);
                }
                catch (\OutOfBoundsException $e)
                {
                    $this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['invalidDate'], $v));
                }

                $varValue = $this->checkDate($v);

                if($objDate)
                {
                    $varInput[$k] = $objDate->tstamp;
                }
                else {
                    $varInput[$k] = $varValue;
                }

            }
        }

        return $varInput;
    }


    protected function checkDate($varInput)
    {
        if (!\Validator::isDate($varInput))
        {
            $this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['date'], \Date::getInputFormat(\Date::getNumericDateFormat())));
        }
        else
        {
            // Validate the date (see #5086)
            try
            {
                new \Date($varInput);
            }
            catch (\OutOfBoundsException $e)
            {
                $this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['invalidDate'], $varInput));
            }
        }

        return $varInput;
    }



    /**
     * Generate the widget and return it as string
     * @return string
     */
    public function generate()
    {
        $arrButtons = array('copy', 'delete');
        $strCommand = 'cmd_' . $this->strField;

        // Change the order
        if (\Input::get($strCommand) && is_numeric(\Input::get('cid')) && \Input::get('id') == $this->currentRecord)
        {
            $this->import('Database');

            switch (\Input::get($strCommand))
            {
                case 'copy':
                    $this->varValue = array_duplicate($this->varValue, \Input::get('cid'));
                    break;

                case 'delete':
                    $this->varValue = array_delete($this->varValue, \Input::get('cid'));
                    break;
            }

            $this->Database->prepare("UPDATE " . $this->strTable . " SET " . $this->strField . "=? WHERE id=?")
                ->execute(serialize($this->varValue), $this->currentRecord);

            $this->redirect(preg_replace('/&(amp;)?cid=[^&]*/i', '', preg_replace('/&(amp;)?' . preg_quote($strCommand, '/') . '=[^&]*/i', '', \Environment::get('request'))));
        }

        // Make sure there is at least an empty array
        if (!is_array($this->varValue) || empty($this->varValue))
        {
            $this->varValue = array('');
        }

        $tabindex = 0;
        $return = '<ul id="ctrl_'.$this->strId.'" class="tl_datewizard">';

        // Add input fields
        for ($i=0; $i<count($this->varValue); $i++)
        {
            $strValue = $this->varValue[$i];

            try
            {
                $objDate = new \Date($this->varValue[$i]);
            }
            catch (\OutOfBoundsException $e) { }

            if($objDate && $strValue != '')
            {
                $strValue = date($GLOBALS['TL_CONFIG']['dateFormat'],$strValue);
            }

            $strCssId = $this->strId . '-' .$i;
            $return .= '<li>';

            $return .= '<input id="ctrl_' . $strCssId . '" type="text" name="' . $this->strId . '[]" class="tl_text" tabindex="' . ++$tabindex . '" value="' . $strValue . '"' . $this->getAttributes() . '>';

            // Date picker
            $format = \Date::formatToJs($GLOBALS['TL_CONFIG']['dateFormat']);

            $return .= '<img src="assets/mootools/datepicker/' . DATEPICKER . '/icon.gif" width="20" height="20" alt="" title="' . specialchars($GLOBALS['TL_LANG']['MSC']['datepicker']) . '" id="toggle_' .  $strCssId . '" style="vertical-align:-6px; cursor:pointer">';
            $return .= '<script>
                            window.addEvent("domready", function() {
                                new Picker.Date($("ctrl_' . $strCssId . '"), {
                                    draggable: false,
                                    toggle: $("toggle_' . $strCssId . '"),
                                    format: "' . $format . '",
                                    positionOffset: {x:-197,y:-182},
                                    pickerClass: "datepicker_dashboard",
                                    useFadeInOut: !Browser.ie,
                                    startDay: ' . $GLOBALS['TL_LANG']['MSC']['weekOffset'] . ',
                                    titleFormat: "' . $GLOBALS['TL_LANG']['MSC']['titleFormat'] . '"
                                });
                            });
                        </script>';

            // Add buttons
            foreach ($arrButtons as $button)
            {
                $return .= '<a
                                href="'.$this->addToUrl('&amp;'.$strCommand.'='.$button.'&amp;cid='.$i.'&amp;id='.$this->currentRecord).'"
                                title="'.specialchars($GLOBALS['TL_LANG']['MSC']['lw_'.$button]).'"
                                onclick="Datewizard.dateWizard(this,\''.$button.'\',\'ctrl_'.$this->strId.'\'); return false"
                            >'.\Image::getHtml($button.'.gif', $GLOBALS['TL_LANG']['MSC']['lw_'.$button], 'class="tl_listwizard_img"').'</a> ';
            }

            $return .= '</li>';
        }

        return $return . '</ul>';
    }
}
