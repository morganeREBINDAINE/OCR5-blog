<?php

namespace OCR5\Controllers;

use OCR5\App\App;
use OCR5\App\Post;
use OCR5\Handler\CommentHandler;
use OCR5\Handler\PostHandler;
use OCR5\Services\BackManager;
use OCR5\Services\FormManager;

class BlogController extends Controller
{
    public function home()
    {
        if (App::isPostMethod()
            && Post::isset(['name', 'email', 'message'])
            && false === (new FormManager())->checkContactForm(Post::get())
        ) {
            $headers  = 'From: adresse de l expediteur'."\r\n";
            $headers .= 'Reply-To: '.Post::get('email')."\r\n";
            $headers .= 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
            mail('mrebindaine@hotmail.com', 'Message de '.Post::get('name'), 'Vous avez recu un message de la part de ' . Post::get('name') . ' : ' . Post::get('message'), $headers);
            $this->addFlash('success', 'Votre message a bien été envoyé.');
        }
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

    public function showPost($identifier)
    {
        $backManager = new BackManager();
        $formManager = new FormManager();
        $postHandler = new PostHandler();

        $post = $postHandler->getValid($identifier);

        $pagination = $backManager->getPagination('comment', 4, 'post_id = '. (int)$identifier);

        if (empty($post)) {
            return $this->error('Aucun article ne correspond à cet article.');
        }

        if (App::isPostMethod()
            && Post::isset(['name', 'email', 'content', 'id'])
        ) {
            if (false === $formManager->checkCommentFormErrors(Post::get(), $identifier)) {
                (new CommentHandler())->create(Post::get()) ?
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
