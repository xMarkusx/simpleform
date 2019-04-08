<?php
namespace CosmoCode\SimpleForm\Utility\Security;

    /***************************************************************
     *  Copyright notice
     *
     *  (c) 2016 Markus Baumann <markus.baumann.b@googlemail.com>
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

use TYPO3\CMS\Core\Crypto\Random;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 *
 * @package simple_form
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class CsrfProtection
{

    /**
     * @var \CosmoCode\SimpleForm\Utility\Form\FormDataHandler
     * @inject
     */
    private $formDataHandler;

    /**
     * @var string
     */
    private $secret = '';

    /**
     * @var bool
     */
    private $active = false;

    /**
     * @return string
     */
    public function generateCsrfToken()
    {
        $token = sha1($this->secret . time() . GeneralUtility::makeInstance(Random::class)->generateRandomBytes(20));
        $GLOBALS['TSFE']->fe_user->setKey('ses', 'token', $token);
        return $token;
    }

    /**
     * @return bool
     */
    public function validateCsrfToken()
    {
        if ($this->isActive()) {
            $csrfToken = $GLOBALS['TSFE']->fe_user->getKey('ses', 'token');
            $gpData = $this->formDataHandler->getGpData();
            $csrfTokenToBeTested = $gpData['token'];
            if ($csrfToken !== $csrfTokenToBeTested) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;
    }

    public function activate()
    {
        $this->active = true;
    }

    public function deactivate()
    {
        $this->active = false;
    }

    public function isActive()
    {
        return $this->active;
    }
}
