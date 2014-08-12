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
                return $this->result->found($collection);
            } else {
                return $this->result->notFound($collection);
            }
        } catch (Exception $e) {
            return $this->result->error($e);
        }
    }

    public function fetchPost($id)
    {
        try {
            $entity = $this->gateway->fetchOneById($id);
            if ($entity) {
                return $this->result->found($entity);
            }
            return $this->result->notFound($id);
        } catch (Exception $e) {
            return $this->result->error($e);
        }
    }

    public function newPost(array $data)
    {
        $entity = $this->factory->newEntity($data);
        return $this->result->newInstance($entity);
    }

    public function create(array $data)
    {
        try {
            $entity = $this->gateway->create($data);
            if ($entity) {
                return $this->result->created($entity);
            } else {
                return new $this->result->notCreated($data);
            }
        } catch (Exception $e) {
            return $this->result->error($e, $data);
        }
    }

    public function update($id, array $data)
    {
        try {
            $entity = $this->gateway->fetchOneById($id);
            if (! $entity) {
                return $this->result->notFound($id);
            }

            unset($data['id']);
            $entity->setData($data);
            $updated = $this->gateway->update($entity);

            if ($updated) {
                return $this->result->updated($entity);
            } else {
                return $this->result->notUpdated($entity);
            }

        } catch (Exception $e) {
            return $this->result->error($e, $entity);
        }
    }

    public function delete($id)
    {
        try {
            $entity = $this->gateway->fetchOneById($id);
            if (! $entity) {
                return $this->result->notFound($id);
            }

            $deleted = $this->gateway->delete($entity);
            if ($deleted) {
                return $this->result->deleted($entity);
            } else {
                return $this->result->notDeleted($entity);
            }
        } catch (Exception $e) {
            return $this->result->error($e, $entity);
        }
    }
}
