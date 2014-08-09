<?php
namespace Web\Blog\Action;

use Aura\Web\Request;
use Domain\Blog\BlogService;
use Web\Blog\Responder\BlogEditResponder;
use Web\AbstractAction;

class BlogEditAction extends AbstractAction
{
    public function __construct(
        Request $request,
        BlogService $domain,
        BlogEditResponder $responder
    ) {
        $this->request = $request;
        $this->domain = $domain;
        $this->responder = $responder;
    }

    public function __invoke($id)
    {
        $data = $this->request->post->get('blog');
        $this->responder->blog = $this->domain->updateById($id, $data);
        return $this->response();
    }
}
