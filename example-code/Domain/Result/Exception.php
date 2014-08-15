<?php
namespace Domain\Result;

use Exception;

class Error extends AbstractResult
{
    protected $exception;

    public function __construct(Exception $exception, $subject = null)
    {
        $this->exception = $exception;
        parent::__construct($subject);
    }

    public function getException()
    {
        return $this->exception;
    }
}
