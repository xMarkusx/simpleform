<?php
namespace TYPO3\SimpleForm\Utility\Validation;

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
     * @var \TYPO3\SimpleForm\Utility\Validation\AbstractValidation
     */
    private $validation;

    /**
     * @var string
     */
    private $formFieldName;

    /**
     * @var array
     */
    private $validationConfiguration;

    /**
     * @var array
     */
    private $postData;

    /**
     * @var array
     */
    private $validationErrors;

    /**
     * @var \TYPO3\SimpleForm\Utility\Validation\ValidationFactory
     * @inject
     */
    private $validationFactory;

    /**
     * check form-values against typoscript validation configuration
     */
    public function checkFormValues() {
        foreach($this->validationConfiguration as $formFieldName => $formField) {
            $this->formFieldName = $formFieldName;

            foreach($formField as $validationCode) {
                $this->validationFactory->setValidationCode($validationCode);
                $this->validation = $this->validationFactory->getValidation();
                $this->checkValidation();
            }
        }
    }

    /**
     * check current validation
     */
    private function checkValidation() {
        if(!$this->validation->checkValue($this->postData[$this->formFieldName])) {
            $this->addValidationError();
        }
    }

    /**
     * add validation error to validation error array
     */
    private function addValidationError() {
        $validationError = new ValidationError();
        $validationError->setValidation($this->validation);
        $validationError->setValue($this->postData[$this->formFieldName]);
        $this->validationErrors[] = $validationError;
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
     * @param array $postData
     */
    public function setPostData($postData) {
        $this->postData = $postData;
    }

    /**
     * @return array
     */
    public function getPostData() {
        return $this->postData;
    }

    /**
     * @param \TYPO3\SimpleForm\Utility\Validation\AbstractValidation $validation
     */
    public function setValidation($validation) {
        $this->validation = $validation;
    }

    /**
     * @return \TYPO3\SimpleForm\Utility\Validation\AbstractValidation
     */
    public function getValidation() {
        return $this->validation;
    }

    /**
     * @param array $validationConfiguration
     */
    public function setValidationConfiguration($validationConfiguration) {
        $this->validationConfiguration = $validationConfiguration;
    }

    /**
     * @return array
     */
    public function getValidationConfiguration() {
        return $this->validationConfiguration;
    }

    /**
     * @param array $validationErrors
     */
    public function setValidationErrors($validationErrors) {
        $this->validationErrors = $validationErrors;
    }

    /**
     * @return array
     */
    public function getValidationErrors() {
        return $this->validationErrors;
    }

    /**
     * @param \TYPO3\SimpleForm\Utility\Validation\ValidationFactory $validationFactory
     */
    public function setValidationFactory($validationFactory) {
        $this->validationFactory = $validationFactory;
    }

    /**
     * @return \TYPO3\SimpleForm\Utility\Validation\ValidationFactory
     */
    public function getValidationFactory() {
        return $this->validationFactory;
    }


}
?>