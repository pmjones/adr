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
                'exception' => $e,
                'page' => $page,
                'paging' => $paging,
            ));
        }
    }

    public function fetchPost($id)
    {
        try {
            $blog = $this->gateway->fetchOneById($id);
            if ($blog) {
                return $this->result->found(array(
                    'blog' => $blog
                ));
            }
            return $this->result->notFound(array(
                'id' => $id
            ));
        } catch (Exception $e) {
            return $this->result->error(array(
                'exception' => $e,
                'id' => $id,
            ));
        }
    }

    public function newPost(array $data)
    {
        return $this->result->newEntity(array(
            'blog' => $this->factory->newEntity($data)
        ));
    }

    public function create(array $data)
    {
        try {
            $blog = $this->gateway->create($data);
            if ($blog) {
                return $this->result->created(array(
                    'blog' => $blog
                ));
            } else {
                return new $this->result->notCreated(array(
                    'blog' => $data,
                ));
            }
        } catch (Exception $e) {
            return $this->result->error(array(
                'exception' => $e,
                'data' => $data,
            ));
        }
    }

    public function update($id, array $data)
    {
        try {
            $blog = $this->gateway->fetchOneById($id);
            if (! $blog) {
                return $this->result->notFound(array(
                    'id' => $id
                ));
            }

            unset($data['id']);
            $blog->setData($data);
            $updated = $this->gateway->update($blog);

            if ($updated) {
                return $this->result->updated(array(
                    'blog' => $blog,
                ));
            } else {
                return $this->result->notUpdated(array(
                    'blog' => $blog,
                ));
            }

        } catch (Exception $e) {
            return $this->result->error(array(
                'exception' => $e,
                'id' => $id,
                'data' => $data,
            ));
        }
    }

    public function delete($id)
    {
        try {
            $blog = $this->gateway->fetchOneById($id);
            if (! $blog) {
                return $this->result->notFound(array(
                    'id' => $id
                ));
            }

            $deleted = $this->gateway->delete($blog);
            if ($deleted) {
                return $this->result->deleted(array(
                    'blog' => $blog,
                ));
            } else {
                return $this->result->notDeleted(array(
                    'blog' => $blog,
                ));
            }
        } catch (Exception $e) {
            return $this->result->error(array(
                'exception' => $e,
                'blog' => $blog,
            ));
        }
    }
}
