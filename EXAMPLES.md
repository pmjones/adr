## Examples of MVC vs ADR

### MVC Starting Point

An MVC directory structure for a naive blogging system might look like the following. Note that `index` and `read` present an alternative JSON type, and the comments template is a "partial" that also presents an alternative JSON type.

    controllers/
        BlogController.php # index(), create(), read(), update(), delete()
    models/
        BlogModel.php
    views/
        blog/
            index.html.php
            index.json.php
            create.html.php
            read.html.php
            read.json.php
            update.html.php
            delete.html.php
            _comments.html.php
            _comments.json.php

Here's another type of MVC directory structure:

    Blog/
        BlogController.php  # index(), create(), read(), update(), delete()
        BlogModel.php
        views/
            index.html.php
            index.json.php
            create.html.php
            read.html.php
            read.json.php
            update.html.php
            delete.html.php
            _comments.html.php
            _comments.json.php

A typical _Controller_ class in MVC might look something like the following. Note that there are multiple actions within the _Controller_ class, and that
the action method deals with the response headers.

```php
<?php
use Framework\Controller;

class BlogController extends Controller
{
    public function create()
    {
        // is this a POST request?
        if ($this->request->isPost()) {

            // retain incoming data
            $data = $this->request->getPost('blog');

            // create a blog post instance
            $blog = $this->blog_model->newInstance($data);

            // is the new instance valid?
            if ($blog->isValid()) {
                // yes, save and redirect to editing
                $blog->save();
                $this->response->redirect('/blog/edit/{$blog->id}');
                return;
            } else {
                // no, show the "create" form with the blog instance
                $this->response->setContent($this->view->render(
                    'create.html.php',
                    array('blog' => $blog),
                ));
                return;
            }
        } else {
            // not a POST request, show the "create" form with defaults
            $this->response->setContent($this->view->render(
                'create.html.php',
                array('blog' => $this->blog_model->getDefault())
            ));
        }
    }

    public function index()
    {
        // ...
    }

    public function read($id)
    {
        // ...
    }

    public function update($id)
    {
        // ...
    }

    public function delete($id)
    {
        // ...
    }
}
?>
```

The `create()` logic could be reduced somewhat by moving even more of the model interactions into a _Service Layer_, but the point remains that the _Controller_ typically sets the response headers and content.

### ADR Revision

In comparison, an ADR directory structure might instead look like this. Note how each _Action_ has a corresponding _Responder_.

    Blog/
        Action/
            BlogIndexAction.php
            BlogCreateAction.php
            BlogReadAction.php
            BlogUpdateAction.php
            BlogDeleteAction.php
        Domain/
            # Model, Gateway, Mapper, Entity, Collection, Service, etc.
        Responder/
            BlogIndexResponder.php
            BlogCreateResponder.php
            BlogReadResponder.php
            BlogUpdateResponder.php
            BlogDeleteResponder.php
            html/
                index.html.php
                create.html.php
                read.html.php
                update.html.php
                delete.html.php
                _comments.html.php
            json/
                index.json.php
                read.json.php
                _comments.json.php

The _Action_ and _Responder_ class pair corresponding to the above _Controller_ `create()` example might look like this:

```php
<?php
use Framework\Action;

class BlogCreateAction extends Action
{
    public function __invoke()
    {
        // is this a POST request?
        if ($this->request->isPost()) {

            // yes, retain incoming data
            $data = $this->request->getPost('blog');

            // create a blog post instance
            $blog = $this->blog_model->newInstance($data);

            // is the new instance valid?
            if ($blog->isValid()) {
                $blog->save();
            }

        } else {
            // not a POST request, use default values
            $blog = $this->blog_model->getDefault();
        }

        // set data into the response
        $this->responder->setData(array('blog' => $blog));
        $this->responder->__invoke();
    }
}
?>
```

```php
<?php
use Framework\Responder;

class BlogCreateResponder extends Responder
{
    // $this->response is the actual response object, or a response descriptor
    // $this->view is a view or template system
    public function __invoke()
    {
        // is there an ID on the blog instance?
        if ($this->data->blog->id) {
            // yes, which means it was saved already.
            // redirect to editing.
            $this->response->setRedirect('/blog/edit/{$blog->id}');
        } else {
            // no, which means it has not been saved yet.
            // show the creation form with the current response data.
            $this->response->setContent($this->view->render(
                'create.html.php',
                $this->data
            ));
        }
    }
}
?>
```

Again, we can see numerous refactoring opportunities here, especially in the domain model work. The point is that the _Action_ does not perform any  _Responder_ work at all. That work is handled entirely by the _Responder_ logic.

You can review an extended set of sample ADR code [here](https://github.com/pmjones/adr/blob/master/example-code).

