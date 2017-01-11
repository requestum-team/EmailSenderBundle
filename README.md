requestum/email-sender-bundle
===

A Symfony Bundle created on August, 2016.

EmailSenderBundle is bundle for Symfony.

**Installation:**
---
Via Composer

    composer require requestum/email-sender-bundle "dev-master"

Add `new Requestum\EmailSenderBundle\RequestumEmailSenderBundle()` to you `app/AppKernel.php`.

    <?php
    // app/AppKernel.php

    // ...
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                // ...

                new Requestum\EmailSenderBundle\RequestumEmailSenderBundle(),
            );

            // ...
        }

        // ...
    }