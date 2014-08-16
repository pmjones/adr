<?php
namespace Domain\Result;

class ResultFactory
{
    public function newEntity(array $result)
    {
        return new NewEntity($result);
    }

    public function found(array $result)
    {
        return new Found($result);
    }

    public function notFound(array $result)
    {
        return new NotFound($result);
    }

    public function valid(array $result)
    {
        return new Valid($result);
    }

    public function notValid(array $result)
    {
        return new NotValid($result);
    }

    public function created(array $result)
    {
        return new Created($result);
    }

    public function notCreated(array $result)
    {
        return new NotCreated($result);
    }

    public function updated(array $result)
    {
        return new Updated($result);
    }

    public function notUpdated(array $result)
    {
        return new NotUpdated($result);
    }

    public function deleted(array $result)
    {
        return new Deleted($result);
    }

    public function notDeleted(array $result)
    {
        return new NotDeleted($result);
    }

    public function error(array $result)
    {
        return new Error($result);
    }
}
