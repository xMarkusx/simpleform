<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    $_EXTKEY,
    'Simpleform',
    'Simple Form'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Simple Form');

require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('simple_form', 'Configuration/Backend/TyposcriptObject.php'));
