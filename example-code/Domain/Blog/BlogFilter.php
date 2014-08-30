<?php
namespace Domain\Blog;

class BlogFilter
{
    protected $messages = array();

    public function getMessages()
    {
        return $this->messages;
    }

    public function forInsert(BlogEntity $blog)
    {
        $this->blog = $blog;
        $this->messages = array();
        return $this->basic($blog);
    }

    public function forUpdate(BlogEntity $blog)
    {
        $this->blog = $blog;
        $this->messages = array();

        $this->blog->id = (int) $this->blog->id;
        if (! $this->blog->id) {
            $this->messages['id'];
        }

        return $this->basic($blog);
    }

    protected function basic()
    {
        $this->blog->author = trim($this->blog->author);
        if (! $this->blog->author) {
            $this->messages['author'] = 'Author cannot be empty.';
        }

        $this->blog->title = trim($this->blog->title);
        if (! $this->blog->title) {
            $this->messages['title'] = 'Title cannot be empty.';
        }

        $this->blog->body = trim($this->blog->body);
        if (! $this->blog->body) {
            $this->messages['body'] = 'Body cannot be empty.';
        }

        return $this->isValid();
    }

    protected function isValid()
    {
        if ($this->messages) {
            return false;
        }

        return true;
    }
}