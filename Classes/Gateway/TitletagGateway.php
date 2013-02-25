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

/**
 * Titletag gateway class
 *
 * Used for backwards compatibility
 *
 * @author     Agentur am Wasser | Maeder & Partner AG <development@agenturamwasser.ch>
 * @package    TYPO3
 * @subpackage titletag
 */
class tx_titletag_TitletagGateway
{
    /**
     * Calls the right utility class to handle the title rendering
     *
     * @param string $titleTagContent
     * @return string
     */
    public function renderTitle($titleTagContent)
    {
        return $this->_getUtilityInstance()->renderTitle($titleTagContent);
    }

    public function substituteIntInc(&$params, &$pObj)
    {
        return $this->_getUtilityInstance()->substituteIntInc($params, $pObj);
    }

    /**
     * Returns the correct utility instance
     *
     * @return Aaw\Titletag\Utility\TitletagUtility | tx_titletag_TitletagUtilityV4
     */
    protected function _getUtilityInstance()
    {
        if(version_compare(TYPO3_version, '6.0.0', '<')) {
            require_once t3lib_extmgm::extPath('titletag') . 'Classes/Utility/TitletagUtilityV4.php';
            return \t3lib_div::makeInstance('tx_titletag_TitletagUtilityV4');
        } else  {
            require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('titletag') . 'Classes/Utility/TitletagUtilityV4.php';
            return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Aaw\Titletag\Utility\TitletagUtility');
        }
    }
}