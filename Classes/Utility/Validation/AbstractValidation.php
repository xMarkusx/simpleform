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
abstract class AbstractValidation {

    /**
     * @var \CosmoCode\SimpleForm\Utility\Form\FormDataHandler
     * @inject
     */
    protected $formDataHandler = null;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var array
     */
    protected $conf;

    /**
     * @var string
     */
    protected $eachFieldName = '';

    /**
     * @var string
     */
    protected $eachIndex = '';

    /**
     * @param mixed $value
     * @return bool
     */
    public function checkValue($value) {
        $this->value = $value;
        return $this->validate();
    }

    /**
     * @return boolean
     */
    abstract protected function validate();

    /**
     * @return string
     */
    abstract public function getValidationCode();

    /**
     * @param array $conf
     */
    public function setConf($conf) {
        $this->conf = $conf;
    }

    /**
     * @return array
     */
    public function getConf() {
        return $this->conf;
    }

    /**
     * @return string
     */
    public function getEachFieldName() {
        return $this->eachFieldName;
    }

    /**
     * @param string $eachFieldName
     */
    public function setEachFieldName($eachFieldName) {
        $this->eachFieldName = $eachFieldName;
    }

    /**
     * @return string
     */
    public function getEachIndex() {
        return $this->eachIndex;
    }

    /**
     * @param string $eachIndex
     */
    public function setEachIndex($eachIndex) {
        $this->eachIndex = $eachIndex;
    }

    /**
     * Check if Validation is called in an each-loop
     * @return bool
     */
    public function isCalledInEachLoop() {
        if(!empty($this->eachFieldName) && !empty($this->eachIndex)) {
            return TRUE;
        }
        return FALSE;
    }
}
?>
