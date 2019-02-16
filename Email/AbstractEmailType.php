<?php

namespace Requestum\EmailSenderBundle\Email;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

abstract class AbstractEmailType implements EmailTypeInterface
{
    private $templating;
    private $translator;

    /**
     * {@inheritdoc}
     */
    abstract public function buildMessage(\Swift_Message $message, array $parameters);

    /**
     * @param EngineInterface $templating
     * @return $this
     */
    public function setTemplating(EngineInterface $templating)
    {
        $this->templating = $templating;
        return $this;
    }

    /**
     * @param TranslatorInterface $translator
     * @return $this
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
        return $this;
    }

    /**
     * @return EngineInterface
     */
    public function getTemplating()
    {
        return $this->templating;
    }

    /**
     * @return TranslatorInterface
     */
    public function getTranslator()
    {
        return $this->translator;
    }

}
