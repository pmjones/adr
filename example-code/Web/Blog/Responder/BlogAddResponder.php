<?php
namespace Blog\Responder;

use Domain\Result;

class BlogAddResponder extends AbstractBlogResponder
{
    protected $result_method = array(
        'Domain\Result\NewEntity' => 'display',
    );

    protected function display()
    {
        $this->renderView('add', array(
            'blog' => $this->result->getSubject()
        ));
    }
}
