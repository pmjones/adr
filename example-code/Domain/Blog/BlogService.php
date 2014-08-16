<?php
namespace Domain\Blog;

use Domain\ResultFactory;
use Exception;

class BlogService
{
    protected $gateway;
    protected $result;

    public function __construct(
        BlogGateway $gateway,
        BlogFactory $factory,
        ResultFactory $result
    ) {
        $this->gateway = $gateway;
        $this->factory = $factory;
        $this->result = $result;
    }

    public function fetchPage($page = 1, $paging = 10)
    {
        try {
            $collection = $this->gateway->fetchAllByPage($page, $paging);
            if ($collection) {
                return $this->result->found(array(
                    'collection' => $collection,
                ));
            } else {
                return $this->result->notFound(array(
                    'collection' => $collection,
                ));
            }
        } catch (Exception $e) {
            return $this->result->error(array(
                'exception' => $e
            ));
        }
    }

    public function fetchPost($id)
    {
        try {
            $entity = $this->gateway->fetchOneById($id);
            if ($entity) {
                return $this->result->found(array(
                    'blog' => $entity
                ));
            }
            return $this->result->notFound(array(
                'id' => $id
            ));
        } catch (Exception $e) {
            return $this->result->error(array(
                'exception' => $e
            ));
        }
    }

    public function newPost(array $data)
    {
        $entity = $this->factory->newEntity($data);
        return $this->result->newEntity(array(
            'blog' => $entity
        ));
    }

    public function create(array $data)
    {
        try {
            $entity = $this->gateway->create($data);
            if ($entity) {
                return $this->result->created(array(
                    'blog' => $entity
                ));
            } else {
                return new $this->result->notCreated(array(
                    'blog' => $data,
                ));
            }
        } catch (Exception $e) {
            return $this->result->error(array(
                'exception' => $e
            ));
        }
    }

    public function update($id, array $data)
    {
        try {
            $entity = $this->gateway->fetchOneById($id);
            if (! $entity) {
                return $this->result->notFound(array(
                    'id' => $id
                ));
            }

            unset($data['id']);
            $entity->setData($data);
            $updated = $this->gateway->update($entity);

            if ($updated) {
                return $this->result->updated(array(
                    'blog' => $entity,
                ));
            } else {
                return $this->result->notUpdated(array(
                    'blog' => $entity,
                ));
            }

        } catch (Exception $e) {
            return $this->result->error(array(
                'exception' => $e
            ));
        }
    }

    public function delete($id)
    {
        try {
            $entity = $this->gateway->fetchOneById($id);
            if (! $entity) {
                return $this->result->notFound(array(
                    'id' => $id
                ));
            }

            $deleted = $this->gateway->delete($entity);
            if ($deleted) {
                return $this->result->deleted(array(
                    'blog' => $entity,
                ));
            } else {
                return $this->result->notDeleted(array(
                    'blog' => $entity,
                ));
            }
        } catch (Exception $e) {
            return $this->result->error(array(
                'exception' => $e
            ));
        }
    }
}
