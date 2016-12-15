<?php
namespace CosmoCode\SimpleForm\Utility\Session;

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
class SessionDataHandler implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * @var \CosmoCode\SimpleForm\Utility\Session\SessionHandler
     * @inject
     */
    private $sessionHandler;

    /**
     * @var \CosmoCode\SimpleForm\Utility\Form\StepHandler
     * @inject
     */
    private $stepHandler;

    /**
     * @param array $formData
     */
    public function storeFormDataFromCurrentStep($formData)
    {
        $currentFormData = $this->sessionHandler->restoreFromSession();
        $currentFormData[$this->stepHandler->getCurrentStep()] = $formData;
        $this->sessionHandler->storeSessionData($currentFormData);
    }

    /**
     * @param array $formData
     * @param string $step
     */
    public function storeFormDataFromStep($formData, $step)
    {
        $currentFormData = $this->sessionHandler->restoreFromSession();
        if ($this->stepHandler->checkIfStepIsValid($step)) {
            $currentFormData[$step] = $formData;
            $this->sessionHandler->storeSessionData($currentFormData);
        }
    }

    /**
     * @return mixed
     */
    public function getFormDataFromCurrentStep()
    {
        $currentFormData = $this->sessionHandler->restoreFromSession();
        return $currentFormData[$this->stepHandler->getCurrentStep()];
    }

    /**
     * @param $step
     * @return mixed
     */
    public function getFormDataFromStep($step)
    {
        $currentFormData = $this->sessionHandler->restoreFromSession();
        return $currentFormData[$step];
    }

    /**
     * @return mixed
     */
    public function getFormData()
    {
        return $this->sessionHandler->restoreFromSession();
    }
}
