<?php
/**
 * Created by PhpStorm.
 * User: bumz
 * Date: 2/13/14
 * Time: 5:33 PM
 */

namespace Requestum\EmailSenderBundle\Swift;


use DirectoryIterator;
use Swift_Transport;

class FileFailTolerantSpool extends \Swift_FileSpool
{
    private $_path;

    private $timeout = 900;

    public function __construct($path)
    {
        parent::__construct($path);

        $this->_path = $path;
    }

    /**
     * Execute a recovery if for any reason a process is sending for too long.
     *
     * @param integer $timeout in second Defaults is for very slow smtp responses
     */
    public function recover($timeout = 900)
    {
        $this->timeout = $timeout;

        foreach (new DirectoryIterator($this->_path) as $file) {
            $file = $file->getRealPath();

            if (substr($file, - 16) == '.message.sending') {
                if ($this->checkTimeExpired($file, $timeout)) {
                    rename($file, substr($file, 0, - 8));
                }
            }
        }
    }

    public function flushQueue(Swift_Transport $transport, &$failedRecipients = null)
    {
        $directoryIterator = new DirectoryIterator($this->_path);

        /* Start the transport only if there are queued files to send */
        if (!$transport->isStarted()) {
            foreach ($directoryIterator as $file) {
                if (substr($file->getRealPath(), -8) == '.message' || substr($file->getRealPath(), -6) == '.error') {
                    $transport->start();
                    break;
                }
            }
        }

        $failedRecipients = (array) $failedRecipients;
        $count = 0;
        $time = time();
        foreach ($directoryIterator as $file) {
            $file = $file->getRealPath();

            // try to send previously message that caused error. If not - remove it.
            if (substr($file, -6) == '.error') {

                // timeout not expired
                if (!$this->checkTimeExpired($file, $this->timeout)) {
                    continue;
                }

                try {
                    $this->sendOut($file, $transport, $failedRecipients, true);
                } catch (\Swift_TransportException $e) {

                    unlink($file.'.sending');

                }

                continue;
            }

            if (substr($file, -8) != '.message') {
                continue;
            }

            if ($addCount = $this->sendOut($file, $transport, $failedRecipients)) {
                $count += $addCount;
            } else {
                continue;
            }

            if ($this->getMessageLimit() && $count >= $this->getMessageLimit()) {
                break;
            }

            if ($this->getTimeLimit() && (time() - $time) >= $this->getTimeLimit()) {
                break;
            }
        }

        return $count;
    }

    private function sendOut($file, Swift_Transport $transport, &$failedRecipients = null, $throwException = false)
    {
        $count = 0;
        /* We try a rename, it's an atomic operation, and avoid locking the file */
        if (rename($file, $file.'.sending')) {
            $message = unserialize(file_get_contents($file.'.sending'));

            try {
                $count += $transport->send($message, $failedRecipients);

                unlink($file.'.sending');
            } catch (\Swift_TransportException $e) {

                if ($throwException) {
                    throw $e;
                }

                rename($file.'.sending', $file.'.sending.error');

                $failedRecipients = array_merge(
                    $failedRecipients,
                    array_keys((array) $message->getTo()),
                    array_keys((array) $message->getCc()),
                    array_keys((array) $message->getBcc())
                );

                return false;
            }

        } else {
            /* This message has just been catched by another process */
            return false;
        }

        return $count;
    }

    /**
     * @param string $file    path to spool file
     * @param int    $timeout timeout in seconds
     *
     * @return bool
     */
    private function checkTimeExpired($file, $timeout)
    {
        $lockedTime = filectime($file);
        return (time() - $lockedTime) > $timeout;
    }

} 