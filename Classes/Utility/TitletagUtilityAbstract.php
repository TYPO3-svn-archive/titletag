<?php
namespace Aaw\Titletag\Utility;
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
 * Abstract titletag utility class
 *
 * @author     Agentur am Wasser | Maeder & Partner AG <development@agenturamwasser.ch>
 * @package    TYPO3
 * @subpackage titletag
 */
abstract class TitletagUtilityAbstract
{
    /**
     * @var boolean
     */
    protected $_enable = false;

    /**
     * @var array
     */
    protected $_conf = array();

    /**
     * @var string
     */
    protected $_separator = null;

    /**
     * @var array
     */
    protected $_substituteIntInc = array();

    /**
     * Initializer
     *
     * @return void
     */
    protected function _init()
    {
        // enable extension
        $this->_enable = (TYPO3_MODE == 'FE' && (bool) $GLOBALS['TSFE']->config['config']['tx_titletag_enable']);

        if($this->_enable) {
            // save configuration
            $this->_conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_titletag.'];
        }
    }

    /**
     * Renders the page title
     *
     * @param string $titleTagContent
     * @return string
     */
    public function renderTitle($titleTagContent)
    {
        $this->_init();

        if(!$this->_enable) {
            return $titleTagContent;
        }

        // workarounding bug #28745 (see http://forge.typo3.org/issues/28745)
        $recordRegister = $GLOBALS['TSFE']->recordRegister;
        $GLOBALS['TSFE']->recordRegister = array();

        $title = $GLOBALS['TSFE']->cObj->stdWrap($this->_conf['forceTitle'], $this->_conf['forceTitle.']);
        if(!$title) {
            // create the first part from the default title
            $parts = array($this->_getDefaultTitle());

            // create additional parts from the stack
            //$parts += $this->_getTitleParts();

            // create the title
            $title = $this->_concatenateTitleParts($parts);
        }

        // remember any intInc scripts
        if(\strpos($title, '<!--INT_SCRIPT.') !== false) {
            \preg_match_all('/<!--INT_SCRIPT\.[a-fA-F0-9]{32}-->/', $title, $matches, PREG_PATTERN_ORDER);
            $this->_substituteIntInc = $matches[0];
        }

        // restore recordRegister
        $GLOBALS['TSFE']->recordRegister = $recordRegister;

        return $title;
    }

    /**
     * Creates the base title value
     *
     * @see t3lib_TStemplate::printTitle()
     * @see \TYPO3\CMS\Core\TypoScript\TemplateService::printTitle()
     * @return string
     */
    protected function _getDefaultTitle()
    {
        $siteTitle = trim($GLOBALS['TSFE']->tmpl->setup['sitetitle']) ? $GLOBALS['TSFE']->tmpl->setup['sitetitle'] : '';
        $pageTitle = '';
        $separator = '';

        if(!$GLOBALS['TSFE']->cObj->stdWrap($GLOBALS['TSFE']->config['config']['noPageTitle'], $GLOBALS['TSFE']->config['config']['noPageTitle.'])) {
            // overriding pagetitle
            $pageTitle = $GLOBALS['TSFE']->cObj->stdWrap($this->_conf['overridePagetitle'], $this->_conf['overridePagetitle.']);
            if(!$pageTitle) {
                $pageTitle = $GLOBALS['TSFE']->altPageTitle
                    ? $GLOBALS['TSFE']->altPageTitle
                    : $GLOBALS['TSFE']->page['title'];
            }
        }

        if($GLOBALS['TSFE']->cObj->stdWrap($GLOBALS['TSFE']->config['config']['pageTitleFirst'], $GLOBALS['TSFE']->config['config']['pageTitleFirst.'])) {
            $temp = $siteTitle;
            $siteTitle = $pageTitle;
            $pageTitle = $temp;
        }

		if ($pageTitle != '' && $siteTitle != '') {
            $separator = $this->_getPageTitleSeparator();
		}

		$title = $siteTitle . $separator . $pageTitle;

        return $title;
    }

    /**
     * Returns the pageTitleSeparator
     *
     * @return string
     */
    protected function _getPageTitleSeparator()
    {
        if($this->_separator === null) {
            $separator = $GLOBALS['TSFE']->cObj->stdWrap($GLOBALS['TSFE']->config['config']['pageTitleSeparator'], $GLOBALS['TSFE']->config['config']['pageTitleSeparator.']);
            if(!$separator) {
                // Falling back to TYPO3 default
                $separator = ': ';
            }
            // remember separator
            $this->_separator = $separator;
        }
        return $this->_separator;
    }

    /**
     * Concatenates the title parts array
     *
     * @param array $parts
     * @return string
     */
    protected function _concatenateTitleParts(array $parts)
    {
        $separator = $this->_getPageTitleSeparator();
        return implode($separator . ' ', $parts);
    }

    /**
     * Restores the htmlspecialchared _INT includes
     *
     * @param array $params
     * @param object $pObj
     * @return void
     */
    public function substituteIntInc(&$params, &$pObj)
    {
        if(!$this->_enable || empty($this->_substituteIntInc)) {
            return;
        }

        foreach($this->_substituteIntInc as $substitute) {
            $pObj->content = \str_replace(\htmlspecialchars($substitute), $substitute, $pObj->content);
        }

        return;
    }
}