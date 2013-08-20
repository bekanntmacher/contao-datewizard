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
 * Form fields
 */
$GLOBALS['BE_FFL']['dateWizard'] = 'DateWizard';

// Add css and js
if(TL_MODE == 'BE')
{
    $GLOBALS['TL_CSS'][] = 'system/modules/datewizard/assets/datewizard.css';
    $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/datewizard/assets/datewizard.js';
}