<?php
namespace CosmoCode\SimpleForm\Interceptor;

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
class InterceptorHandler implements \TYPO3\CMS\Core\SingletonInterface {

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     * @inject
     */
    protected $objectManager;

    /**
     * @var array
     */
    private $interceptors;

    /**
     * @var array
     */
    private $interceptorsConfiguration;

    /**
     * create interceptors
     * TODO: write log if exception appears
     */
    public function createInterceptorsFromInterceptorsConfiguration() {
        foreach($this->interceptorsConfiguration as $singleInterceptorConfiguration) {
            /** @var $interceptor AbstractInterceptor */
            try {
                $interceptor = $this->objectManager->get($singleInterceptorConfiguration['interceptor']);
                $interceptor->setInterceptorConfiguration($singleInterceptorConfiguration['conf']);
                $this->interceptors[] = $interceptor;
            } catch(\Exception $exception) {

            }
        }
    }

    /**
     * call finish function of all configured interceptors
     */
    public function callAllInterceptors() {
		if(empty($this->interceptors)) {
			return 0;
		}
        foreach($this->interceptors as $interceptor) {
            /** @var $interceptor AbstractInterceptor */
            $interceptor->intercept();
        }
    }

    /**
     * @param array $interceptorsConfiguration
     */
    public function setInterceptorsConfiguration($interceptorsConfiguration) {
        $this->interceptorsConfiguration = $interceptorsConfiguration;
    }

    /**
     * @return array
     */
    public function getInterceptorsConfiguration() {
        return $this->interceptorsConfiguration;
    }
}
?>