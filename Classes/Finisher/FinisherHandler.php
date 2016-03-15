<?php
namespace CosmoCode\SimpleForm\Finisher;

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
class FinisherHandler implements \TYPO3\CMS\Core\SingletonInterface {

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     * @inject
     */
    protected $objectManager;

    /**
     * @var array
     */
    private $finishers;

    /**
     * @var array
     */
    private $finishersConfiguration;

    /**
     * create finishers
     * TODO: write log if exception appears
     */
    public function createFinishersFromFinishersConfiguration() {
		$this->resetFinishers();
        if ($this->finishersConfiguration) {
            foreach($this->finishersConfiguration as $singleFinisherConfiguration) {
                /** @var $finisher AbstractFinisher */
                try {
                    $finisher = $this->objectManager->get($singleFinisherConfiguration['finisher']);
                    $finisher->setFinisherConfiguration($singleFinisherConfiguration['conf']);
                    $this->finishers[] = $finisher;
                } catch(\Exception $exception) {

                }
            }
        }
    }

    /**
     * call finish function of all configured finishers
     */
    public function callAllFinishers() {
		if(empty($this->finishers)) {
			return 0;
		}
        foreach($this->finishers as $finisher) {
            /** @var $finisher AbstractFinisher */
            $finisher->finish();
        }
    }

    /**
     * @param array $finishersConfiguration
     */
    public function setFinishersConfiguration($finishersConfiguration) {
        $this->finishersConfiguration = $finishersConfiguration;
    }

    /**
     * @return array
     */
    public function getFinishersConfiguration() {
        return $this->finishersConfiguration;
    }

	private function resetFinishers() {
		$this->finishers = array();
	}
}
?>
