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
class FormController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

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
     * @var \CosmoCode\SimpleForm\VariableInjector\VariableInjectorHandler
     * @inject
     */
    private $variableInjectorHandler;

    /**
     * @var \CosmoCode\SimpleForm\Utility\Security\CsrfProtection
     * @inject
     */
    private $csrfProtection = null;

    /**
     * initialize
     * @param string $step
     */
    public function initialize($step)
    {
        $this->initializeFormDataHandler();
        $this->initializeStepHandler($step);
        $this->initializeValidationConfigurationHandler();
        $this->initializeSessionHandler();
        $this->initializeInterceptorHandler();
        $this->initializeFinisherHandler();
        $this->initializePreProcessorHandler();
        $this->initializeVariableInjectorHandler();
        if (array_key_exists('activateCsrfProtection', $this->settings) && $this->settings['activateCsrfProtection'] === '1') {
            $this->initializeCsrfProtection();
        }
    }

    /**
     * initialize formDataHandler
     */
    private function initializeFormDataHandler()
    {
        $this->formDataHandler->setFormPrefix($this->settings['formPrefix']);
        $this->formDataHandler->setGpData($this->request->getArguments());
    }

    /**
     * initialize validationConfigurationHandler
     */
    private function initializeValidationConfigurationHandler()
    {
        $this->validationConfigurationHandler->setTypoScriptSettings($this->settings);
    }

    /**
     * initialize SessionHandler
     */
    private function initializeSessionHandler()
    {
        if ($this->settings['formPrefix']) {
            $this->sessionHandler->setSessionDataStorageKey($this->settings['formPrefix']);
        } else {
            $this->sessionHandler->setSessionDataStorageKey('simpleForm');
        }
    }

    /**
     * initialize StepHandler
     * @param string $step
     */
    private function initializeStepHandler($step)
    {
        $this->stepHandler->setSteps($this->settings['steps']);
        if ($step) {
            $this->stepHandler->setCurrentStep($step);
        }
        $this->stepHandler->initialize();
    }

    /**
     * initialize InterceptorHandler
     */
    private function initializeInterceptorHandler()
    {
        $this->interceptorHandler->setInterceptorsConfiguration($this->settings[$this->stepHandler->getCurrentStep()]['interceptors']);
        $this->interceptorHandler->createInterceptorsFromInterceptorsConfiguration();
    }

    /**
     * initialize FinisherHandler
     */
    private function initializeFinisherHandler()
    {
        $this->finisherHandler->setFinishersConfiguration($this->settings['finisher']);
        $this->finisherHandler->createFinishersFromFinishersConfiguration();
    }

    /**
     * initialize PreProcessorHandler
     */
    private function initializePreProcessorHandler()
    {
        $this->preProcessorHandler->setPreProcessorsConfiguration($this->settings[$this->stepHandler->getCurrentStep()]['preProcessors']);
        $this->preProcessorHandler->createPreProcessorsFromPreProcessorsConfiguration();
    }

    /**
     * initialize VariableInjectorHandler
     */
    private function initializeVariableInjectorHandler()
    {
        $this->variableInjectorHandler->setVariableInjectorsConfiguration($this->settings[$this->stepHandler->getCurrentStep()]['variableInjectors']);
        $this->variableInjectorHandler->createVariableInjectorsFromVariableInjectorsConfiguration();
    }

    /**
     * initialize Csrf Protection
     */
    private function initializeCsrfProtection()
    {
        $this->csrfProtection->setSecret($this->settings['secret']);
        $this->csrfProtection->activate();
    }

    /**
     * action displayForm
     * @param string $step
     * @param boolean $simulateSubmit
     *
     * @return void
     */
    public function displayFormAction($step = null, $simulateSubmit = false)
    {
        $this->initialize($step);
        $gpData = $this->formDataHandler->getGpData();
        if (empty($gpData['formPrefix']) || $gpData['formPrefix'] == $this->formDataHandler->getFormPrefix()) {
            if ($step && !$simulateSubmit) {
                $this->stepHandler->setCurrentStep($step);
                $this->preProcessorHandler->callAllPreProcessors();
                $this->variableInjectorHandler->callAllVariableInjectors();
                $this->stayOnCurrentStep();
            } elseif ($this->formDataHandler->formDataExists()) {
                if (!$this->csrfProtection->validateCsrfToken()) {
                    $this->sessionHandler->clearSessionData();
                    $this->redirect("displayForm");
                }
                $this->validate();
            } elseif ($simulateSubmit && $step) {
                $manipulatedGpData = $gpData;
                $manipulatedGpData[$this->formDataHandler->getFormPrefix()][$step] = $this->sessionDataHandler->getFormDataFromCurrentStep();
                $this->formDataHandler->setGpData($manipulatedGpData);
                $this->validate();
            } else {
                $this->preProcessorHandler->callAllPreProcessors();
                $this->variableInjectorHandler->callAllVariableInjectors();
                $this->stayOnCurrentStep();
            }
        } else {
            $this->preProcessorHandler->callAllPreProcessors();
            $this->variableInjectorHandler->callAllVariableInjectors();
            $this->stayOnCurrentStep();
        }
        $this->view->assign('stepHandler', $this->stepHandler);
        $this->view->assign('sessionData', $this->sessionDataHandler->getFormData());
    }

    /**
     * validate formData of current step
     * TODO: refactor, add possibility to add validations to previous-step direction
     */
    private function validate()
    {
        if ($this->stepHandler->getDirection() === \CosmoCode\SimpleForm\Utility\Form\StepHandler::GO_TO_PREVIOUS_STEP) {
            $this->goToPreviousStep();
            $this->validator->setDeactivateCheck(true);
        } else {
            $this->validator->checkFormValues();
            if ($this->validationErrorHandler->validationErrorsExists()) {
                $this->stayOnCurrentStepAfterFailedValidation();
            } else {
                $this->interceptorHandler->callAllInterceptors();
                if ($this->validationErrorHandler->validationErrorsExists()) {
                    $this->stayOnCurrentStepAfterFailedValidation();
                } else {
                    if ($this->stepHandler->formIsOnLastStep()) {
                        $this->sessionDataHandler->storeFormDataFromCurrentStep($this->formDataHandler->getFormDataFromCurrentStep());
                        $this->callFinisher();
                        $this->view->assign('finished', 1);
                        $this->view->assign('formData', $this->formDataHandler->getFormDataFromCurrentStep());
                        if ($this->settings['clearSessionDataWhenFinished'] === '1') {
                            $this->sessionHandler->clearSessionData();
                        }
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
    private function callFinisher()
    {
        $this->finisherHandler->callAllFinishers();
    }

    /**
     * stay on current Step
     */
    private function stayOnCurrentStep()
    {
        $this->populateFreshestFormData($this->sessionDataHandler->getFormDataFromCurrentStep(), $this->formDataHandler->getFormDataFromCurrentStep());
        $this->view->assign('step', $this->stepHandler->getCurrentStep());
        $this->view->assign('token', $this->csrfProtection->generateCsrfToken());
    }

    /**
     * stay on current step after validation has failed
     */
    private function stayOnCurrentStepAfterFailedValidation()
    {
        $this->view->assign('step', $this->stepHandler->getCurrentStep());
        $this->view->assign('formData', $this->formDataHandler->getFormDataFromCurrentStep());
        $this->view->assign('validationErrors', $this->validationErrorHandler->getValidationErrorsFromCurrentStep());
        $this->view->assign('token', $this->csrfProtection->generateCsrfToken());
    }

    /**
     * go to next step
     */
    private function goToNextStep()
    {
        $this->sessionDataHandler->storeFormDataFromCurrentStep($this->formDataHandler->getFormDataFromCurrentStep());
        $additionalParams = $this->getAdditionalParams();
        $additionalParams['step'] = $this->stepHandler->getNextStep();
        $this->view->assign('token', $this->csrfProtection->generateCsrfToken());
        $this->redirect("displayForm", null, null, $additionalParams);
    }

    /**
     * go to previous step
     */
    private function goToPreviousStep()
    {
        $this->sessionDataHandler->storeFormDataFromCurrentStep($this->formDataHandler->getFormDataFromCurrentStep());
        $additionalParams = $this->getAdditionalParams();
        $additionalParams['step'] = $this->stepHandler->getPreviousStep();
        $this->view->assign('token', $this->csrfProtection->generateCsrfToken());
        $this->redirect("displayForm", null, null, $additionalParams);
    }

    protected function populateFreshestFormData($sessionData, $formData)
    {
        if ($sessionData) {
            $this->view->assign('formData', $sessionData);
        } else {
            $this->view->assign('formData', $formData);
        }
    }

    /**
     * Returns array with additional params defined in typoscript settings
     *
     * @return array
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    protected function getAdditionalParams()
    {
        $additionalParams = array();
        foreach ($this->settings['additionalParams'] as $additionalParam) {
            if ($this->request->hasArgument($additionalParam)) {
            }
            $additionalParams[$additionalParam] = $this->request->getArgument($additionalParam);
        }
        return $additionalParams;
    }
}
