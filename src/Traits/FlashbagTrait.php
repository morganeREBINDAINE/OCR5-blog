<?php

namespace OCR5\Traits;

use OCR5\App\Session;

trait FlashbagTrait
{
    protected function addFlash($subject, $message)
    {
        Session::set('flashbag', $message, $subject);
    }
}