<?php
namespace CosmoCode\SimpleForm\Utility\Form;

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
class FormDataHandler implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * @var array
     */
    private $gpData;

    /**
     * @var boolean
     */
    private $formDataIsManipulated = false;

    /**
     * @var \CosmoCode\SimpleForm\Utility\Form\StepHandler
     * @inject
     */
    private $stepHandler;

    /**
     * @var string
     */
    private $formPrefix;

    /**
     * @var \CosmoCode\SimpleForm\Utility\Session\SessionDataHandler
     * @inject
     */
    protected $sessionDataHandler;

    /**
     * @param boolean $formDataIsManipulated
     */
    public function setFormDataIsManipulated($formDataIsManipulated)
    {
        $this->formDataIsManipulated = $formDataIsManipulated;
    }

    /**
     * @return boolean
     */
    public function getFormDataIsManipulated()
    {
        return $this->formDataIsManipulated;
    }

    /**
     * @param string $formPrefix
     */
    public function setFormPrefix($formPrefix)
    {
        $this->formPrefix = $formPrefix;
    }

    /**
     * @return string
     */
    public function getFormPrefix()
    {
        return $this->formPrefix;
    }

    /**
     * @param array $gpData
     */
    public function setGpData($gpData)
    {
        $this->gpData = $gpData;
    }

    /**
     * @return array
     */
    public function getGpData()
    {
        return $this->gpData;
    }

    /**
     * @param string $arrayKey
     *
     * @return mixed
     */
    public function getFormValue($arrayKey)
    {
        return $this->gpData[$this->formPrefix][$this->stepHandler->getCurrentStep()][$arrayKey];
    }

    /**
     * @param $arrayKey
     * @param $formValue
     */
    public function setFormValue($arrayKey, $formValue)
    {
        $this->gpData[$this->formPrefix][$this->stepHandler->getCurrentStep()][$arrayKey] = $formValue;
        $this->formDataIsManipulated = true;
        $sessionDataFromCurrentStep = $this->sessionDataHandler->getFormDataFromCurrentStep();
        $sessionDataFromCurrentStep[$arrayKey] = $formValue;
        $this->sessionDataHandler->storeFormDataFromCurrentStep($sessionDataFromCurrentStep);
    }

    /**
     * @param string $arrayKey
     * @param string $step
     *
     * @return mixed
     */
    public function getFormValueAtStep($arrayKey, $step)
    {
        if ($this->stepHandler->checkIfStepIsValid($step)) {
            $stepData = $this->sessionDataHandler->getFormDataFromStep($step);
            return $stepData[$arrayKey];
        }
        return null;
    }

    /**
     * @param string $arrayKey
     * @param mixed $formValue
     * @param string $step
     */
    public function setFormValueAtStep($arrayKey, $formValue, $step)
    {
        if ($this->stepHandler->checkIfStepIsValid($step)) {
            $stepData = $this->sessionDataHandler->getFormDataFromStep($step);
            $stepData[$arrayKey] = $formValue;
            $this->formDataIsManipulated = true;
            $this->sessionDataHandler->storeFormDataFromStep($stepData, $step);
        }
    }

    /**
     * @return bool
     */
    public function formDataExists()
    {
        if (!empty($this->gpData[$this->formPrefix])) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getFormDataFromCurrentStep()
    {
        return $this->gpData[$this->formPrefix][$this->stepHandler->getCurrentStep()];
    }
}
