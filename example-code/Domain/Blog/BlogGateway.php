<?php
namespace Domain\Blog;

class BlogGateway
{
    public function __construct(ExtendedPdo $pdo, BlogFactory $factory)
    {
        $this->pdo = $pdo;
        $this->factory = $factory;
    }

    public function fetchOneById($id)
    {
        $row = $this->pdo->fetchOne(
            'SELECT * FROM blog WHERE id = :id',
            array('id' => (int) $id)
        );

        if ($row) {
            return $this->factory->newEntity($row);
        }
    }

    public function fetchAllByPage($page = 1, $paging = 10)
    {
        $limit = (int) $paging;
        $offset = ($page - 1) * $limit;
        $rows = $this->pdo->fetchAll(
            "SELECT * FROM blog LIMIT $limit OFFSET $offset"
        );
        if ($rows) {
            return $this->factory->newCollection($rows);
        }
    }

    public function create(BlogEntity $entity)
    {
        $affected = $this->pdo->perform(
            'INSERT INTO blog (
                author,
                title,
                intro,
                body
            ) VALUES (
                :author,
                :title,
                :intro,
                :body
            )',
            $entity->getData()
        );

        if ($affected) {
            $entity->id = $this->pdo->lastInsertId();
        }

        return (bool) $affected;
    }

    public function update(BlogEntity $entity)
    {
        $affected = $this->pdo->perform(
            'UPDATE blog
            SET
                author = :author,
                title = :title,
                intro = :intro,
                body = :body
            WHERE id = :id',
            $entity->getData()
        );
        return (bool) $affected;
    }

    public function delete(BlogEntity $entity)
    {
        $affected = $this->pdo->perform(
            'DELETE FROM blog WHERE id = :id',
            array('id' => $entity->id)
        );
        return (bool) $affected;
    }
}
