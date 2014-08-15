<?php
namespace Domain\Result;

class NotValid extends AbstractResult
{
    protected $messages;

    public function __construct($subject, $messages)
    {
        parent::__construct($subject);
        $this->messages = $messages;
    }

    public function getMessages()
    {
        return $this->messages;
    }
}
