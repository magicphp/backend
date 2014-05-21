<?php
    /**
     * Initializer module
     * 
     * @package     MagicPHP Backend
     * @author      André Ferreira <andrehrf@gmail.com>
     */

    $oModule = Modules::Append("backend", __DIR__ . SP);
    $oModule->Set("name", "MagicPHP Backend")
            ->Set("author", "André Ferreira <andre@magicphp.org>")
            ->Set("website", "https://magicphp.org")
            ->Start();