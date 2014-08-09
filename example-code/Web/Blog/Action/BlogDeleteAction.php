<?php
namespace Web\Blog\Action;

use Aura\Web\Request;
use Domain\Blog\BlogService;
use Web\Blog\Responder\BlogDeleteResponder;
use Web\AbstractAction;

class BlogDeleteAction extends AbstractAction
{
    public function __construct(
        Request $request,
        BlogService $domain,
        BlogDeleteResponder $responder
    ) {
        $this->request = $request;
        $this->domain = $domain;
        $this->responder = $responder;
    }

    public function __invoke($id)
    {
        $this->responder->status = $this->domain->deleteById($id);
        return $this->response();
    }
}
