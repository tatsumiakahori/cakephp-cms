<?php
// src/Model/Table/ArticlesTable.php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Utility\Text;
use Cake\Event\EventInterface;
use Cake\Validation\Validator as Validater;

class ArticlesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
    }

    public function beforeSave(EventInterface $event, $entity, $options)
    {
        if ($entity->isNew() && !$entity->slug) {
            $sluggedTitle = Text::slug($entity->title);
            // スラグをスキーマで定義されている最大長に調整
            $entity->slug = substr($sluggedTitle, 0, 191);
        }
    }

    public function validationDefault(Validater $validator): Validater
    {
        return $validator
            ->notEmptyString('title', 'タイトルは必須項目です。')
            ->minLength('title', 10, 'タイトルは10文字以上で入力してください。')
            ->maxLength('title', 255, 'タイトルは255文字以内で入力してください。')

            ->notEmptyString('body', '本文は必須項目です。')
            ->minLength('body', 10, '本文は10文字以上で入力してください。');
    }
}
