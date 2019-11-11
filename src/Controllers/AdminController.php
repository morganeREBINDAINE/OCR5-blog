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
        if (false === $this->isAdmin()) {
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

                switch ($_POST['action']) {
                    case 'accepter':
                        $backManager->handleEntity($entity, $id, 1);
                        header('location:'.$_SESSION['last_page']);
                        break;
                    case 'modifier':
                        header('location: http://blog/modifier-article-'.$id);
                        exit;
                    break;
                    case 'refuser':
                        $backManager->handleEntity($entity, $id, 2);
                        // no break
                    case 'supprimer':
                        $backManager->handleEntity($entity, $id, 3);
                        break;
                    default:
                        App::error404();
                }
                header('location:'.$_SESSION['last_page']);
            }

            return $this->error('Il y a eu un problème lors de la manipulation des données. Veuillez ré-essayer.');
        }
    }

    public function writePost($id = null)
    {
        if ($id) {
            $post = (new BackManager())->getPost($id);

            if ($post === false || $post->getUser() !== $_SESSION['user']->getId()) {
                return $this->error('Cet article n\'existe pas ou bien vous n\'avez pas de droits dessus.');
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['content'], $_POST['chapo'], $_FILES['image'])) {
            $em = new FormManager();
            $image = $em->createImage($_FILES['image']);

            if (false === $em->checkPostFormErrors($_POST, $image)) {
                $entityManager = new EntityManager();
                if ($id) {
                    if (isset($_POST['keep-image']) && $_POST['keep-image'] === true) {
                        $image['image'] = $post->getImage();
                        $image['extension'] = $post->getExtension();
                    }
                    $entityManager->updatePost($_POST, $image, $id) ?
                        $this->addFlash('success', 'Votre article a été modifié.')
                        : $this->addFlash('error', 'Il y a eu un problème lors de la modification de votre article.');
                    header("Refresh:0");
                    exit();
                }
                $entityManager->createPost($_POST, $image) ?
                    $this->addFlash('success', 'Votre article a été ajouté et doit passer en validation par l\'administratrice avant d\'être publié.')
                    : $this->addFlash('error', 'Il y a eu un problème lors de l\'ajout de l\'article.');
            }
        }
        return $this->render('back/post-form', [
            'post' => false === is_null($id) ? $post : $_POST
        ]);
    }

    public function myArticles()
    {
        $posts = (new BackManager())->createTable('post', true, $_SESSION['user']->getId());

        return $this->render('back/my-posts', [
            'posts' => $posts,
            'title' => 'Mes articles'
        ]);
    }

    private function isAdmin()
    {
        return $_SESSION['user']->getRole() === 'administrator';
    }
}
