<?php

namespace OCR5\Controllers;

class BackController extends Controller
{
    public function profile() {
        return $this->render('back/profile');
    }
}