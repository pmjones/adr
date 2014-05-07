<?php
namespace Blog\Action;

use Aura\Web\Request;
use Blog\Domain\BlogService;
use Blog\Responder\BlogDeleteResponder;

class BlogDeleteAction extends AbstractBlogAction
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
        $this->responder->blog = $this->domain->fetchOneById($id);

        $this->responder->success = null;
        if ($this->responder->blog) {
            $this->responder->success = $this->domain->delete($blog);
        }
        
        return $this->response();
    }
}
