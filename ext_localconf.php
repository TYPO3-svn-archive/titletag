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

if(\version_compare(TYPO3_version, '4.7.0', '<')) {
    // TYPO3 4.5 / 4.6
    t3lib_extMgm::addTypoScriptSetup('includeLibs.tx_titletag = EXT:titletag/Classes/Utility/TitletagUtilityV4.php' . CRLF .
                                     'config.titleTagFunction = Tx_Titletag_Utility_TitletagUtilityV4->renderTitle');
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all']['tx_titletag'] = 'EXT:titletag/Classes/Utility/TitletagUtilityV4.php:Tx_Titletag_Utility_TitletagUtilityV4->substituteIntInc';

} elseif(\version_compare(TYPO3_version, '6.0.0', '<')) {
    // TYPO3 4.7
    t3lib_extMgm::addTypoScriptSetup('config.titleTagFunction = Tx_Titletag_Utility_TitletagUtilityV4->renderTitle');
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all']['tx_titletag'] = 'Tx_Titletag_Utility_TitletagUtilityV4->substituteIntInc';
} else  {
    // TYPO3 6.x
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('config.titleTagFunction = Aaw\\Titletag\\Utility\\TitletagUtility->renderTitle');
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all']['tx_titletag'] = 'Aaw\\Titletag\\Utility\\TitletagUtility->substituteIntInc';
}

?>