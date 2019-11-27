<?php

namespace OCR5\Controllers;

use OCR5\Handler\CommentHandler;
use OCR5\Handler\PostHandler;
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
        $pagination = (new BackManager())->getPagination('post', 2);

        return $this->render('blog/posts-list', [
            'posts' => $pagination['posts'],
            'page' => $pagination['pages'],
        ]);
    }

    public function showPost($id)
    {
        $backManager = new BackManager();
        $formManager = new FormManager();
        $postHandler = new PostHandler();

        $post = $postHandler->getValid($id);

        $pagination = $backManager->getPagination('comment', 4, 'post_id = '. (int)$id);
//        var_dump($pagination);

        if (empty($post)) {
            return $this->error('Aucun article ne correspond à cet article.');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST'
            && isset($_POST['name'], $_POST['email'], $_POST['content'], $_POST['id'])
        ) {
            $_POST['original_id'] = $id;

            if (false === $formManager->checkCommentFormErrors($_POST)) {
                (new CommentHandler())->create($_POST) ?
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
