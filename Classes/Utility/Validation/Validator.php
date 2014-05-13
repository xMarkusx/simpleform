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
class Validator implements \TYPO3\CMS\Core\SingletonInterface {

    /**
     * @var \CosmoCode\SimpleForm\Utility\Validation\AbstractValidation
     */
    private $validation;

    /**
     * @var string
     */
    private $formFieldName;

    /**
     * @var \CosmoCode\SimpleForm\Utility\Validation\ValidationConfigurationHandler
     * @inject
     */
    private $validationConfigurationHandler;

    /**
     * @var \CosmoCode\SimpleForm\Utility\Validation\ValidationErrorHandler
     * @inject
     */
    private $validationErrorHandler;

    /**
     * @var \CosmoCode\SimpleForm\Utility\Form\FormDataHandler
     * @inject
     */
    private $formDataHandler;

    /**
     * @var \CosmoCode\SimpleForm\Utility\Validation\ValidationFactory
     * @inject
     */
    private $validationFactory;

    /**
     * @var bool
     */
    private $deactivateCheck = false;

    /**
     * check form-values against typoscript validation configuration
     */
    public function checkFormValues() {
		$validationConfiguration = $this->validationConfigurationHandler->getValidationConfigurationFromCurrentStep();
        if(!$this->deactivateCheck && $validationConfiguration) {
            foreach($validationConfiguration as $formFieldName => $formField) {
                $this->formFieldName = $formFieldName;

                foreach($formField as $validationCode) {
                    $this->validationFactory->setValidationCode($validationCode);
                    $this->validation = $this->validationFactory->getValidation();
                    $this->checkValidation();
                }
            }
        }
    }

    /**
     * check current validation
     */
    private function checkValidation() {
        if(!$this->validation->checkValue($this->formDataHandler->getFormValue($this->formFieldName))) {
            $this->addValidationError();
        }
    }

    /**
     * add validation error to validation error array
     */
    private function addValidationError() {
        $validationError = new ValidationError();
        $validationError->setValidationCode($this->validation->getValidationCode());
        $validationError->setFormValue($this->formDataHandler->getFormValue($this->formFieldName));
        $validationError->setFormField($this->formFieldName);
        $this->validationErrorHandler->addValidationError($validationError);
    }

    /**
     * @param string $formFieldName
     */
    public function setFormFieldName($formFieldName) {
        $this->formFieldName = $formFieldName;
    }

    /**
     * @return string
     */
    public function getFormFieldName() {
        return $this->formFieldName;
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
     * @param \CosmoCode\SimpleForm\Utility\Validation\ValidationFactory $validationFactory
     */
    public function setValidationFactory($validationFactory) {
        $this->validationFactory = $validationFactory;
    }

    /**
     * @return \CosmoCode\SimpleForm\Utility\Validation\ValidationFactory
     */
    public function getValidationFactory() {
        return $this->validationFactory;
    }

    /**
     * @param boolean $deactivateCheck
     */
    public function setDeactivateCheck($deactivateCheck) {
        $this->deactivateCheck = $deactivateCheck;
    }

    /**
     * @return boolean
     */
    public function getDeactivateCheck() {
        return $this->deactivateCheck;
    }
}
?>
