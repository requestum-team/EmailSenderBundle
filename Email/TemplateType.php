<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 11.08.16
 * Time: 13:31
 */

namespace Requestum\EmailSenderBundle\Email;


class TemplateType extends AbstractEmailType
{
    private $sender;
    private $email;
    private $subject;
    private $template;
    private $templateData;

    protected function setTemplateVariable($key, $value)
    {
        $this->templateData[$key] = $value;
    }


    public function __construct($sender, $email, $subject, $template, $data = array())
    {
        $this->sender = $sender;
        $this->email = $email;
        $this->subject = $subject;
        $this->template = $template;
        $this->templateData = $data;
    }

    protected function setSubject($subject)
    {
        $this->subject = $subject;
    }

    protected function getTemplateData()
    {
        return $this->templateData;
    }

    public function buildMessage(\Swift_Mime_Message $message, array $parameters)
    {
        $message->setFrom($this->sender);
        $message->setTo($this->email);
        $message->setSubject($this->subject);
        $message->setBody($this->getTemplating()->render(
            $this->template,
            $this->templateData
        ), 'text/html');
    }
}