<?php
namespace Blog\Responder;

use Domain\Result;

class BlogUpdateResponder extends AbstractBlogResponder
{
    protected $result_method = array(
        'Domain\Result\NotFound' => 'notFound',
        'Domain\Result\NotValid' => 'notValid',
        'Domain\Result\Updated' => 'updated',
        'Domain\Result\NotUpdated' => 'notUpdated',
    );

    protected function notValid()
    {
        $this->response->setStatus('422');
        return $this->renderView('edit', array(
            'blog' => $this->result->getSubject(),
            'messages' => $this->result->getMessages(),
        ));
    }

    protected function updated()
    {
        return $this->renderView('edit', array(
            'blog' => $this->result->getSubject()
        ));
    }

    protected function notUpdated()
    {
        $this->response->setStatus('500');
        return $this->renderView('edit', array(
            'blog' => $this->result->getSubject();
        ));
    }
}
