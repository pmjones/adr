<?php
namespace Domain\Result;

class ResultFactory
{
    public function newEntity($subject)
    {
        return new NewEntity($subject);
    }

    public function found($subject)
    {
        return new Found($subject);
    }

    public function notFound($subject)
    {
        return new NotFound($subject);
    }

    public function valid($subject)
    {
        return new Valid($subject);
    }

    public function notValid($subject, $messages)
    {
        return new NotValid($subject, $messages);
    }

    public function created($subject)
    {
        return new Created($subject);
    }

    public function notCreated($subject)
    {
        return new NotCreated($subject);
    }

    public function updated($subject)
    {
        return new Updated($subject);
    }

    public function notUpdated($subject)
    {
        return new NotUpdated($subject);
    }

    public function deleted($subject)
    {
        return new Deleted($subject);
    }

    public function notDeleted($subject)
    {
        return new NotDeleted($subject);
    }

    public function error($exception, $subject = null)
    {
        return new Error($exception, $subject);
    }
}
