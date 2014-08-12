<?php
namespace Domain;

class ResultFactory
{
    public function newInstance($subject)
    {
        return new Result(Result::STATUS_NEW_INSTANCE, $subject);
    }

    public function found($subject)
    {
        return new Result(Result::STATUS_FOUND, $subject);
    }

    public function notFound($subject)
    {
        return new Result(Result::STATUS_NOT_FOUND, $subject);
    }

    public function valid($subject)
    {
        return new Result(Result::STATUS_VALID, $subject);
    }

    public function notValid($subject, $messages)
    {
        return new Result(Result::STATUS_NOT_VALID, $subject, $messages);
    }

    public function created($subject)
    {
        return new Result(Result::STATUS_CREATED, $subject);
    }

    public function notCreated($subject)
    {
        return new Result(Result::STATUS_NOT_CREATED, $subject);
    }

    public function updated($subject)
    {
        return new Result(Result::STATUS_UPDATED, $subject);
    }

    public function notUpdated($subject)
    {
        return new Result(Result::STATUS_NOT_UPDATED, $subject);
    }

    public function deleted($subject)
    {
        return new Result(Result::STATUS_DELETED, $subject);
    }

    public function notDeleted($subject)
    {
        return new Result(Result::STATUS_NOT_DELETED, $subject);
    }

    public function error($exception, $subject = null)
    {
        return new Result(Result::STATUS_ERROR, $subject, $exception);
    }
}
