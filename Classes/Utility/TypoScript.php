<?php
namespace CosmoCode\SimpleForm\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Utility providing TypoScript-related static helper functions.
 *
 * @package simple_form
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class TypoScript {

    /**
     * @var \TYPO3\CMS\Extbase\Service\TypoScriptService
     */
    static protected $typoScriptService;

    /**
     * Processes TypoScript configuration in given input array recursively.
     *
     * @param $arr mixed The input value (usually an array) to parse and process. Must be a plain array
     *                   (e.g. returned by TypoScriptService::convertTypoScriptArrayToPlainArray().
     */
    static public function processTypoScriptSetupRecursive(&$arr) {
        if (is_array($arr)) {
            if (isset($arr['_typoScriptNodeValue'])) {
                $typoScriptArray = self::getTypoScriptService()->convertPlainArrayToTypoScriptArray($arr);
                $arr = self::getContentObject()->cObjGetSingle($arr['_typoScriptNodeValue'], $typoScriptArray);
            } else {
                foreach ($arr as &$val) {
                    self::processTypoScriptSetupRecursive($val);
                }
            }
        }
    }

    /**
     * @return \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
     */
    static protected function getContentObject() {
        return $GLOBALS['TSFE']->cObj;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Service\TypoScriptService
     */
    static protected function getTypoScriptService() {
        if (!self::$typoScriptService) {
            self::$typoScriptService = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Service\\TypoScriptService');
        }

        return self::$typoScriptService;
    }
}
