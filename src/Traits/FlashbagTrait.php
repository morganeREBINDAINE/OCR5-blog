<?php

namespace OCR5\Traits;

trait FlashbagTrait
{
    protected function addFlash($subject, $message)
    {
        $_SESSION['flashbag'][$subject] = $message;
    }
}