<?php
namespace Blog\Responder;

use Domain\Result;

class BlogAddResponder extends AbstractBlogResponder
{
    protected $result_method = array(
        Result::STATUS_NEW_INSTANCE => 'display',
    );

    protected function display(Result $result)
    {
        $this->renderView('add', array(
            'blog' => $this->result->getSubject()
        ));
    }
}
