<?php
namespace Blog\Domain;

class BlogService
{
    public function fetchAllByPage($page)
    {
        // returns a collection of BlogEntity objects
    }

    public function fetchOneById($id)
    {
        // returns a single BlogEntity
    }

    public function create(array $data)
    {
        // returns a new BlogEntity instance based on the input data.
        // the entity will be saved if the data is valid, giving it an ID.
        // if the data is not valid then the entity will not get an ID.
        // failure messages are saved using setMessages() on the entity.
    }

    public function update(BlogEntity $blog, array $data)
    {
        // updates an existing BlogEntity object with input data.
        // failure messages are saved using setMessages() on the entity.
    }

    public function delete(BlogEntity $blog)
    {
        // deletes a BlogEntity object, returning a success boolean
    }
}
