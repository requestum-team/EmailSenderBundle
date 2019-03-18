<?php

namespace Requestum\EmailSenderBundle\Email;

use Requestum\EmailSenderBundle\Model\TranslatableString;

class TemplateType extends AbstractEmailType
{
    private $sender;
    private $email;
    private $subject;
    private $template;
    private $templateData;

    /**
     * TemplateType constructor.
     * @param string                     $sender
     * @param string                     $email
     * @param string|TranslatableString  $subject
     * @param string                     $template
     * @param array                      $data
     */
    public function __construct($sender, $email, $subject, $template, $data = array())
    {
        $this->sender = $sender;
        $this->email = $email;
        $this->subject = $subject;
        $this->template = $template;
        $this->templateData = $data;
    }

    /**
     * @param $subject
     */
    protected function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return array
     */
    protected function getTemplateData()
    {
        return $this->templateData;
    }

    /**
     * @param $key
     * @param $value
     */
    protected function setTemplateVariable($key, $value)
    {
        $this->templateData[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function buildMessage(\Swift_Mime_SimpleMessage $message, array $parameters)
    {
        if ($this->subject instanceof TranslatableString) {
            $this->subject = $this->subject->getString($this->getTranslator());
        }

        $message->setFrom($this->sender);
        $message->setTo($this->email);
        $message->setSubject($this->subject);
        $message->setBody($this->getTemplating()->render(
            $this->template,
            $this->templateData
        ), 'text/html');
    }
}
