<?php

namespace Requestum\EmailSenderBundle\Model;

use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class TranslatableString DTO to hold translatable string parameters
 */
class TranslatableString
{
    /** @var  string */
    private $message;

    /** @var  array */
    private $parameters;

    /** @var  int */
    private $plural;

    /** @var  string */
    private $translationDomain;

    /**
     * TranslatableString constructor.
     * @param string $message
     * @param array $parameters
     * @param int $plural
     * @param string $translationDomain
     */
    public function __construct($message, array $parameters = [], $plural = null, $translationDomain = null)
    {
        $this->message = $message;
        $this->parameters = $parameters;
        $this->plural = $plural;
        $this->translationDomain = $translationDomain;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return int
     */
    public function getPlural()
    {
        return $this->plural;
    }

    /**
     * @param int $plural
     */
    public function setPlural($plural)
    {
        $this->plural = $plural;
    }

    /**
     * @return string
     */
    public function getTranslationDomain()
    {
        return $this->translationDomain;
    }

    /**
     * @param string $translationDomain
     */
    public function setTranslationDomain($translationDomain)
    {
        $this->translationDomain = $translationDomain;
    }

    /**
     * @param TranslatorInterface $translator
     * @return string
     */
    public function getString(TranslatorInterface $translator)
    {
        if (null === $this->plural) {
            $translatedMessage = $translator->trans(
                $this->message,
                $this->parameters,
                $this->translationDomain
            );
        } else {
            try {
                $translatedMessage = $translator->transChoice(
                    $this->message,
                    $this->plural,
                    $this->parameters,
                    $this->translationDomain
                );
            } catch (\InvalidArgumentException $e) {
                $translatedMessage = $translator->trans(
                    $this->message,
                    $this->parameters,
                    $this->translationDomain
                );
            }
        }

        return $translatedMessage;
    }
}
