<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'CosmoCode.' . $_EXTKEY,
	'Simpleform',
	array(
		'Form' => 'displayForm',
		
	),
	// non-cacheable actions
	array(
		'Form' => 'displayForm',
		
	)
);

/**
 * Hook to allow accessing simple_form session data via getText (e.g. foo = TEXT, foo.data = simpleForm:First|email)
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_content.php']['getData'][]
	= 'CosmoCode\SimpleForm\Hooks\GetData';

?>
