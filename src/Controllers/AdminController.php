<?php

namespace OCR5\Controllers;

use OCR5\App\App;
use OCR5\App\Session;
use OCR5\App\Post;
use OCR5\Handler\PostHandler;
use OCR5\Services\BackManager;
use OCR5\Services\FormManager;

class AdminController extends Controller
{
    public function __construct()
    {
        if (false === $this->isConnected()) {
            $this->redirect('/');
        }
    }

    public function profile()
    {
        $backManager = new BackManager();
        $contributorsRequests = $this->isAdmin() ? $backManager->createTable('user') : null;
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
        if (Post::get('token') && Post::get('action')) {
            $token = Post::get('token');
            list($entity, $id, $hash) = explode('-', $token);

            if (password_verify($id= (int)base64_decode($id), $hash)) {
                $handler = 'OCR5\Handler\\'.ucfirst($entity).'Handler';
                $handler = new $handler();

                switch (Post::get('action')) {
                    case 'accepter':
                        $handler->changeStatus($id, 1);
                        $this->redirect(Session::get('last_page'));
                    case 'modifier':
                        $this->redirect('/modifier-article-'.$id);
                        break;
                    case 'refuser':
                        $handler->changeStatus($id, 2);
                        break;
                    case 'supprimer':
                        $handler->changeStatus($id, 3);

                        break;
                    default:
                        App::error404();
                }
                $this->redirect(Session::get('last_page'));
            }

            return $this->error('Il y a eu un problème lors de la manipulation des données. Veuillez ré-essayer.');
        }
    }

    public function writePost($id = null)
    {
        $postHandler = new PostHandler();
        $post = $postHandler->get($id) ?: null;

        if (($id) && ($post === false || ($this->isAdmin() === false && $post->getUser() !== Session::get('user')->getId()))) {
            return $this->error('Cet article n\'existe pas ou bien vous n\'avez pas de droits dessus.');
        }

        if (App::isPostMethod()
            && null !== Post::get('title')
            && null !== Post::get('content')
            && null !== Post::get('chapo')
            && null !== Post::getFile('image')
        ) {
            $em = new FormManager();
            $formData = Post::get();

            $image = $em->createImage(Post::getFile('image'), $post);

            if (false === $em->checkPostFormErrors($formData, $image)) {
                if ($id) {
                    $postHandler->change($id, $formData, $image);
                    $this->addFlash('success', 'Votre article a été modifié.');
                    header("Refresh:0");
                    exit();
                }

                $formData['img'] = $image;
                $postHandler->create($formData, $image);
                $this->addFlash('success', 'Votre article a été ajouté et doit être validé par l\'administratrice avant d\'être publié.');
                $this->redirect('/profil');
            }
        }

        return $this->render('back/post-form', [
            'post' => null !== $id ? $post : $formData

        ]);
    }

    public function myArticles()
    {
        $posts = (new BackManager())->createTable('post', true, Session::get('user')->getId());

        return $this->render('back/my-posts', [
            'posts' => $posts,
            'title' => 'Mes articles en ligne'
        ]);
    }

    private function isAdmin()
    {
        return Session::get('user')->getRole() === 'administrator';
    }
}
