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
        $query = $this->Articles->find()->contain(['Tags']);
        $articles = $this->paginate($query);
        $this->set(compact('articles'));
    }

    public function view($slug = null)
    {
        $article = $this->Articles
            ->findBySlug($slug)
            ->contain('Tags')
            ->firstOrFail();
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

        // タグのリストを取得してセット
        $tags = $this->Articles->Tags->find('list')->all();
        $this->set('tags', $tags);

        $this->set('article', $article);
    }

    public function edit($slug = null)
    {
        $article = $this->Articles
            ->findBySlug($slug)
            ->contain('Tags') // 関連するタグも取得
            ->firstOrFail();

        // PATCH, POST, または PUT リクエストの場合にのみデータを更新
        if ($this->request->is(['patch', 'post', 'put'])) {
            $article = $this->Articles->patchEntity($article, $this->request->getData());
            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been updated.'));
                return $this->redirect(['action' => 'view', $article->slug]);
            }
            $this->Flash->error(__('Unable to update your article.'));
        }

        $tags = $this->Articles->Tags->find('list')->all();
        $this->set(compact('tags'));
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

    public function tags(...$tags)
    {
        // ArticlesTable を使用してタグ付きの記事を検索します。
        $articles = $this->Articles->find('tagged', [
            'tags' => $tags
        ])
        ->all();

        // 変数をビューテンプレートのコンテキストに渡します。
        $this->set([
            'articles' => $articles,
            'tags' => $tags
        ]);
    }
}
