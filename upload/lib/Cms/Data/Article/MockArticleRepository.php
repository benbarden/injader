<?php


namespace Cms\Data\Article;

use Cms\Data\IRepository,
    Cms\Exception\Data\DataException;


class MockArticleRepository implements IRepository
{
    public function exists($id)
    {
        return $id == 1;
    }
    public function getById($id)
    {
        if ($this->exists($id)) {
            return new Article(
                array(
                    'id' => $id,
                    'title' => 'Test Article',
                    'create_date' => '2009-12-31 09:30:00'
                )
            );
        } else {
            throw new DataException(sprintf('Record %s does not exist.', $id));
        }
    }
} 