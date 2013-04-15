<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'TYPO3.' . $_EXTKEY,
	'Simpleform',
	array(
		'Form' => 'displayForm',
		
	),
	// non-cacheable actions
	array(
		'Form' => 'displayForm',
		
	)
);

?>