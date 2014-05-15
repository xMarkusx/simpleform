<?php
namespace CosmoCode\SimpleForm\Controller;

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
class FormController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

    /**
     * @var \CosmoCode\SimpleForm\Utility\Form\FormDataHandler
     * @inject
     */
    private $formDataHandler;

    /**
     * @var \CosmoCode\SimpleForm\Utility\Form\StepHandler
     * @inject
     */
    private $stepHandler;

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
     * @var \CosmoCode\SimpleForm\Utility\Session\SessionHandler
     * @inject
     */
    private $sessionHandler;

    /**
     * @var \CosmoCode\SimpleForm\Utility\Session\SessionDataHandler
     * @inject
     */
    private $sessionDataHandler;

    /**
     * @var \CosmoCode\SimpleForm\Utility\Validation\Validator
     * @inject
     */
    private $validator;

    /**
     * @var \CosmoCode\SimpleForm\Interceptor\InterceptorHandler
     * @inject
     */
    private $interceptorHandler;

    /**
     * @var \CosmoCode\SimpleForm\Finisher\FinisherHandler
     * @inject
     */
    private $finisherHandler;

	/**
	 * @var \CosmoCode\SimpleForm\PreProcessor\PreProcessorHandler
	 * @inject
	 */
	private $preProcessorHandler;

    /**
     * initialize
     */
    public function initializeAction() {
        $this->initializeFormDataHandler();
        $this->initializeStepHandler();
        $this->initializeValidationConfigurationHandler();
        $this->initializeSessionHandler();
        $this->initializeInterceptorHandler();
        $this->initializeFinisherHandler();
		$this->initializePreProcessorHandler();
    }

    /**
     * initialize formDataHandler
     */
    private function initializeFormDataHandler() {
        $this->formDataHandler->setFormPrefix($this->settings['formPrefix']);
        $this->formDataHandler->setGpData($this->request->getArguments());
    }

    /**
     * initialize validationConfigurationHandler
     */
    private function initializeValidationConfigurationHandler() {
        $this->validationConfigurationHandler->setTypoScriptSettings($this->settings);
    }

    /**
     * initialize SessionHandler
     */
    private function initializeSessionHandler() {
        $this->sessionHandler->setSessionDataStorageKey('simpleForm');
    }

    /**
     * initialize StepHandler
     */
    private function initializeStepHandler() {
        $this->stepHandler->setSteps($this->settings['steps']);
        $this->stepHandler->initialize();
    }

    /**
     * initialize InterceptorHandler
     */
    private function initializeInterceptorHandler() {
        $this->interceptorHandler->setInterceptorsConfiguration($this->settings[$this->stepHandler->getCurrentStep()]['interceptors']);
        $this->interceptorHandler->createInterceptorsFromInterceptorsConfiguration();
    }

    /**
     * initialize FinisherHandler
     */
    private function initializeFinisherHandler() {
        $this->finisherHandler->setFinishersConfiguration($this->settings['finisher']);
        $this->finisherHandler->createFinishersFromFinishersConfiguration();
    }

	/**
	 * initialize PreProcessorHandler
	 */
	private function initializePreProcessorHandler() {
		$this->preProcessorHandler->setPreProcessorsConfiguration($this->settings[$this->stepHandler->getCurrentStep()]['preProcessors']);
		$this->preProcessorHandler->createPreProcessorsFromPreProcessorsConfiguration();
	}

    /**
	 * action displayForm
	 * TODO save next/previous step in session and make interntal redirect to displayFormAction
	 *
	 * @return void
	 */
	public function displayFormAction() {
        if($this->formDataHandler->formDataExists()) {
            $this->validate();
        } else {
			$this->preProcessorHandler->callAllPreProcessors();
            $this->stayOnCurrentStep();
        }
	}

    /**
     * validate formData of current step
     * TODO: refactor, add possibility to add validations to previous-step direction
     */
    private function validate() {
        if($this->stepHandler->getDirection() === \CosmoCode\SimpleForm\Utility\Form\StepHandler::GO_TO_PREVIOUS_STEP) {
            $this->goToPreviousStep();
            $this->validator->setDeactivateCheck(true);
        } else {
            $this->validator->checkFormValues();
            if($this->validationErrorHandler->validationErrorsExists()) {
                $this->stayOnCurrentStepAfterFailedValidation();
            } else {
                $this->interceptorHandler->callAllInterceptors();
                if($this->validationErrorHandler->validationErrorsExists()) {
                    $this->stayOnCurrentStepAfterFailedValidation();
                } else {
                    if($this->stepHandler->formIsOnLastStep()) {
                        $this->callFinisher();
                        $this->view->assign('finished', 1);
                        $this->view->assign('formData', $this->formDataHandler->getFormDataFromCurrentStep());
                    } else {
                        $this->goToNextStep();
                    }
                }
            }
        }
    }

    /**
     * call all Finishers
     */
    private function callFinisher() {
        $this->finisherHandler->callAllFinishers();
    }

    /**
     * stay on current Step
     */
    private function stayOnCurrentStep() {
		$this->populateFreshestFormData($this->sessionDataHandler->getFormDataFromCurrentStep(), $this->formDataHandler->getFormDataFromCurrentStep());
        $this->view->assign('step', $this->stepHandler->getCurrentStep());
    }

    /**
     * stay on current step after validation has failed
     */
    private function stayOnCurrentStepAfterFailedValidation() {
        $this->view->assign('step', $this->stepHandler->getCurrentStep());
        $this->view->assign('formData', $this->formDataHandler->getFormDataFromCurrentStep());
        $this->view->assign('validationErrors', $this->validationErrorHandler->getValidationErrorsFromCurrentStep());
    }

    /**
     * go to next step
     */
    private function goToNextStep() {
        $this->sessionDataHandler->storeFormDataFromCurrentStep($this->formDataHandler->getFormDataFromCurrentStep());
        $this->view->assign('formData', $this->sessionDataHandler->getFormDataFromStep($this->stepHandler->getNextStep()));
        $this->view->assign('step', $this->stepHandler->getNextStep());
    }

    /**
     * go to previous step
     */
    private function goToPreviousStep() {
        $this->sessionDataHandler->storeFormDataFromCurrentStep($this->formDataHandler->getFormDataFromCurrentStep());
        $this->view->assign('formData', $this->sessionDataHandler->getFormDataFromStep($this->stepHandler->getPreviousStep()));
        $this->view->assign('step', $this->stepHandler->getPreviousStep());
    }

	protected function populateFreshestFormData($sessionData, $formData) {
		if($sessionData) {
			$this->view->assign('formData', $sessionData);
		} else {
			$this->view->assign('formData', $formData);
		}
	}
}
?>