<?php

namespace src\controllers\api;

use src\models\Article;
use src\exceptions\NotFoundException;
use src\exceptions\UnauthorizedException;
use src\exceptions\ForbiddenException;
use src\exceptions\InvalidArgumentException;

class ArticlesApiController extends ApiController
{
    public function all()
    {
        $articles = Article::findAll();
        $this->view->displayJson(['articles' => $articles]);
    }

    public function view($id)
    {
        $article = Article::getById($id);
        if ($article === null) {
            throw new NotFoundException();
        }

        $this->view->displayJson(['article' => $article]);
    }

      public function add()
    {
        if ($this->user === null) {
            throw new UnauthorizedException();
        }

        $input = $this->getInputData() ?? [];
        $articleFromRequest = isset($input['articles'][0]) ? $input['articles'][0] : $input;

        try {
            $article = Article::create($articleFromRequest, ['size' => 0], $this->user);
            $this->view->displayJson([
                'result' => 'OK',
                'id' => $article->getId(),
                'author_id' => $this->user->getId()
            ]);
        } catch (\InvalidArgumentException | InvalidArgumentException $e) {
            http_response_code(400);
            $this->view->displayJson(['error' => $e->getMessage()]);
        }
    }

     public function edit($id)
    {
   
        if ($this->user === null) {
            throw new UnauthorizedException();
        }

        $article = Article::getById($id);
        if ($article === null) {
            throw new NotFoundException();
        }

        if ($article->getAuthorId() !== $this->user->getId()) {
            throw new ForbiddenException();
        }

        $input = $this->getInputData() ?? [];
        $articleFromRequest = isset($input['articles'][0]) ? $input['articles'][0] : $input;

        try {
            $article->updateFromArray($articleFromRequest, ['size' => 0]);
            $this->view->displayJson(['result' => 'OK']);
        } catch (\InvalidArgumentException | InvalidArgumentException $e) {
            http_response_code(400);
            $this->view->displayJson(['error' => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        if ($this->user === null) {
            throw new UnauthorizedException();
        }
        $article = Article::getById($id);
        if ($article === null) {
            throw new NotFoundException();
        }

        if ($article->getAuthorId() !== $this->user->getId()) {
            throw new ForbiddenException();
        }

        $article->delete();

        $this->view->displayJson(['result' => 'OK']);
    }

    public function search()
    {
        if (empty($_GET['q'])) {
            $this->view->renderHtml('articles/search.php');
            return;
        } else {
            $articles = Article::searchByName($_GET['q']);
            $this->view->renderHtml('articles/search.php', ['articles' => $articles]);
        }
    }
}
