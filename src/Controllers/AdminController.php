<?php

namespace OCR5\Controllers;

use OCR5\Services\BackManager;
use OCR5\Services\ContributorsManager;

class AdminController extends Controller
{
    public function __construct()
    {
        if (false === $this->isConnected()) {
            header("HTTP/1.0 401 Unauthorized");
            header('location: http://blog/');
        }
    }

    public function profile()
    {
        $backManager = new BackManager();
        $contributorsRequests = $backManager->getContributorsRequests();
        $articlesRequests = $backManager->getArticlesRequests();
        $commentsRequests = $backManager->getCommentsRequests();

        return $this->render('back/profile', [
            'contributorsRequests' => $contributorsRequests,
            'articlesRequests' => $articlesRequests,
            'commentsRequests' => $commentsRequests,
//            'test' => $test
        ]);
    }

    public function contributorsList()
    {
        $contributorsManager = new ContributorsManager();
        $contributors = $contributorsManager->getValidsContributors();

        return $this->render('back/list-contributors', [
            'contributors' => $contributors
        ]);
    }

    public function contributorsRequests()
    {
        if (isset($_POST['id'], $_POST['hash'], $_POST['action'])
            && password_verify($_POST['id'], $_POST['hash'])) {
            $contributorsManager = new ContributorsManager();
            switch ($_POST['action']) {
                case 'accepter':
                    if ($contributorsManager->handleContributor($_POST['id'], 1)) {
                        $this->addFlash('contributor', 'L\'éditeur a été accepté.');
                    }
                    header('location: http://blog/profil');

                    break;
                case 'refuser':
                    if ($contributorsManager->handleContributor($_POST['id'], 2)) {
                        $this->addFlash('contributor', 'L\'éditeur a été refusé.');
                    }
                    header('location: http://blog/profil');
                    break;
                case 'supprimer':
                    if ($contributorsManager->handleContributor($_POST['id'], 2)) {
                        $this->addFlash('contributor', 'L\'éditeur a été supprimé.');
                    }
                    header('location: http://blog/gestion-redacteurs');
                    break;
            }
        }

    }
}
