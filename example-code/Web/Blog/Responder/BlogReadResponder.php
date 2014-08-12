<?php
namespace Blog\Responder;

use Domain\Result;

class BlogReadResponder extends AbstractBlogResponder
{
    protected $available = array(
        'text/html' => '',
        'application/json' => '.json'
    );

    protected $result_method = array(
        Result::STATUS_FOUND => 'found',
        Result::STATUS_NOT_FOUND => 'notFound'
    );

    protected function found()
    {
        if ($$this->negotiateMediaType()) {
            $this->renderView('read', array(
                'blog' => $this->result->getSubject()
            ));
        }
    }
}
