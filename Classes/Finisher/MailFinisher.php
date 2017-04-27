<?php
namespace CosmoCode\SimpleForm\Finisher;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MailUtility;


/**
 * Class MailFinisher
 *
 * This code is adapted from \TYPO3\CMS\Form\PostProcess\MailPostProcessor
 *
 * Example TypoScript configuration:
 * templateRootPaths.10 =
 * layoutRootPaths.10 =
 * partialRootPaths.10 =
 * user {
 *      senderEmail =
 *      senderName =
 *      recipientEmail =
 *      # or value form the form (only available for recipient email)
 *      recipientField {
 *          step =
 *          field =
 *      }
 *      # translation not supported yet
 *      subject =
 *      plaintextTemplate =
 *      htmlTemplate
 * }
 * admin .< user
 *
 */
class MailFinisher extends \CosmoCode\SimpleForm\Finisher\AbstractFinisher
{


    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     * @inject
     */
    protected $objectManager;

    /**
     * @var \TYPO3\CMS\Core\Mail\MailMessage
     */
    protected $mailMessage;

    /**
     * @var array
     */
    protected $templateRootPaths;

    /**
     * @var array
     */
    protected $layoutRootPaths;

    /**
     * @var array
     */
    protected $partialRootPaths;

    /**
     * @var array
     */
    protected $typoScript;


    public function finish()
    {


        $logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Log\LogManager')->getLogger(__CLASS__);
        $logger->info('Everything went fine.');

        $this->typoScript = $this->getFinisherConfiguration();
        $this->setTemplatePaths();

        if (isset($this->typoScript['user'])) {
            $this->mailMessage = GeneralUtility::makeInstance('TYPO3\CMS\Core\Mail\MailMessage');
            $userMailSettings = $this->typoScript['user'];
            $this->send($userMailSettings);
        }

        if (isset($this->typoScript['admin'])) {
            $this->mailMessage = GeneralUtility::makeInstance('TYPO3\CMS\Core\Mail\MailMessage');
            $adminMailSettings = $this->typoScript['admin'];
            $this->send($adminMailSettings);
        }
    }

    /**
     * Sets the subject of the mail message
     * If not configured, it will use a default setting
     *
     * @param array $mailSettings
     */
    protected function setSubject($mailSettings)
    {
        if (isset($mailSettings['subject'])) {
            $subject = $mailSettings['subject'];
        } else {
            $subject = GeneralUtility::getIndpEnv('HTTP_HOST');
        }

        $this->mailMessage->setSubject($subject);
    }

    /**
     * Sets the sender of the mail message
     *
     * @param array $mailSettings
     */
    protected function setFrom($mailSettings)
    {
        if (isset($mailSettings['senderEmail'])) {
            $fromEmail = $mailSettings['senderEmail'];
        }

        if (isset($mailSettings['senderName'])) {
            $fromName = $mailSettings['senderName'];
        }

        if (!empty($fromName)) {
            $from = array($fromEmail => $fromName);
        } else {
            $from = $fromEmail;
        }
        $this->mailMessage->setFrom($from);
    }

    /**
     * Adds the receiver of the mail message when configured
     *
     * @param array $mailSettings
     */
    protected function setTo($mailSettings)
    {
        if (isset($mailSettings['recipientEmail'])) {
            $email = $mailSettings['recipientEmail'];
        } elseif (isset($mailSettings['recipientField'])) {
            $email = $this->getValueFromForm($mailSettings['recipientField']);
        }
        $this->mailMessage->setTo($email);
    }

    /**
     * Set the default character set used
     *
     * Respect formMailCharset if it was set, otherwise use metaCharset for mail
     * if different from renderCharset
     *
     * @return void
     */
    protected function setCharacterSet()
    {
        $characterSet = null;
        if ($GLOBALS['TSFE']->config['config']['formMailCharset']) {
            $characterSet = $GLOBALS['TSFE']->csConvObj->parse_charset($GLOBALS['TSFE']->config['config']['formMailCharset']);
        } elseif ($GLOBALS['TSFE']->metaCharset != $GLOBALS['TSFE']->renderCharset) {
            $characterSet = $GLOBALS['TSFE']->metaCharset;
        }
        if ($characterSet) {
            $this->mailMessage->setCharset($characterSet);
        }
    }

