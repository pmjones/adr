<?php
namespace Blog\Responder;

use Domain\Result;

class BlogEditResponder extends AbstractBlogResponder
{
    protected $result_method = array(
        'Domain\Result\Found' => 'found',
        'Domain\Result\NotFound' => 'notFound',
    );

    public function found()
    {
        return $this->renderView('edit', array(
            'blog' => $this->result->getSubject()
        ));
    }
}
