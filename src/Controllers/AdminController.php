<?php

namespace OCR5\Controllers;

use OCR5\App\App;
use OCR5\Entities\Post;
use OCR5\Handler\PostHandler;
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
        $contributorsRequests = $this->isAdmin() ? $backManager->createTable('user') : null;
        $postsRequests = $this->isAdmin() ? $backManager->createTable('post') : null;

        $commentsRequests = $backManager->createTable('comment');
//        var_dump($commentsRequests);die;

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
                $title = 'Liste des commentaires en ligne';
                $entity = 'comment';
                break;
            case 'redacteurs':
                $title = 'Liste des rédacteurs valides';
                $entity = 'user';
                break;
            case 'articles':
                $title = 'Liste des articles en ligne';
                $entity = 'post';
                break;
            default:
                return App::error404();
        }

        $table = (new BackManager())->createTable($entity, true);

        return $this->render('back/list', [
            'title' => $title,
            'form' => $table
        ]);
    }

    public function actionEntities()
    {
        if (isset($_POST['token'], $_POST['action'])) {
            $token = $_POST['token'];
            list($entity, $id, $hash) = explode('-', $token);

            if (password_verify($id= (int)base64_decode($id), $hash)) {
                $repository = 'OCR5\Repository\\'.ucfirst($entity).'Repository';
                $repository = new $repository();

                switch ($_POST['action']) {
                    case 'accepter':
                        $repository->changeStatus($id, 1);
                        $this->redirect($_SESSION['last_page']);
                    case 'modifier':
                        $this->redirect('/modifier-article-'.$id);
                    break;
                    case 'refuser':
                        $repository->changeStatus($id, 2);
                        break;
                    case 'supprimer':
                        $repository->changeStatus($id, 3);

                        break;
                    default:
                        App::error404();
                }
                $this->redirect($_SESSION['last_page']);
            }

            return $this->error('Il y a eu un problème lors de la manipulation des données. Veuillez ré-essayer.');
        }
    }

    public function writePost($id = null)
    {
        $repository = new PostHandler();
        if ($id) {
            $post = $repository->get($id);

            if ($post === false || ($this->isAdmin() === false && $post->getUser() !== $_SESSION['user']->getId())) {
                return $this->error('Cet article n\'existe pas ou bien vous n\'avez pas de droits dessus.');
            }
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['content'], $_POST['chapo'], $_FILES['image'])) {
            $em = new FormManager();

            // @todo changer
            if (isset($_POST['keep-image']) && $_POST['keep-image'] === 'on') {
                $image['extension'] = $post->getExtension();
                $image['name'] = $post->getImage();
                $image['status'] = 'keep';
            } else {
                $image = $em->createImage($_FILES['image']);
            }

            if (false === $em->checkPostFormErrors($_POST, $image)) {
                if ($id) {
                    $repository->change($id, $_POST, $image);
                    $this->addFlash('success', 'Votre article a été modifié.');
                    header("Refresh:0");
                    exit();
                }

                $_POST['img'] = $image;
                $repository->create($_POST);
                $this->addFlash('success', 'Votre article a été ajouté et doit être validé par l\'administratrice avant d\'être publié.');
                $this->redirect('/profil');
            }
        }

        return $this->render('back/post-form', [
            'post' => null !== $id ? $post : $_POST
        ]);
    }

    public function myArticles()
    {
        $posts = (new BackManager())->createTable('post', true, $_SESSION['user']->getId());

        return $this->render('back/my-posts', [
            'posts' => $posts,
            'title' => 'Mes articles en ligne'
        ]);
    }

    private function isAdmin()
    {
        return $_SESSION['user']->getRole() === 'administrator';
    }
}
