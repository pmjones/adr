<?php
namespace Web\Blog\Action;

use Aura\Web\Request;
use Domain\Blog\BlogService;
use Web\Blog\Responder\BlogReadResponder;
use Web\AbstractAction;

class BlogReadAction extends AbstractAction
{
    public function __construct(
        Request $request,
        BlogService $domain,
        BlogReadResponder $responder
    ) {
        $this->request = $request;
        $this->domain = $domain;
        $this->responder = $responder;
    }

    public function __invoke($id)
    {
        $this->responder->blog = $this->domain->fetchOneById($id);
        $this->responder->setAccept($this->request->accept);
        return $this->response();
    }
}
