<?php
// src/Controller/ArticlesController.php
namespace App\Controller;

use App\Controller\AppController;

class ArticlesController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
    }

    public function index()
    {
        $articles = $this->paginate($this->Articles);
        $this->set(compact('articles'));
    }

    public function view($slug = null)
    {
        $article = $this->Articles->findBySlug($slug)->firstOrFail();
        $this->set(compact('article'));
    }

    public function add()
    {
        $article = $this->Articles->newEmptyEntity();
        if ($this->request->is('post')) {
            $article = $this->Articles->patchEntity($article, $this->request->getData());

            // user_id の決め打ちは一時的なもので、あとで認証を構築する際に削除されます。
            $article->user_id = 1;

            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to add your article.'));
        }
        $this->set('article', $article);
    }

    public function edit($slug = null)
    {
        $article = $this->Articles->findBySlug($slug)->firstOrFail();

        // PATCH, POST, または PUT リクエストの場合にのみデータを更新
        if ($this->request->is(['patch', 'post', 'put'])) {
            $article = $this->Articles->patchEntity($article, $this->request->getData());
            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been updated.'));
                return $this->redirect(['action' => 'view', $article->slug]);
            }
            $this->Flash->error(__('Unable to update your article.'));
        }

        $this->set(compact('article'));
    }

    public function delete($slug)
    {
        $this->request->allowMethod(['post', 'delete']);

        $article = $this->Articles->findBySlug($slug)->firstOrFail();
        if ($this->Articles->delete($article)) {
            $this->Flash->success(__('The article with id: {0} has been deleted.', h($article->id)));
        } else {
            $this->Flash->error(__('The article with id: {0} could not be deleted.', h($article->id)));
        }
        return $this->redirect(['action' => 'index']);
    }
}
