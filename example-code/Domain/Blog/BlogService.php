<?php
namespace Domain\Blog;

use Domain\PayloadFactory;
use Exception;

class BlogService
{
    protected $filter;
    protected $gateway;
    protected $factory;
    protected $payload;

    public function __construct(
        BlogFilter $filter,
        BlogGateway $gateway,
        BlogFactory $factory,
        PayloadFactory $payload
    ) {
        $this->filter = $filter;
        $this->gateway = $gateway;
        $this->factory = $factory;
        $this->payload = $payload;
    }

    public function fetchPage($page = 1, $paging = 10)
    {
        try {

            $collection = $this->gateway->fetchAllByPage($page, $paging);
            if (! $collection) {
                return $this->payload->notFound(array(
                    'collection' => $collection,
                ));
            }

            return $this->payload->found(array(
                'collection' => $collection,
            ));

        } catch (Exception $e) {

            return $this->payload->error(array(
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
            if (! $blog) {
                return $this->payload->notFound(array(
                    'id' => $id
                ));
            }

            return $this->payload->found(array(
                'blog' => $blog
            ));

        } catch (Exception $e) {

            return $this->payload->error(array(
                'exception' => $e,
                'id' => $id,
            ));

        }
    }

    public function newPost(array $data)
    {
        return $this->payload->newEntity(array(
            'blog' => $this->factory->newEntity($data)
        ));
    }

    public function create(array $data)
    {
        try {

            // instantiate a new entity
            $blog = $this->factory->newEntity($data);

            // validate the entity
            if (! $this->filter->forInsert($blog)) {
                return $this->payload->notValid(array(
                    'blog' => $blog,
                    'messages' => $this->filter->getMessages()
                ));
            }

            // insert the entity
            if (! $this->gateway->create($blog)) {
                return new $this->payload->notCreated(array(
                    'blog' => $blog,
                ));
            }

            // success
            return $this->payload->created(array(
                'blog' => $blog,
            ));

        } catch (Exception $e) {

            return $this->payload->error(array(
                'exception' => $e,
                'data' => $data,
            ));

        }
    }

    public function update($id, array $data)
    {
        try {

            // fetch the entity
            $blog = $this->gateway->fetchOneById($id);
            if (! $blog) {
                return $this->payload->notFound(array(
                    'id' => $id
                ));
            }

            // set data in the entity; do not overwrite existing $id
            unset($data['id']);
            $blog->setData($data);

            // validate the entity
            if (! $this->filter->forUpdate($blog)) {
                return $this->payload->notValid(array(
                    'blog' => $blog,
                    'messages' => $this->filter->getMessages()
                ));
            }

            // update the entity
            if (! $this->gateway->update($blog)) {
                return $this->payload->notUpdated(array(
                    'blog' => $blog,
                ));
            }

            // success
            return $this->payload->updated(array(
                'blog' => $blog,
            ));

        } catch (Exception $e) {

            return $this->payload->error(array(
                'exception' => $e,
                'id' => $id,
                'data' => $data,
            ));

        }
    }

    public function delete($id)
    {
        try {

            // fetch the entity
            $blog = $this->gateway->fetchOneById($id);
            if (! $blog) {
                return $this->payload->notFound(array(
                    'id' => $id
                ));
            }

            // delete the entity
            if (! $this->gateway->delete($blog)) {
                return $this->payload->notDeleted(array(
                    'blog' => $blog,
                ));
            }

            // success
            return $this->payload->deleted(array(
                'blog' => $blog,
            ));

        } catch (Exception $e) {

            return $this->payload->error(array(
                'exception' => $e,
                'blog' => $blog,
            ));

        }
    }
}
