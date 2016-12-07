<?php
namespace CosmoCode\SimpleForm\Utility\Validation;

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
 *
 *
 * @package simple_form
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class LessThanValidation extends AbstractValidation {

    const VALIDATION_CODE = 'less_than';

    /**
     * @param mixed $value
     * @return bool
     */
    public function checkValue($value) {
        $this->value = $value;
        return $this->validate();
    }

    /**
     * @return boolean
     */
    protected function validate() {
        return ($this->value < intval($this->conf['value']));
    }

    /**
     * @return string
     */
    public function getValidationCode() {
        return self::VALIDATION_CODE;
    }
}
?>