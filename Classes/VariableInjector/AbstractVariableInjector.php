<?php
namespace CosmoCode\SimpleForm\VariableInjector;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Christian Baer <chr.baer@gmail.com>
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
abstract class AbstractVariableInjector
{

    /**
     * @var array
     */
    protected $variableInjectorConfiguration;


    /**
     * @var array
     */
    private $formPluginSettings;


    /**
     * all variables in associative array get assigned into view
     *
     * @return array associative array
     */
    abstract public function getInjectVariables(
    );

    /**
     * @param array $variableInjectorConfiguration
     */
    public function setVariableInjectorConfiguration($variableInjectorConfiguration)
    {
        if (isset($variableInjectorConfiguration['enableTypoScriptProcessing'])
            && $variableInjectorConfiguration['enableTypoScriptProcessing'] === '1'
        ) {
            \CosmoCode\SimpleForm\Utility\TypoScript::processTypoScriptSetupRecursive($variableInjectorConfiguration);
        }

        $this->variableInjectorConfiguration = $variableInjectorConfiguration;
    }

    /**
     * @return array
     */
    public function getVariableInjectorConfiguration()
    {
        return $this->variableInjectorConfiguration;
    }

    /**
     * @param array $formPluginSettings
     */
    public function setFormPluginSettings($formPluginSettings)
    {
        $this->formPluginSettings = $formPluginSettings;
    }

    /**
     * @return array
     */
    public function getFormPluginSettings()
    {
        return $this->formPluginSettings;
    }


}
