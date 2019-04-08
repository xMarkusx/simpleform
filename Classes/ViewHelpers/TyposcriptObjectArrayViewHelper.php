<?php
namespace CosmoCode\SimpleForm\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Markus Baumann <baumann@cosmocode.de>, CosmoCode GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * returns array from given string of ce: typoscript_object
 */
class TyposcriptObjectArrayViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('string', 'string', '', true);
    }

    /**
     * @return array
     */
    public function render()
    {
        $output = array();
        $items = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $this->arguments['string']);
        foreach ($items as $item) {
            $keyValue = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(':', $item);
            $output[$keyValue[0]] = $keyValue[1];
        }

        return $output;
    }
}
