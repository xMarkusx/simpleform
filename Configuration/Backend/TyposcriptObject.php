<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$typeName = 'typoscript_object';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
    array(
        'LLL:EXT:simple_form/Resources/Private/Language/locallang_db.xlf:typoscript_object',
        $typeName
    ),
    'CType',
    $_EXTKEY
);

$columnArray = array(
    'tx_simple_form_typoscript_object_config' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:simple_form/Resources/Private/Language/locallang_db.xlf:typoscript_object_config',
        'config' => array(
            'type' => 'input',
            'size' => '50',
            'max' => '255',
            'eval' => 'trim',
        )
    )
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', $columnArray, 1);

if (version_compare(TYPO3_version, '6.2.0', '<')) {
    \TYPO3\CMS\Core\Utility\GeneralUtility::loadTCA('tt_content');
}

$TCA['tt_content']['types'][$typeName] = array(
    'showitem' =>
    '--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.general;general,
     header;LLL:EXT:simple_form/Resources/Private/Language/locallang_db.xlf:typoscript_object_path;,tx_simple_form_typoscript_object_config;LLL:EXT:simple_form/Resources/Private/Language/locallang_db.xlf:typoscript_object_config;,
     --div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
            --palette--;LLL:EXT:cms/locallang_ttc.xml:palette.visibility;visibility,
            --palette--;LLL:EXT:cms/locallang_ttc.xml:palette.access;access'
);
