<?php
namespace CosmoCode\SimpleForm\Interceptor;

    /***************************************************************
     *  Copyright notice
     *
     *  (c) 2016 Santiago Núñez Negrillo <s.nuneznegrillo@gmail.com>
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
 * Class CombineInterceptor
 * @package CosmoCode\SimpleForm\Interceptor
 *
 */
class CombineInterceptor extends \CosmoCode\SimpleForm\Interceptor\AbstractInterceptor {

    /**
     * Parameters:
     * - combinedFieldName: Variable name where the combined result will be stored
     * - values: array with values to be combined
     *      - text: string
     *      - field: form field
     *
     * Example:
     *
     * interceptors {
     *   1 {
     *       interceptor = CosmoCode\SimpleForm\Interceptor\CombineInterceptor
     *       conf {
     *          combinedFieldName = fooBarVariable
     *          values {
     *              1.field = fooField
     *              2.text = bar
     *          }
     *      }
     *   }
     * }
     *
     */
    public function intercept() {
        $typoScript = $this->getInterceptorConfiguration();

        $combinedString = '';
        $combinedFieldName = $typoScript['combinedFieldName'];
        $values = $typoScript['values'];

        // If combinedField name is empty nothing will be done
        if (! $combinedFieldName) {
            return false;
        }

        foreach ($values as $value) {
            if (! empty($value['text'])) {
                $combinedString .= $value['text'];
            } elseif (! empty($value['field'])) {
                $fieldText = $this->formDataHandler->getFormValue($value['field']);

                if ($fieldText) {
                    $combinedString .= $fieldText;
                }
            }
        }

        $this->formDataHandler->setFormValue($combinedFieldName, $combinedString);
    }
}
