<?php
namespace Blog\Domain;

class BlogService
{
    public function fetchAllByPage($page)
    {
        // returns a collection of BlogEntity objects
        $data = array(
            array(
                'id' => 1,
                'author' => 'Paul M Jones',
                'title' => 'About Paul',
                'intro' => 'Paul M. Jones is an internationally recognized PHP expert ...',
                'body' => <<<EOT 
Paul M. Jones is an internationally recognized PHP expert who has worked as
everything from junior developer to VP of Engineering in all kinds of
organizations (corporate, military, non-profit, educational, medical, and
others). Paul's latest open-source project is Aura for PHP.
EOT;
            ),
            array(
                'id' => 2,
                'author' => 'Hari KT',
                'title' => 'About Hari',
                'intro' => 'Some introduction',
                'body' => 'Some long content'
            )
        );
        $collection = array();
        foreach ($data as $val) {
            $collection[] = new BlogEntity($val);
        }
        return $collection;
    }

    public function fetchOneById($id)
    {
        // returns a single BlogEntity
        $data = array(
            'id' => 1,
            'author' => 'Hari KT',
            'title' => 'Something',
            'intro' => 'Some introduction',
            'body' => 'Some long content'
        );
        return new BlogEntity($data);
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
