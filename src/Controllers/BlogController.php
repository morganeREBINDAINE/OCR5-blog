<?php

namespace OCR5\Controllers;

use OCR5\Repository\PostRepository;
use OCR5\Services\BackManager;
use OCR5\Services\EntityManager;
use OCR5\Services\FormManager;

class BlogController extends Controller
{
    public function home()
    {
        return $this->render('blog/home');
    }

    public function postsList()
    {
        $backManager = new BackManager();
        $pagination = $backManager->getPagination('post', 2);

        return $this->render('blog/posts-list', [
            'posts' => $pagination['posts'],
            'page' => $pagination['pages'],
        ]);
    }

    public function showPost($id)
    {
        $backManager = new BackManager();
        $formManager = new FormManager();
        $postRepository = new PostRepository();

        $post = $postRepository->getValid($id);

        $pagination = $backManager->getPaginatedCommentsByPost(4, $id);

        if (empty($post)) {
            return $this->error('Aucun article ne correspond à cet article.');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST'
            && isset($_POST['name'], $_POST['email'], $_POST['content'], $_POST['id'])
        ) {
            $_POST['original_id'] = $id;

            if (false === $formManager->checkCommentFormErrors($_POST)) {
                (new EntityManager())->createComment($_POST) ?
                    $this->addFlash('success', 'Votre commentaire a été ajouté: il doit être validé avant d\'être publié.')
                    : $this->addFlash('error', 'Il y a eu un problème lors de l\'ajout de l\'article.');
            }
        }

        return $this->render('blog/post-single', [
            'post' => $post,
            'comments' => $pagination['comments'],
            'page' => $pagination['pages']
        ]);
    }

}
