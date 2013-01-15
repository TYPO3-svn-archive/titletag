<?php
/**
 * Copyright notice
 *
 * (c) 2012 Agentur am Wasser | Maeder & Partner AG
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
 * @copyright  Copyright (c) 2012 Agentur am Wasser | Maeder & Partner AG {@link http://www.agenturamwasser.ch}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @category   TYPO3
 * @package    titletag
 * @version    $Id$
 */

if (!defined ('TYPO3_MODE')) {
     die ('Access denied.');
}

/** @see t3lib_Singleton */
require_once PATH_t3lib . 'interfaces/interface.t3lib_singleton.php';

/**
 * Main plugin
 *
 * @author     Agentur am Wasser | Maeder & Partner AG <development@agenturamwasser.ch>
 * @package    TYPO3
 * @subpackage titletag
 */
class tx_titletag implements t3lib_Singleton
{
    /**
     * @var array
     */
    protected $_conf = array();

    /**
     *
     * @var boolean
     */
    protected $_enable = false;

    /**
     *
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
     * 'pObj' => &$this, 'cache_pages_row' => &$row
     */
    public function pageLoadedFromCache(&$params, $pObj)
    {
        $tsfeConfig = (array)unserialize($params['cache_pages_row']['cache_data']);
        $this->_enable = (TYPO3_MODE == 'FE' && (bool) $tsfeConfig['config']['tx_titletag_enable']);

        if(!$this->_enable) {
            return;
        }

        $this->_conf = $tsfeConfig['tx_titletag_config'];

        $this->_configToStack();
        return;
    }

    public function tsfeSaveCache(&$params, $pObj)
    {
        $params['pObj']->config['tx_titletag_config'] = $this->_conf;
    }

    protected function _configToStack()
    {
        /** @see $matchObj t3lib_matchCondition_frontend */
        $matchCondition = t3lib_div::makeInstance('t3lib_matchCondition_frontend');
        $matchCondition instanceof t3lib_matchCondition_frontend;
//         $matchCondition->setSimulateMatchConditions(array());
//         $matchCondition->setSimulateMatchResult(false);

        foreach($this->_conf['partConf.'] as $partName => $partConf) {
            if(!array_key_exists('condition', $partConf)
              || $matchCondition->match($partConf['condition'])) {
                self::getStack()->push(rtrim($partName, '.'), $partConf);
            }
        }

        return;
    }


    /**
     *
     * @see t3lib_PageRenderer::render()
     * @param array $params
     * @param t3lib_PageRenderer $pObj
     * @return void
     */
    public function renderTitle(&$params, /*t3lib_PageRenderer*/ $pObj)
    {
        $this->_init();

        if(!$this->_enable) {
            return;
        }

        $title = $GLOBALS['TSFE']->cObj->stdWrap($this->_conf['forceTitle'], $this->_conf['forceTitle.']);
        if(!$title) {
            // create the first part from the default title
            $parts = array($this->_getDefaultTitle());

            // load typoscript config into the stack
            $this->_configToStack();

            // create parts from the stack
            $parts += $this->_renderTitleParts();

            // create the title
            $title = $this->_concatenateTitleParts($parts);
        }

        $params['title'] = $title;

        return;
    }

    /**
     *
     * @param array $parts
     * @return string
     */
    protected function _concatenateTitleParts(array $parts)
    {
        // create separator
        if(version_compare(TYPO3_version, '4.7.0', '>=')
          && isset($GLOBALS['TSFE']->tmpl->setup['config.']['pageTitleSeparator'])
          && $GLOBALS['TSFE']->tmpl->setup['config.']['pageTitleSeparator']) {
            $separator = $GLOBALS['TSFE']->tmpl->setup['config.']['pageTitleSeparator'];
        } else {
            $separator = $GLOBALS['TSFE']->cObj->stdWrap($this->_conf['pageTitleSeparator'], $this->_conf['pageTitleSeparator.']);
            if(!$separator) {
                $separator = ':';
            }
        }

        return implode($separator . ' ', $parts);
    }

