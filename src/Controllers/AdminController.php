<?php

namespace OCR5\Controllers;

use OCR5\App\App;
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
        $contributorsRequests = $this->isAdmin() ? $this->createTable('user') : null;
        $postsRequests = $this->isAdmin() ? $backManager->createTable('post') : null;
        $commentsRequests = $backManager->createTable('comment');

        return $this->render('back/profile', [
            'contributorsRequests' => $contributorsRequests,
            'postsRequests' => $postsRequests,
            'commentsRequests' => $commentsRequests,
        ]);
    }

    public function handleEntities($entity)
    {
        if(false === $this->isAdmin()) {
            return App::error404();
        }
        switch ($entity) {
            case 'commentaires':
                $entity = 'comment';
                break;
            case 'redacteurs':
                $entity = 'user';
                break;
            case 'articles':
                $entity = 'post';
                break;
            default:
                return App::error404();
        }

        $table = (new BackManager())->createTable($entity, true);

        return $this->render('back/list', [
            'form' => $table
        ]);
    }

    public function actionEntities()
    {
        if (isset($_POST['token'], $_POST['action'])) {
            $token = $_POST['token'];
            list($entity, $id, $hash) = explode('-', $token);

            if (password_verify($id= (int)base64_decode($id), $hash)) {
                $backManager = new BackManager();
                $className = '\OCR5\Entities\\'.ucfirst($entity);

                switch ($_POST['action']) {
                    case 'accepter':
                        $backManager->handleEntity($entity, $id, 1);
                        header('location:'.$_SESSION['last_page']);
                        break;
                    case 'refuser':
                    case 'supprimer':
                        $backManager->handleEntity($entity, $id, 2);
                        header('location:'.$_SESSION['last_page']);
                        break;
                    default:
                        App::error404();
                }
            }

            return $this->error('Il y a eu un problème lors de la manipulation des données. Veuillez ré-essayer.');
        }
    }

    public function writePost()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['content'], $_POST['chapo'])) {
            $em = new FormManager();
            $image = isset($_FILES['image']) ? $em->createImage($_FILES['image']) : null;

            if (false === $em->checkPostFormErrors($_POST, $image)) {
                (new EntityManager())->createPost($_POST, $image) ?
                    $this->addFlash('success', 'Votre article a été ajouté et doit passer en validation par l\'administratrice avant d\'être publié.')
                    : $this->addFlash('error', 'Il y a eu un problème lors de l\'ajout de l\'article.');
            }
        }
        return $this->render('back/post-form');
    }

    public function myArticles() {
        $posts = (new BackManager())->createTable('post', true, $_SESSION['user']->getId());
//        var_dump($posts);die;
        return $this->render('back/my-posts', [
            'posts' => $posts
        ]);
    }

    private function isAdmin() {
        return $_SESSION['user']->getRole() === 'administrator';
    }
}
