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
class StepHandler implements \TYPO3\CMS\Core\SingletonInterface {

    const GO_TO_NEXT_STEP = 'go_to_next_step';
    const GO_TO_PREVIOUS_STEP = 'go_to_previous_step';

    /**
     * @var array
     */
    private $steps;

    /**
     * @var string
     */
    private $lastStepName;

    /**
     * @var string
     */
    private $firstStepName;

    /**
     * @var string
     */
    private $currentStep;

    /**
     * @var string
     */
    private $direction;

    /**
     * @var \CosmoCode\SimpleForm\Utility\Form\FormDataHandler
     * @inject
     */
    private $fomDataHandler;

    public function initialize() {
        $this->defineCurrentStep();
        $this->defineDirection();
    }

    private function defineCurrentStep() {
        $gpData = $this->fomDataHandler->getGpData();
        if(!empty($gpData['currentStep'])) {
            $this->currentStep = $gpData['currentStep'];
        } else {
            $this->currentStep = $this->firstStepName;
        }
    }

    private function defineDirection() {
        $gpData = $this->fomDataHandler->getGpData();
        $this->direction = self::GO_TO_NEXT_STEP;

        if($gpData['submit'] === 'back') {
            $this->direction = self::GO_TO_PREVIOUS_STEP;
        }
    }

    /**
     * @return string
     */
    public function getNextStep() {
        if($this->currentStep === $this->lastStepName) {
            return $this->currentStep;
        }
        $currentStepKey = array_search($this->currentStep, $this->steps);
        return $this->steps[$currentStepKey + 1];
    }

    /**
     * @return string
     */
    public function getPreviousStep() {
        if($this->currentStep === $this->firstStepName) {
            return $this->currentStep;
        }
        $currentStepKey = array_search($this->currentStep, $this->steps);
        return $this->steps[$currentStepKey - 1];
    }

    /**
     * @param string $currentStep
     */
    public function setCurrentStep($currentStep) {
        $this->currentStep = $currentStep;
    }

    /**
     * @return string
     */
    public function getCurrentStep() {
        return $this->currentStep;
    }

    /**
     * @param string $typoscriptStepConfiguration
     */
    public function setSteps($typoscriptStepConfiguration) {
        $this->steps = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $typoscriptStepConfiguration);
        $this->firstStepName = $this->steps[0];
        $this->lastStepName = $this->steps[count($this->steps) - 1];
    }

    /**
     * @return array
     */
    public function getSteps() {
        return $this->steps;
    }

    /**
     * @param string $direction
     */
    public function setDirection($direction) {
        $this->direction = $direction;
    }

    /**
     * @return string
     */
    public function getDirection() {
        return $this->direction;
    }

    /**
     * @return bool
     */
    public function formIsOnLastStep() {
        if($this->currentStep === $this->lastStepName) {
            return true;
        }
        return false;
    }
}
?>