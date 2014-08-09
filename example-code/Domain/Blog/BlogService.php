<?php
namespace Domain\Blog;

use Aura\Sql\ExtendedPdo;
use Domain\Status;

// a naive domain service for example purposes only
class BlogService
{
    protected $pdo;

    public function __construct(ExtendedPdo $pdo)
    {
        $this->pdo = $pdo;
    }

    public function fetchAllByPage($page = 1, $paging = 10)
    {
        $collection = array();
        $limit = (int) $paging;
        $offset = ($page - 1) * $limit;

        $rows = $this->pdo->fetchAll(
            "SELECT * FROM blog LIMIT $limit OFFSET $offset"
        );

        foreach ($rows as $row) {
            $collection[] = new BlogEntity($row);
        }

        return $collection;
    }

    public function fetchOneById($id)
    {
        $row = $this->pdo->fetchOne(
            'SELECT * FROM blog WHERE id = :id',
            array('id' => (int) $id)
        );

        if (! $row) {
            return new Status\NotFound($id);
        }

        return new BlogEntity($row);
    }

    public function create(array $data)
    {
        $blog = new BlogEntity($data);

        $result = $this->pdo->perform(
            'INSERT INTO blog (
                author, title, intro, body
            ) VALUES (
                :author, :title, :intro, :body
            )',
            $data
        );

        if (! $result) {
            $blog->setMessages(array('Could not create blog.'));
        } else {
            $blog->id = $this->pdo->lastInsertId();
        }

        return $blog;
    }

    public function updateById($id, array $data)
    {
        $blog = $this->fetchOneById($id);
        if ($blog instanceof Status\NotFound) {
            return $blog;
        }

        unset($data['id']);
        $blog->setData($data);

        $result = $this->pdo->perform(
            'UPDATE blog
            SET
                author = :author,
                title = :title,
                intro = :intro,
                body = :body
            WHERE id = :id',
            $data
        );

        if (! $result) {
            $blog->setMessages(array('Could not update blog.'));
        }

        return $blog;
    }

    public function deleteById($id)
    {
        $blog = $this->fetchOneById($id);
        if ($blog instanceof Status\NotFound) {
            return $blog;
        }

        $result = $this->pdo->perform(
            'DELETE FROM blog WHERE id = :id',
            array('id' => $blog->id)
        );

        if ($result) {
            return new Status\Deleted($blog->id);
        }

        return false;
    }
}
