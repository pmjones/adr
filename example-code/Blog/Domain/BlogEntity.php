<?php
namespace Blog\Domain;

class BlogEntity
{
    public $id;
    public $author;
    public $title;
    public $intro;
    public $body;

    protected $messages;

    public function setMessages($messages)
    {
        $this->messages = $messages;
    }

    public function getMessages($messages)
    {
        return $this->messages;
    }
}
