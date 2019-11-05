<?php

namespace OCR5\Controllers;

use OCR5\App\App;
use OCR5\Services\BackManager;
use OCR5\Services\UserManager;
use Twig\TwigFunction;

class BlogController extends Controller
{
    public function home()
    {
        return $this->render('blog/home');
    }

    public function postsList() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        $backManager = new BackManager();
        $posts = $backManager->getPaginatedPosts($page, 2);

        return $this->render('blog/posts-list', [
            'posts' => $posts,
            'page' => $page
        ]);
    }

    public function showPost($id) {
        $post = (new BackManager())->getValid('post', $id);

        if (empty($post)) {
            return $this->error('Aucun article ne correspond à cet article.');
        }

        return $this->render('blog/post-single', [
            'post' => $post,
        ]);
    }
}