    /**
     * Renders the title parts
     *
     * @todo improve ignore config
     * @return array
     */
    protected function _renderTitleParts()
    {
        $parts = array();
        // arrange the stack in the right order
        foreach(self::getStack() as $partName => $partConf) {
            // remove the part from the stack
            self::getStack()->offsetUnset($partName);

            // ignore condition
            if(array_key_exists('ignoreOnMatch', $partConf)) {
                if(array_key_exists($partConf['ignoreOnMatch'], $parts)
                  || self::getStack()->offsetExists($partConf['ignoreOnMatch'])) {
                    continue;
                }
            }
            switch($partConf['triggers']) {
                case 'userFunc' :
                    $params = array('pObj' => &$this);
                    $parts[$partName] = t3lib_div::callUserFunction($partConf['userFunc'], $params, $this);
                    break;
                case 'content' :
                    $parts[$partName] = $GLOBALS['TSFE']->cObj->cObjGetSingle($partConf['content'], $partConf['content.']);
                    break;
                case 'text' :
                    $parts[$partName] = $partConf['content'];
                    break;
                default :
                    throw new InvalidArgumentException('Illegal trigger "' . $partConf['triggers'] . '" in plugin.tx_titletag.partConf.' . $partName . '.triggers');
                    break;
            }

            // remember any intInc scripts
            if(strpos($parts[$partName], '<!--INT_SCRIPT.') !== false) {
                $this->_substituteIntInc[] = $parts[$partName];
            }
        }

        return $parts;
    }

    /**
     *
     * @see tslib_fe::processOutput()
     * @param array $params
     * @param object $pObj
     */
    public function modifyTitle(&$params, &$pObj)
    {
        if(!$this->_enable) {
            return;
        }

        if(!count(self::getStack())) {
            return;
        }

        $noTitleFound = (preg_match('/<title>(.*?)<\/title>/i', $pObj->content, $matches) != 1);

        // create the first part from the current title
        $parts = array($matches[1]);

        // add the parts from the stack
        $addParts = $this->_renderTitleParts();
        array_walk($addParts, 'htmlspecialchars');
        $parts += $addParts;

        // create the title
        $title = $this->_concatenateTitleParts($parts);

        // replace the title in the HTML document
        $pObj->content = preg_replace('/<title>(.*?)<\/title>/', '<title>' . $title . ' ' .date('d.m.Y H:i:s', time()) . '</title>', $pObj->content, 1);

        return;
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
        if(!$this->_enable) {
            return;
        }

        foreach($this->_substituteIntInc as $substitute) {
            $pObj->content = str_replace(htmlspecialchars($substitute), $substitute, $pObj->content);
        }
        return;
    }

    /**
     * Creates the base title value
     *
     * @see TSPagegen::renderContentWithHeader()
     * @see t3lib_TStemplate::printTitle()
     * @return string
     */
    protected function _getDefaultTitle()
    {
        // overriding pagetitle
        $pageTitle = $GLOBALS['TSFE']->cObj->stdWrap($this->_conf['overridePagetitle'], $this->_conf['overridePagetitle.']);
        if(!$pageTitle) {
            $pageTitle = $GLOBALS['TSFE']->altPageTitle
                ? $GLOBALS['TSFE']->altPageTitle
                : $GLOBALS['TSFE']->page['title'];
        }

        $separator = $GLOBALS['TSFE']->cObj->stdWrap($this->_conf['pageTitleSeparator'], $this->_conf['pageTitleSeparator.']);

        if($separator && version_compare(TYPO3_version, '4.7.0', '<')) {
            // "back-port" the function from TYPO3 4.7
            $siteTitle = trim($GLOBALS['TSFE']->tmpl->setup['sitetitle']);
            $pageTitle = $GLOBALS['TSFE']->config['config']['noPageTitle'] ? '' : $pageTitle;

            if($GLOBALS['TSFE']->config['config']['pageTitleFirst']) {
                $temp = $siteTitle;
                $siteTitle = $pageTitle;
                $pageTitle = $temp;
            }

            if ($pageTitle != '' && $siteTitle != '') {
                $title = $siteTitle . $separator . ' ' . $pageTitle;
            } else {
                $title = $siteTitle . $pageTitle;
            }

        } else {
            // let t3lib_TStemplate::printTitle() gernerate the title 'as usual'
            $title = $GLOBALS['TSFE']->tmpl->printTitle(
                $pageTitle,
                $GLOBALS['TSFE']->config['config']['noPageTitle'],
                $GLOBALS['TSFE']->config['config']['pageTitleFirst']);
        }

        if ($GLOBALS['TSFE']->config['config']['titleTagFunction']) {
            $title = $GLOBALS['TSFE']->cObj->callUserFunction($GLOBALS['TSFE']->config['config']['titleTagFunction'], array(), $title);
        }

        return $title;
    }

    /**
     * Titletag API: returns the stack
     *
     * @return tx_titletag_stack
     */
    public static function getStack()
    {
        require_once t3lib_extMgm::extPath('titletag') . 'class.tx_titletag_stack.php';
        return tx_titletag_stack::getInstance();
    }
}

class tx_titletag_test
{
    public function main($content, $conf)
    {
        return __METHOD__ . ' | ' . date('d.m.Y H:i:s', time());
        //print '<h3>' . __METHOD__ . ' | ' . date('d.m.Y H:i:s', time()) . '</h3><pre>' . tx_zendmvc_backtrace(true) . '</pre>';
    }
}

?>