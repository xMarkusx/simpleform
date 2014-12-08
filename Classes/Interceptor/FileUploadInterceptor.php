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
class FileUploadInterceptor extends AbstractInterceptor {

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     * @inject
     */
    protected $objectManager;

    /**
     * @var \TYPO3\CMS\Core\Utility\File\BasicFileUtility
     */
    protected $basicFileUtility;

    /**
     * @var array
     */
    protected $currentFile;

    /**
     * @var array
     */
    protected $currentFileConfiguration;

    /**
     * intercept
     */
    public function intercept() {
        $this->basicFileUtility = $this->objectManager->get('TYPO3\CMS\Core\Utility\File\BasicFileUtility');
        $this->processUploadFields();
    }

    /**
     * process upload fields
     */
    private function processUploadFields() {
        $fileConfigurations = $this->interceptorConfiguration['files'];
        foreach($fileConfigurations as $fileConfiguration) {
            $this->currentFileConfiguration = $fileConfiguration;
            $this->currentFile = $this->formDataHandler->getFormValue($fileConfiguration['formName']);
            if($this->checkCurrentFile()) {
                $this->storeCurrentFile();
            }
        }
    }

    /**
     * check file
     */
    private function checkCurrentFile() {
        if($this->fileIsSelected()) {
            if($this->checkFileType() && $this->checkFileSize()) {
                return true;
            }
            $this->formDataHandler->setFormValue($this->currentFileConfiguration['formName'], null);
            return false;
        } else {
            if($this->fileIsRequired()) {
                $this->addFileErrorToValidationErrors('file_required');
                return false;
            }
            return true;
        }
    }

    /**
     * check current file type
     */
    private function checkFileType() {
		$fileInfo = finfo_open(FILEINFO_MIME_TYPE);
		$mimeType = finfo_file($fileInfo, $this->currentFile['tmp_name']);
		finfo_close($fileInfo);
        if(in_array($mimeType, explode(',', $this->currentFileConfiguration['allowedMimeTypes']))) {
            return true;
        }
        $this->addFileErrorToValidationErrors('no_valid_file_type');
        return false;
    }

    /**
     * check current file size
     * @return bool
     */
    private function checkFileSize() {
        if($this->currentFile['size'] <= $this->currentFileConfiguration['maxSize']) {
            return true;
        }
        $this->addFileErrorToValidationErrors('no_valid_file_size');
        return false;
    }

    /**
     * return required state
     * @return bool
     */
    private function fileIsRequired() {
        if(empty($this->currentFileConfiguration['required'])) {
            return false;
        }
        return true;
    }

    /**
     * return true if any file is selected for upload, else return false
     * @return bool
     */
    private function fileIsSelected() {
        if(empty($this->currentFile['name'])) {
            return false;
        }
        return true;
    }

    /**
     * store uploaded files
     */
    private function storeCurrentFile() {
		$this->createUploadFolderIfNotExisting();
		$uploadFolder = $this->interceptorConfiguration['uploadFolder'].$this->formDataHandler->getFormValue('uploadFolderHash');
        if ($_FILES['tx_simpleform_simpleform']) {
            $fileName = $this->basicFileUtility->getUniqueName($this->currentFile['name'], \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($uploadFolder));
            \TYPO3\CMS\Core\Utility\GeneralUtility::upload_copy_move($this->currentFile['tmp_name'], $fileName);
        }
    }

	protected function createUploadFolderIfNotExisting() {
		$this->generateUploadFolderHash();
		if (!file_exists($this->interceptorConfiguration['uploadFolder'].$this->formDataHandler->getFormValue('uploadFolderHash'))) {
			mkdir($this->interceptorConfiguration['uploadFolder'].$this->formDataHandler->getFormValue('uploadFolderHash'), 0777, true);
		}
	}

	protected function generateUploadFolderHash() {
		if(!$this->formDataHandler->getFormValue('uploadFolderHash')) {
			$randomString = rand(0,100000).'--'.time();
			$randomHash = md5($randomString);
			$this->formDataHandler->setFormValue('uploadFolderHash', $randomHash);
		}
	}

    /**
     * add validation error
     * @param string $validationCode
     */
    private function addFileErrorToValidationErrors($validationCode) {
        $validationError = new \CosmoCode\SimpleForm\Utility\Validation\ValidationError();
        $validationError->setFormField($this->currentFileConfiguration['formName']);
        $validationError->setValidationCode($validationCode);
        $this->validationErrorHandler->addValidationError($validationError);
    }

    /**
     * TODO: Evaluate if this function is needed for Fluid version < 6.0.0. Fluid 6.0.0 provides all necessary data in post variables
     *
     * @param string $nameType
     * @return mixed
     */
    private function getFileNameFromFiles($nameType) {
        $fileName = $_FILES['tx_simpleform_simpleform'][$nameType];
        foreach($this->currentUploadField as $fieldName) {
            $fileName = $fileName[$fieldName];
        }
        return $fileName;
    }
}
?>