    /**
     * Add the HTML content
     *
     * Add a MimePart of the type text/html to the message.
     *
     * @param array $mailSettings
     */
    protected function setHtmlContent($mailSettings)
    {
        if ($mailSettings['htmlTemplate']) {
            $htmlContent = $this->getView($mailSettings['htmlTemplate'])->render();
            if (!$this->mailMessage->getBody()) {
                $this->mailMessage->setBody($htmlContent, 'text/html');
            } else {
                $this->mailMessage->addPart($htmlContent, 'text/html');
            }
        }
    }

    /**
     * Add the plain content
     *
     * Add a MimePart of the type text/plain to the message.
     *
     * @param array $mailSettings
     */
    protected function setPlainContent($mailSettings)
    {
        if ($mailSettings['plaintextTemplate']) {
            $plainContent = $this->getView($mailSettings['plaintextTemplate'], 'Plain')->render();

            if (!$this->mailMessage->getBody()) {
                $this->mailMessage->setBody($plainContent, 'text/plain');
            } else {
                $this->mailMessage->addPart($plainContent, 'text/plain');
            }
        }
    }

    /**
     * Sends the mail.
     * Sending the mail requires the recipient and message to be set.
     *
     * @param array $mailSettings
     * @return void
     */
    protected function send($mailSettings)
    {
        $this->setSubject($mailSettings);
        $this->setFrom($mailSettings);
        $this->setTo($mailSettings);
        $this->setHtmlContent($mailSettings);
        $this->setPlainContent($mailSettings);

        if ($this->mailMessage->getTo() && $this->mailMessage->getBody()) {
            $this->mailMessage->send();
        }
    }


    /**
     * Set the html and plaintext templates
     *
     * @return void
     */
    protected function setTemplatePaths()
    {
        if (isset($this->typoScript['templateRootPaths'])
            && is_array($this->typoScript['templateRootPaths'])
        ) {
            $this->templateRootPaths = $this->typoScript['templateRootPaths'];
        }

        if (isset($this->typoScript['layoutRootPaths'])
            && $this->typoScript['layoutRootPaths'] !== ''
        ) {
            $this->layoutRootPaths = $this->typoScript['layoutRootPaths'];
        }

        if (isset($this->typoScript['partialRootPaths'])
            && $this->typoScript['partialRootPaths'] !== ''
        ) {
            $this->partialRootPaths = $this->typoScript['partialRootPaths'];
        }


    }

    /**
     * Make fluid view instance
     *
     * @param string $templateName
     * @param string $scope
     * @return \TYPO3\CMS\Fluid\View\StandaloneView
     */
    protected function getView($templateName, $scope = 'Html')
    {

        /** @var \TYPO3\CMS\Fluid\View\StandaloneView $view */
        $view = $this->objectManager->get(\TYPO3\CMS\Fluid\View\StandaloneView::class);

        $view->setLayoutRootPaths($this->layoutRootPaths);
        $view->setTemplateRootPaths($this->templateRootPaths);
        $view->setPartialRootPaths($this->partialRootPaths);
        $view->setTemplate($templateName);
        $view->assignMultiple(array(
            'formData' => $this->sessionDataHandler->getFormData()
        ));

        return $view;
    }

    /**
     * Get value from submitted data
     *
     * @param array $fieldConf
     * @return null|string
     */
    private function getValueFromForm($fieldConf)
    {
        $value = null;
        if ($fieldConf['step'] && $fieldConf['field']) {
            $value = $this->formDataHandler->getFormValueAtStep($fieldConf['field'], $fieldConf['step']);
        }
        return $value;
    }

}