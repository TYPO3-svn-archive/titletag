<?php
/**
 * Copyright notice
 *
 * (c) 2013 Agentur am Wasser | Maeder & Partner AG
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 * **************************************************************
 *
 * @author     Agentur am Wasser | Maeder & Partner AG <development@agenturamwasser.ch>
 * @copyright  Copyright (c) 2013 Agentur am Wasser | Maeder & Partner AG {@link http://www.agenturamwasser.ch}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @category   TYPO3
 * @package    titletag
 * @version    $Id$
 */

if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

// Register hooks
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-postProcess']['tx_titletag'] = 'EXT:titletag/Classes/Utility/TitletagUtility.php:&Aaw\Titletag\Utility\TitletagUtility->renderTitle';
// $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all']['tx_titletag'] = 'EXT:titletag/class.tx_titletag.php:&tx_titletag->substituteIntInc';
// $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output']['tx_titletag'] = 'EXT:titletag/class.tx_titletag.php:&tx_titletag->modifyTitle';
// $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['pageLoadedFromCache']['tx_titletag'] = 'EXT:titletag/class.tx_titletag.php:&tx_titletag->pageLoadedFromCache';
// $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-cached']['tx_titletag'] = 'EXT:titletag/class.tx_titletag.php:&tx_titletag->tsfeSaveCache';
?>