<?php

########################################################################
# Extension Manager/Repository config file for ext "titletag".
#
# Auto generated 25-02-2013 13:32
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Title Tag Configuration',
	'description' => 'Manages the configurable generation of the html tag "title". Fully compatible with TYPO3 4.5 - 6.x',
	'category' => 'fe',
	'author' => 'Agentur am Wasser | Maeder & Partner AG',
	'author_email' => 'development@agenturamwasser.ch',
	'shy' => '',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author_company' => 'Agentur am Wasser | Maeder & Partner AG',
	'version' => '0.2.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.5-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:10:{s:9:"Changelog";s:4:"1516";s:12:"ext_icon.gif";s:4:"e922";s:17:"ext_localconf.php";s:4:"4dcf";s:14:"ext_tables.php";s:4:"417d";s:35:"Classes/Gateway/TitletagGateway.php";s:4:"0b27";s:35:"Classes/Utility/TitletagUtility.php";s:4:"3530";s:37:"Classes/Utility/TitletagUtilityV4.php";s:4:"f54d";s:38:"Configuration/TypoScript/constants.txt";s:4:"520b";s:34:"Configuration/TypoScript/setup.txt";s:4:"2f40";s:14:"doc/manual.sxw";s:4:"3579";}',
	'suggests' => array(
	),
);

?>