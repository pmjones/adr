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

    public function __construct($data = array())
    {
        $this->setData($data);
    }

    public function setMessages($messages)
    {
        $this->messages = $messages;
    }

    public function getMessages($messages)
    {
        return $this->messages;
    }

    public function setData($data = array())
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    public function getData()
    {
        $properties = get_object_vars($this);
        return $properties;
    }
}
