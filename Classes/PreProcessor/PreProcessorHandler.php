<?php
namespace CosmoCode\SimpleForm\PreProcessor;

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
class PreProcessorHandler implements \TYPO3\CMS\Core\SingletonInterface {

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     * @inject
     */
    protected $objectManager;

    /**
     * @var array
     */
    private $preProcessors;

    /**
     * @var array
     */
    private $preProcessorsConfiguration;

    /**
     * create preProcessors
     * TODO: write log if exception appears
     */
    public function createPreProcessorsFromPreProcessorsConfiguration() {
        foreach($this->preProcessorsConfiguration as $singlePreProcessorConfiguration) {
            /** @var $preProcessor AbstractPreProcessor */
            try {
                $preProcessor = $this->objectManager->get($singlePreProcessorConfiguration['preProcessor']);
                $preProcessor->setPreProcessorConfiguration($singlePreProcessorConfiguration['conf']);
                $this->preProcessors[] = $preProcessor;
            } catch(\Exception $exception) {

            }
        }
    }

    /**
     * call finish function of all configured preProcessors
     */
    public function callAllPreProcessors() {
		if(empty($this->preProcessors)) {
			return 0;
		}
        foreach($this->preProcessors as $preProcessor) {
            /** @var $preProcessor AbstractPreProcessor */
            $preProcessor->preProcess();
        }
    }

    /**
     * @param array $preProcessorsConfiguration
     */
    public function setPreProcessorsConfiguration($preProcessorsConfiguration) {
        $this->preProcessorsConfiguration = $preProcessorsConfiguration;
    }

    /**
     * @return array
     */
    public function getPreProcessorsConfiguration() {
        return $this->preProcessorsConfiguration;
    }
}
?>