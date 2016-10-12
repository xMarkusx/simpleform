<?php
namespace CosmoCode\SimpleForm\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Get data from simple form session
 *
 * While accessing values from single step forms is possible with GP:foo|bar,
 * it is not possible from multi step forms.
 * This hook allows accessing the form data which simple form saves in a session.
 *
 * @author Tilo Baller <baller@cosmocode.de>
 */
class GetData implements \TYPO3\CMS\Frontend\ContentObject\ContentObjectGetDataHookInterface {

    /**
     * Extends the getData()-Method of \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer to process more/other commands
     *
     * @param string $getDataString Full content of getData-request e.g. "TSFE:id // field:title // field:uid
     * @param array $fields Current field-array
     * @param string $sectionValue Currently examined section value of the getData request e.g. "field:title
     * @param string $returnValue Current returnValue that was processed so far by getData
     * @param \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $parentObject Parent content object
     * @return string Get data result
     */
    public function getDataExtension(
        $getDataString,
        array $fields,
        $sectionValue,
        $returnValue,
        \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer &$parentObject
    ) {
        $parts = explode(':', $sectionValue, 2);
        $type = strtolower(trim($parts[0]));
        $key = trim($parts[1]);

        if (isset($type) && isset($key) && $type === 'simpleform') {
            /** @var \CosmoCode\SimpleForm\Utility\Session\SessionDataHandler $simpleFormSessionDataHandler */
            $simpleFormSessionDataHandler = GeneralUtility::makeInstance('CosmoCode\\SimpleForm\\Utility\\Session\\SessionDataHandler');

            $simpleFormSessionData = $simpleFormSessionDataHandler->getFormData();
            $returnValue = $parentObject->getGlobal($key, $simpleFormSessionData);
        }

        return $returnValue;
    }
}
