<?php
namespace Blog\Responder;

use Domain\Result;

class BlogUpdateResponder extends AbstractBlogResponder
{
    protected $result_method = array(
        Result::STATUS_NOT_FOUND => 'notFound',
        Result::STATUS_NOT_VALID => 'notValid',
        Result::STATUS_UPDATED => 'updated',
        Result::STATUS_NOT_UPDATED => 'notUpdated',
    );

    protected function notValid($result)
    {
        $this->response->setStatus('422');
        return $this->renderView('edit', array(
            'blog' => $result->getSubject()
        ));
    }

    protected function updated()
    {
        return $this->renderView('edit', array(
            'blog' => $result->getSubject()
        ));
    }

    protected function notUpdated()
    {
        $this->response->setStatus('500');
        return $this->renderView('edit', array(
            'blog' => $result->getSubject();
        ));
    }
}
