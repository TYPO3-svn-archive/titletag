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

/**
 * Main plugin
 *
 * @author     Agentur am Wasser | Maeder & Partner AG <development@agenturamwasser.ch>
 * @package    TYPO3
 * @subpackage titletag
 */
class tx_titletag
{
    /**
     * @var array
     */
    public $conf = array();

    /**
     * @var tslib_cObj
     */
    public $cObj;

    /**
     * Main plugin method
     *
     * @param string $content
     * @param array $conf
     * @return string
     */
    public function main($content, $conf)
    {
        $this->conf = $conf;

        // allow force-override the whole pagetitle
        $title = trim($this->cObj->cObjGetSingle($conf['forceTitle'], $conf['forceTitle.']));
        if(!$title) {
            $pageTitle = $this->cObj->cObjGetSingle($conf['overridePagetitle'], $conf['overridePagetitle.']);
            if(!$pageTitle) {
                $pageTitle = $GLOBALS['TSFE']->altPageTitle
                    ? $GLOBALS['TSFE']->altPageTitle
                    : $GLOBALS['TSFE']->page['title'];
            }

            $noPageTitle = $GLOBALS['TSFE']->config['config']['noPageTitle'];
            if($noPageTitle == 2 && isset($conf['noPageTitle']) && in_array($conf['noPageTitle'], array(0,1))) {
                $noPageTitle = $conf['noPageTitle'];
            }

            $title = $this->_createBaseTitle($pageTitle, $noPageTitle);

            // look for $_GET params that need 'title-expansion'
            $mmForumParams = t3lib_div::_GET('tx_mmforum_pi1');
            $ttNewsParams = t3lib_div::_GET('tx_ttnews');
            $append = '';

            // mm_forum
            if(array_key_exists('tid', $mmForumParams) && $mmForumParams['tid']) {
                $row = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('topic_title', 'tx_mmforum_topics', 'uid=' . $mmForumParams['tid']);
                $append = $row['topic_title'];
            } elseif(array_key_exists('fid', $mmForumParams) && $mmForumParams['fid']) {
                $row = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('forum_name', 'tx_mmforum_forums', 'uid=' . $mmForumParams['fid']);
                $append = $row['forum_name'];
            } elseif(array_key_exists('tag_uid', $ttNewsParams) && $ttNewsParams['tag_uid']) {
                $row = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('title', 'tx_simpletag_tag', 'uid=' . $ttNewsParams['tag_uid']);
                $append = $row['title'];
            }

            $append = trim($append);
            if(strlen($append)) {
                $pageTitleSeparator = $this->cObj->stdWrap($this->conf['pageTitleSeparator'], $this->conf['pageTitleSeparator.']);
                if($pageTitleSeparator && version_compare(TYPO3_version, '4.7.0', '<')) {
                    $separator = $pageTitleSeparator;
                } elseif(version_compare(TYPO3_version, '4.7.0', '>=')
                  && isset($GLOBALS['TSFE']->config['config']['pageTitleSeparator'])
                  && $GLOBALS['TSFE']->config['config']['pageTitleSeparator']) {
                    $separator = $GLOBALS['TSFE']->config['config']['pageTitleSeparator'];
                } else {
                    $separator = ':';
                }

                $title .= $separator . $append;
            }
        }

        $title = $this->cObj->wrap($title, $conf['wrap']);

        if($conf['debug']) {
            $title .= ' [' . date('d.m.Y H:i:s') . ']';
        }

        if($GLOBALS['TSFE']->config['config']['noPageTitle'] != 2) {
            if(strlen(trim($GLOBALS['TSFE']->content)) > 0) {
                $content = $GLOBALS['TSFE']->content;
                $content = preg_replace('/<title>(.*?)<\/title>/', '<title>' . $title . '</title>', $content, 1);
                $GLOBALS['TSFE']->content = $content;
            } else {
                $GLOBALS['TSFE']->page['title'] = $title;
            }
        }

        if(isset($conf['noReturn']) && $conf['noReturn']) {
            return;
        }

        return '<title>' . $title . '</title>';
    }

    /**
     * Creates the base title value
     *
     * @see TSPagegen::renderContentWithHeader()
     * @see t3lib_TStemplate::printTitle()
     * @param string $pageTitle
     * @param int $noPageTitle
     * @return string
     */
    protected function _createBaseTitle($pageTitle, $noPageTitle)
    {
        $separator = $this->cObj->stdWrap($this->conf['pageTitleSeparator'], $this->conf['pageTitleSeparator.']);
        if($separator && version_compare(TYPO3_version, '4.7.0', '<')) {
            // "back-port" the function from TYPO3 4.7
            $siteTitle = trim($GLOBALS['TSFE']->tmpl->setup['sitetitle']);
            $pageTitle = $noPageTitle ? '' : $pageTitle;

            if($GLOBALS['TSFE']->config['config']['pageTitleFirst']) {
                $temp = $siteTitle;
                $siteTitle = $pageTitle;
                $pageTitle = $temp;
            }

            if ($pageTitle != '' && $siteTitle != '') {
                $title = $siteTitle . $separator . $pageTitle;
            } else {
                $title = $siteTitle . $pageTitle;
            }

        } else {
            // let t3lib_TStemplate::printTitle() gernerate the title 'as usual'
            $title = $GLOBALS['TSFE']->tmpl->printTitle(
                $pageTitle,
                $noPageTitle,
                $GLOBALS['TSFE']->config['config']['pageTitleFirst']);
        }

        if ($GLOBALS['TSFE']->config['config']['titleTagFunction']) {
            $title = $GLOBALS['TSFE']->cObj->callUserFunction($GLOBALS['TSFE']->config['config']['titleTagFunction'], array(), $title);
        }

        return $title;
    }
}

?>