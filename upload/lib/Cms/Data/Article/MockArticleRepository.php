<?php


namespace Cms\Data\Article;

use Cms\Data\IRepository;


class MockArticleRepository implements IRepository
{
    public function exists($id)
    {
        return $id == 1;
    }
    public function getById($id)
    {
        if ($this->exists($id)) {
            return new Article(array('id' => $id, 'title' => 'Test Article'));
        } else {
            throw new \Exception(sprintf('Record %s does not exist.', $id));
        }
    }
} 