<?php
namespace Domain\Payload;

class PayloadFactory
{
    public function newEntity(array $payload)
    {
        return new NewEntity($payload);
    }

    public function found(array $payload)
    {
        return new Found($payload);
    }

    public function notFound(array $payload)
    {
        return new NotFound($payload);
    }

    public function valid(array $payload)
    {
        return new Valid($payload);
    }

    public function notValid(array $payload)
    {
        return new NotValid($payload);
    }

    public function created(array $payload)
    {
        return new Created($payload);
    }

    public function notCreated(array $payload)
    {
        return new NotCreated($payload);
    }

    public function updated(array $payload)
    {
        return new Updated($payload);
    }

    public function notUpdated(array $payload)
    {
        return new NotUpdated($payload);
    }

    public function deleted(array $payload)
    {
        return new Deleted($payload);
    }

    public function notDeleted(array $payload)
    {
        return new NotDeleted($payload);
    }

    public function error(array $payload)
    {
        return new Error($payload);
    }
}
