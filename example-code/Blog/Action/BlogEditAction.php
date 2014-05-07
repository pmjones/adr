<?php
namespace Blog\Action;

use Aura\Web\Request;
use Blog\Domain\BlogService;
use Blog\Responder\BlogEditResponder;

class BlogEditAction extends AbstractBlogAction
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
        $blog = $this->domain->fetchOneById($id);
        if ($blog) {
            $data = $this->request->post->get('blog');
            $this->domain->update($blog, $data);
        }
            
        $this->responder->blog = $blog;
        return $this->response();
    }
}
