<?php
namespace Web;

use Aura\View\View;
use Aura\Web\Response;
use Aura\Web\Request\Accept;
use Domain\Result\ResultInterface;

abstract class AbstractResponder
{
    protected $accept;

    protected $available = array();

    protected $response;

    protected $result;

    protected $result_method = array();

    protected $view;

    public function __construct(
        Response $response,
        View $view
    ) {
        $this->response = $response;
        $this->view = $view;
        $this->init();
    }

    protected function init()
    {
        if (! isset($this->result_method['Domain\Result\Error'])) {
            $this->result_method['Domain\Result\Error'] = 'error';
        }
    }

    public function __invoke()
    {
        $class = get_class($this->result);
        $method = isset($this->result_method[$class])
                ? $this->result_method[$class]
                : 'notRecognized';
        $this->$method();
        return $this->response;
    }

    public function setAccept(Accept $accept)
    {
        $this->accept = $accept;
    }

    public function setResult(ResultInterface $result)
    {
        $this->result = $result;
    }

    protected function notRecognized()
    {
        $domain_status = $this->result->getStatus();
        $this->response->setStatus('500');
        $this->response->setBody("Unknown domain result status: '$status'");
        return $this->response;
    }

    protected function negotiateMediaType()
    {
        if (! $this->available || ! $this->accept) {
            return true;
        }

        $available = array_keys($this->available);
        $media = $this->accept->media->negotiate($available);
        if (! $media) {
            $this->response->status->set(406);
            $this->response->content->setType('text/plain');
            $this->response->content->set(implode(',', $available));
            return false;
        }

        $this->response->content->setType($media->available->getValue());
        return true;
    }

    protected function renderView($view, $layout = null)
    {
        $content_type = $this->response->content->getType();
        if ($content_type) {
            $view .= $this->available[$content_type];
        }

        $this->view->setView($view);
        $this->view->setLayout($layout);
        $this->view->addData($this->result->get());
        $this->response->content->set($this->view->__invoke());
    }

    protected function notFound()
    {
        $this->response->status->set(404);
    }

    protected function error()
    {
        $e = $this->result->get('exception');
        $this->response->status->set('500');
        $this->response->content->set($e->getMessage());
    }
}
