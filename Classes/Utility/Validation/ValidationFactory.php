<?php
namespace CosmoCode\SimpleForm\Utility\Validation;

    /***************************************************************
     *  Copyright notice
     *
     *  (c) 2013 Markus Baumann <baumann@cosmocode.de>
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
class ValidationFactory implements \TYPO3\CMS\Core\SingletonInterface {

    /**
     * @var string
     */
    private $validationCode;

    /**
     * @var \CosmoCode\SimpleForm\Utility\Validation\AbstractValidation
     */
    private $validation;

    /**
     * define the type of validation
     */
    private function defineValidationType() {
        switch($this->validationCode) {
            case \CosmoCode\SimpleForm\Utility\Validation\IsNotEmptyValidation::VALIDATION_CODE:
                $this->validation = new IsNotEmptyValidation();
                break;
            case \CosmoCode\SimpleForm\Utility\Validation\IsAlphanumericValidation::VALIDATION_CODE:
                $this->validation = new IsAlphanumericValidation();
                break;
			case \CosmoCode\SimpleForm\Utility\Validation\IsNumericValidation::VALIDATION_CODE:
				$this->validation = new IsNumericValidation();
				break;
			case \CosmoCode\SimpleForm\Utility\Validation\IsEmailValidation::VALIDATION_CODE:
				$this->validation = new IsEmailValidation();
				break;
			default:
				try {
					$validation = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($this->validationCode);
					if(is_a($validation, '\CosmoCode\SimpleForm\Utility\Validation\AbstractValidation')) {
						$this->validation = $validation;
					}
				} catch(\TYPO3\CMS\Core\FormProtection\Exception $e) {
					//TODO:logging or similar
				}
				break;
        }
    }

    /**
     * @param \CosmoCode\SimpleForm\Utility\Validation\AbstractValidation $validation
     */
    public function setValidation($validation) {
        $this->validation = $validation;
    }

    /**
     * @return \CosmoCode\SimpleForm\Utility\Validation\AbstractValidation
     */
    public function getValidation() {
        return $this->validation;
    }

    /**
     * @param string $validationCode
     */
    public function setValidationCode($validationCode) {
        $this->validationCode = $validationCode;
        $this->defineValidationType();
    }

    /**
     * @return string
     */
    public function getValidationCode() {
        return $this->validationCode;
    }

}
?>