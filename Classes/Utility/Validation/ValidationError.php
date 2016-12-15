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
class ValidationError
{

    /**
     * @var string
     */
    private $formField;

    /**
     * @var string
     */
    private $eachFieldName = '';

    /**
     * @var string
     */
    private $eachIndex = '';

    /**
     * @var mixed
     */
    private $formValue;

    /**
     * @var string
     */
    private $validationCode;

    /**
     * @var string
     */
    protected $customErrorText;

    /**
     * @param mixed $formField
     */
    public function setFormField($formField)
    {
        $this->formField = $formField;
    }

    /**
     * @return mixed
     */
    public function getFormField()
    {
        return $this->formField;
    }

    /**
     * @param mixed $formValue
     */
    public function setFormValue($formValue)
    {
        $this->formValue = $formValue;
    }

    /**
     * @return mixed
     */
    public function getFormValue()
    {
        return $this->formValue;
    }

    /**
     * @param string $validationCode
     */
    public function setValidationCode($validationCode)
    {
        $this->validationCode = $validationCode;
    }

    /**
     * @return string
     */
    public function getValidationCode()
    {
        return $this->validationCode;
    }

    /**
     * @param string $customErrorText
     */
    public function setCustomErrorText($customErrorText)
    {
        $this->customErrorText = $customErrorText;
    }

    /**
     * @return string
     */
    public function getCustomErrorText()
    {
        return $this->customErrorText;
    }

    /**
     * @return string
     */
    public function getEachFieldName()
    {
        return $this->eachFieldName;
    }

    /**
     * @param string $eachFieldName
     */
    public function setEachFieldName($eachFieldName)
    {
        $this->eachFieldName = $eachFieldName;
    }

    /**
     * @return string
     */
    public function getEachIndex()
    {
        return $this->eachIndex;
    }

    /**
     * @param string $eachIndex
     */
    public function setEachIndex($eachIndex)
    {
        $this->eachIndex = $eachIndex;
    }
}
