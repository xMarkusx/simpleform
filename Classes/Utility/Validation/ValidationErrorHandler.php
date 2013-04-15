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
class ValidationErrorHandler implements \TYPO3\CMS\Core\SingletonInterface {

    /**
     * @var \TYPO3\SimpleForm\Utility\Form\StepHandler
     * @inject
     */
    private $stepHandler;

    /**
     * @var array
     */
    private $validationErrors;

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
     * @param \TYPO3\SimpleForm\Utility\Validation\ValidationError $validationError
     */
    public function addValidationError($validationError) {
        $this->validationErrors[$this->stepHandler->getCurrentStep()][$validationError->getFormField()][] = $validationError;
    }

    /**
     * @return mixed
     */
    public function getValidationErrorsFromCurrentStep() {
        return $this->validationErrors[$this->stepHandler->getCurrentStep()];
    }

    /**
     * @return bool
     */
    public function validationErrorsExists() {
        if(empty($this->validationErrors)) {
            return false;
        }
        return true;
    }
}
?>