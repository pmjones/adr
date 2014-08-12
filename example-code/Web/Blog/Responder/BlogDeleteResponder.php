<?php
namespace Blog\Responder;

use Domain\Result;

class BlogDeleteResponder extends AbstractBlogResponder
{
    protected $result_method = array(
        Result::STATUS_NOT_FOUND => 'notFound',
        Result::STATUS_DELETED => 'deleted',
        Result::STATUS_NOT_DELETED => 'notDeleted',
        Result::STATUS_ERROR => 'error',
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
