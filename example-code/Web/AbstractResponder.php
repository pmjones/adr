<?php
namespace Web;

use Aura\View\View;
use Aura\Web\Response;
use Aura\Web\Request\Accept;
use Domain\Result;

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
        // empty by default
    }

    public function __invoke()
    {
        $status = $this->result->getStatus();
        $method = isset($this->result_method[$status])
                ? $this->result_method[$status]
                : 'notRecognized';
        $this->$method();
        return $this->response;
    }

    public function setAccept(Accept $accept)
    {
        $this->accept = $accept;
    }

    public function setResult(Result $result)
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

    protected function renderView($view, array $data = array())
    {
        $content_type = $this->response->content->getType();
        if ($content_type) {
            $view .= $this->available[$content_type];
        }

        $this->view->setView($view);
        $this->view->addData($data);
        $this->response->content->set($this->view->__invoke());
    }

    protected function error()
    {
        $this->response->setStatus('500');
        $this->response->setBody($this->getInfo()->getMessage());
    }
}
