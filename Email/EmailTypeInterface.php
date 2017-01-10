<?php
/**
 * Created by JetBrains PhpStorm.
 * User: sergey
 * Date: 6/10/13
 * Time: 3:48 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Requestum\EmailSenderBundle\Email;


use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

interface EmailTypeInterface
{
    /**
     * Builds message instance
     *
     * @param \Swift_Mime_Message $message
     * @return mixed
     */
    public function buildMessage(\Swift_Mime_Message $message, array $parameters);

    /**
     * Set's templating engine
     *
     * @param EngineInterface $templating
     * @return $this
     */
    public function setTemplating(EngineInterface $templating);

    /**
     * @param TranslatorInterface $translator
     * @return $this
     */
    public function setTranslator(TranslatorInterface $translator);
}