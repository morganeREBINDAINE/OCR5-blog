<?php

namespace OCR5\Controllers;

use OCR5\Entities\Post;
use OCR5\Services\BackManager;
use OCR5\Services\EntityManager;
use OCR5\Services\EntitiesManager;
use OCR5\Services\FormManager;
use OCR5\Services\PostManager;
use Verot\Upload\Upload;

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
        $contributorsRequests = $backManager->createTable('user');
        $postsRequests = $backManager->createTable('post');

        $commentsRequests = null;

        return $this->render('back/profile', [
            'contributorsRequests' => $contributorsRequests,
            'postsRequests' => $postsRequests,
            'commentsRequests' => $commentsRequests,
        ]);
    }

    public function contributorsHandler()
    {
        $form = (new BackManager())->createTable('user', true);

        return $this->render('back/list', [
            'form' => $form
        ]);
    }

    public function postsHandler()
    {
        $form = (new BackManager())->createTable('post', true);

        return $this->render('back/list', [
            'form' => $form
        ]);
    }

    public function handleEntities()
    {
        if (isset($_POST['token'], $_POST['action'])) {
            $token = $_POST['token'];
            list($entity, $id, $hash) = explode('-', $token);

            if (password_verify($id= (int)base64_decode($id), $hash)) {
                $backManager = new BackManager();
                $className = '\OCR5\Entities\\'.ucfirst($entity);

                switch ($_POST['action']) {
                    case 'accepter':
                        if ($backManager->handleEntity($entity, $id, 1)) {
                            $traduction = $className::REQUESTED_TRADUCTION;
                        }
                        header('location:'.$_SESSION['last_page']);
                        break;
                    case 'refuser':
                    case 'supprimer':
                        if ($backManager->handleEntity($entity, $id, 2)) {
                            $traduction = $className::REQUESTED_TRADUCTION;
                        }
                        header('location:'.$_SESSION['last_page']);
                        break;
                    default:
                        die('set this case in AdminController::handleEntities()');
                }
            }

            return $this->error('Il y a eu un problème lors de la manipulation des données. Veuillez ré-essayer.');
        }
    }

    public function writePost()
    {
        if ($_SERVER['REQUEST_METHOD'] && isset($_POST['title'], $_POST['content'], $_POST['chapo'])) {
            $image['file'] = isset($_FILES['image']) ? $_FILES['image'] : null;
            $image['name'] = $_SESSION['user']->getUsername() . time();

            $em = new FormManager();
            if (false === $em->checkPostFormErrors($_POST, $image)) {
                (new EntityManager())->createPost($_POST, $image['name']) ?
                    $this->addFlash('success', 'Votre article a été ajouté.')
                    : $this->addFlash('error', 'Il y a eu un problème lors de l\'ajout de l\'article.');
            }
        }
        return $this->render('back/post-form');
    }
}
