<?php
namespace Blog\Responder;

use Domain\Result;

class BlogDeleteResponder extends AbstractBlogResponder
{
    protected $result_method = array(
        'Domain\Result\NotFound' => 'notFound',
        'Domain\Result\Deleted' => 'deleted',
        'Domain\Result\NotDeleted' => 'notDeleted',
    );

    protected function deleted()
    {
        $this->renderView('delete-success', array(
            'blog' => $this->result->getSubject(),
        ));
    }

    protected function notDeleted()
    {
        $this->response->setStatus(500);
        $this->renderView('delete-failure', array(
            'blog' => $this->result->getSubject(),
        ));
    }
}
