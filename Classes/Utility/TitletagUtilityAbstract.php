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

            // as of TYPO3 6.2 the page title generation is triggered again
            // after INTincScript(), so we have to render _INT scripts here
            // use $GLOBALS['TSFE']->content to determine wether this is neccessary
            if (version_compare(TYPO3_version, '6.2', '>=') && !empty($GLOBALS['TSFE']->content)) {

                do {
                    \preg_match_all('/<!--(INT_SCRIPT\.[a-f0-9]{32})-->/', $title, $matches, PREG_SET_ORDER);
                    $intScripts = array();
                    foreach($matches as $match) {
                        if (\is_array($GLOBALS['TSFE']->config['INTincScript'][$match[1]])) {
                            $intScripts[$match[1]] = $GLOBALS['TSFE']->config['INTincScript'][$match[1]];
                        }
                    }

                    $title = $this->_replaceIntScripts($title, $intScripts);
                } while (\strpos($title, '<!--INT_SCRIPT.') !== false);

            } else {
                \preg_match_all('/<!--INT_SCRIPT\.[a-fA-F0-9]{32}-->/', $title, $matches, PREG_PATTERN_ORDER);
                $this->_substituteIntInc = $matches[0];
            }
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
        if (isset($this->_conf['sitetitle_stdWrap.'])) {
            $siteTitle = $GLOBALS['TSFE']->cObj->stdWrap($siteTitle, $this->_conf['sitetitle_stdWrap.']);
        }
        $pageTitle = '';
        $separator = '';

        if(!$GLOBALS['TSFE']->cObj->stdWrap($GLOBALS['TSFE']->config['config']['noPageTitle'], $GLOBALS['TSFE']->config['config']['noPageTitle.'])) {
            // overriding pagetitle
            $pageTitle = $GLOBALS['TSFE']->cObj->stdWrap($this->_conf['overridePagetitle'], $this->_conf['overridePagetitle.']);
            if(!$pageTitle) {
                $pageTitle = $GLOBALS['TSFE']->altPageTitle
                    ? $GLOBALS['TSFE']->altPageTitle
                    : $GLOBALS['TSFE']->page['title'];
                if (isset($this->_conf['pagetitle_stdWrap.'])) {
                    $pageTitle = $GLOBALS['TSFE']->cObj->stdWrap($pageTitle, $this->_conf['pagetitle_stdWrap.']);
                }
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

    /**
     * @param string $title
     * @param array $intScripts
     * @return string
     * @see TyposcriptFrontendController::INTincScript_includeLibs()
     * @see TyposcriptFrontendController::INTincScript_process()
     */
    protected function _replaceIntScripts($title, array $intScripts)
    {
        foreach($intScripts as $intScriptKey => $intConf) {
            // includes
            if (isset($intConf['includeLibs']) && $intConf['includeLibs']) {
                $GLOBALS['TSFE']->includeLibraries($this->_trimExplode(',', $intConf['includeLibs'], true));
            }

            // get the cObj
            $intCobj = unserialize($GLOBALS['TSFE']->config['INTincScript'][$intScriptKey]['cObj']);

            // generate content
            switch ($intConf['type']) {
            	case 'COA' :
            	    $substitute = $intCobj->COBJ_ARRAY($intConf['conf']);
            	    break;
            	case 'FUNC' :
            	    $substitute = $intCobj->USER($intConf['conf']);
            	    break;
            	case 'POSTUSERFUNC' :
            	    $substitute = $intCobj->callUserFunction($intConf['postUserFunc'], $intConf['conf'], $intConf['content']);
            	    break;
            }
            $substitute = $GLOBALS['TSFE']->convOutputCharset($substitute);
            $title = str_replace('<!--' . $intScriptKey . '-->', $substitute, $title);
        }

        return $title;
    }

    /**
     * Proxy to GeneralUtility::trimExplode(), respecting TYPO3_version
     *
     * @param string $delim
     * @param string $string
     * @param boolaen $removeEmptyValues
     * @param integer $limit
     * @return array
     */
    protected function _trimExplode($delim, $string, $removeEmptyValues = FALSE, $limit = 0)
    {
        if (\version_compare(TYPO3_version, '6.0.0', '<')) {
            return \t3lib_div::trimExplode($delim, $string, $removeEmptyValues, $limit);
        }
        return \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode($delim, $string, $removeEmptyValues, $limit);
    }
}