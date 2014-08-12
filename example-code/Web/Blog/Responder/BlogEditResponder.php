<?php
namespace Blog\Responder;

use Domain\Result;

class BlogEditResponder extends AbstractBlogResponder
{
    protected $result_method = array(
        Result::STATUS_FOUND => 'found',
        Result::STATUS_NOT_FOUND => 'notFound'
    );

    public function found()
    {
        return $this->renderView('edit', array(
            'blog' => $this->result->getSubject()
        ));
    }
}
