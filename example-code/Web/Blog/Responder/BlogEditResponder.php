<?php
namespace Web\Blog\Responder;

class BlogEditResponder extends AbstractBlogResponder
{
    protected $result_method = array(
        'Domain\Result\Found' => 'found',
        'Domain\Result\NotFound' => 'notFound',
    );

    public function found()
    {
        return $this->renderView('edit');
    }
}
