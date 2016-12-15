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
class SessionHandler implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * @var string
     */
    private $sessionDataStorageKey;

    /**
     * @param string $sessionDataStorageKey
     */
    public function setSessionDataStorageKey($sessionDataStorageKey)
    {
        $this->sessionDataStorageKey = $sessionDataStorageKey;
    }

    /**
     *
     * Loads the data from the session, and populates formData and passedActionMethods
     * @return mixed
     *
     */
    public function restoreFromSession()
    {
        return $GLOBALS['TSFE']->fe_user->getKey('ses', $this->sessionDataStorageKey);
    }

    /**
     * Stores form data to session array
     *
     * @param mixed $data
     */
    public function storeSessionData($data)
    {
        $GLOBALS['TSFE']->fe_user->setKey('ses', $this->sessionDataStorageKey, $data);
        $GLOBALS['TSFE']->fe_user->storeSessionData();
    }

    /**
     * Clears the data from the current session
     *
     * @return void
     */
    public function clearSessionData()
    {
        $GLOBALS['TSFE']->fe_user->setKey('ses', $this->sessionDataStorageKey, null);
        $GLOBALS['TSFE']->fe_user->storeSessionData();
    }
}
