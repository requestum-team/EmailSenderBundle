<?php
/**
 * Created by JetBrains PhpStorm.
 * User: sergey
 * Date: 6/10/13
 * Time: 3:47 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Requestum\EmailSenderBundle\Service;

use Requestum\EmailSenderBundle\Email\EmailTypeInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

class EmailSender
{

    private $mailer;
    private $templating;
    private $translator;
    private $parameters;

    public function __construct(\Swift_Mailer $mailer, EngineInterface $templating, TranslatorInterface $translator)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->translator = $translator;
        $this->parameters = array();
    }

    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function addParameter($key, $value)
    {
        $this->parameters[$key] = $value;
    }

    public function buildMessage(EmailTypeInterface $type, \Swift_Mime_Message $message = null)
    {
        if (null === $message) {
            $message = \Swift_Message::newInstance();
        }

        $type->setTemplating($this->templating);
        $type->setTranslator($this->translator);
        $type->buildMessage($message, $this->parameters);

        return $message;
    }

    public function sendMessage(\Swift_Mime_Message $message)
    {
        return $this->mailer->send($message);
    }

    public function sendType(EmailTypeInterface $emailType)
    {
        $this->sendMessage($this->buildMessage($emailType));
    }

}