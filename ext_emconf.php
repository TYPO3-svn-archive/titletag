<?php

########################################################################
# Extension Manager/Repository config file for ext "titletag".
#
# Auto generated 03-02-2013 22:16
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Title Tag Configuration',
	'description' => 'Manages the configurable generation of the html tag "title"',
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
	'version' => '0.1.1',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.5-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:10:{s:9:"Changelog";s:4:"4d68";s:12:"ext_icon.gif";s:4:"e922";s:17:"ext_localconf.php";s:4:"4fe6";s:14:"ext_tables.php";s:4:"417d";s:35:"Classes/Gateway/TitletagGateway.php";s:4:"b305";s:35:"Classes/Utility/TitletagUtility.php";s:4:"d076";s:37:"Classes/Utility/TitletagUtilityV4.php";s:4:"be06";s:38:"Configuration/TypoScript/constants.txt";s:4:"520b";s:34:"Configuration/TypoScript/setup.txt";s:4:"2f40";s:14:"doc/manual.sxw";s:4:"940b";}',
	'suggests' => array(
	),
);

?>