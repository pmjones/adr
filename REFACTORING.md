# Refactoring From MVC to ADR

## Starting With MVC

### Directory Structure

The MVC directory structure for a naive blogging system might look like the following:

    controllers/
        BlogController.php
    models/
        BlogModel.php
    views/
        blog/
            index.php
            create.php
            read.php
            update.php
            delete.php
            _comments.php

> Some notes:
>
> - This system uses an ActiveRecord implementation for its models, even though ActiveRecord is classified primarily as a data source architecture pattern and only secondarily as a domain logic pattern. This is a common enough pattern, so we won't present code for it here.
>
> - The views are a series of PHP-based templates. This pattern is also common enough not to warrant explicit code presentation.

### MVC Logic

The MVC _BlogController_ class might look something like this:

```php
<?php
class BlogController
{
    public function __construct(
        Request $request,
        Response $response,
        TemplateView $view,
        BlogModel $model
    ) {
        // ...
    }

    public function index()
    {
        // ...
    }

    public function create()
    {
        // is this a POST request?
        if ($this->request->isPost()) {

            // retain incoming data
            $data = $this->request->getPost('blog');

            // create a blog post instance
            $blog = $this->model->newInstance($data);

            // is the new instance valid?
            if ($blog->isValid()) {
                // yes, insert and redirect to editing
                $blog->save();
                $this->response->setHeader('Location', "/blog/edit/{$blog->id}");
            } else {
                // no, show the "create" form with the blog record
                $html = $this->view->render(
                    'create.php',
                    ['blog' => $blog],
                );
                $this->response->setContent($html);
            }
        } else {
            // not a POST request, show the "create" form with a new record
            $html = $this->view->render(
                'create.php',
                ['blog' => $this->model->newInstance()]
            );
            $this->response->setContent($html);
        }

        return $this->response;
    }

    public function read()
    {
        // ...
    }

    public function update()
    {
        // ...
    }

    public function delete()
    {
        // ...
    }
}
```

> Some notes:
>
> - The _Controller_ contains multiple action methods.
>
> - The _Controller_  sets the response headers directly, even though it hands off content-building control to a _Template View_. Since the entire HTTP response is being presented, setting headers in the _Controller_ represents a failure to properly separate presentation concerns.
>
> - The _Controller_ performs business logic on the _Model_, rather than handing off the business logic to a domain layer. This represents a failure to properly separate domain concerns.


## Refactoring to ADR

### Directory Structure

In comparison, an ADR directory structure refactored from the above MVC system might look like this:

    resources/
        templates/
            blog/
                index.php
                create.php
                read.php
                update.php
                delete.php
                _comments.php
    src/
        Domain/
            Blog/
                BlogModel.php
        Ui/
            Web/
                Blog/
                    Index/
                        BlogIndexAction.php
                        BlogIndexResponder.php
                    Create/
                        BlogCreateAction.php
                        BlogCreateResponder.php
                    Read/
                        BlogReadAction.php
                        BlogReadResponder.php
                    Update/
                        BlogUpdateAction.php
                        BlogUpdateResponder.php
                    Delete/
                        BlogDeleteAction.php
                        BlogDeleteResponder.php

> Some notes:
>
> - We have extracted each action method from the _BlogController_ to its own _Action_ class in a namespace dedicated to a "web" user interface.
>
> - Each _Action_ has a corresponding _Responder_, into which all presentation work (i.e., response-building work) has been placed.
>
> - We have renamed `views/` to `templates/` and moved it to a different location.
>
> - While we might prefer to replace the ActiveRecord _BlogModel_ class with a data mapper (_BlogMapper_) that returns persistence model objects (_BlogRecord_), that is beyond the scope of this exercise. We will leave the ActiveRecord implementation as it is, though we have moved it into a namespace dedicated to the domain layer.

### ADR Logic

The refactoring goals are to:

- separate presentation (response-building) logic from all other logic;
- separate domain logic from all other logic;
- remove all conditional logic from Actions (with the exception of ternaries for default input values)

These goals complement each other and feed back on each other; they might or might not be acheived in isolation.

The following is one possible order of refactorings. Another set of changes, or a similar set but in a different order, might achieve the same goals.

#### Separate Presentation

An initial refactoring of the above _BlogController_ `create()` method to an _Action_ and _Responder_ pair might look like this:

```php
<?php
class BlogCreateAction
{
    public function __construct(
        Request $request,
        BlogCreateResponder $responder,
        BlogModel $model
    ) {
        // ...
    }

    public function __invoke()
    {
        // is this a POST request?
        if ($this->request->isPost()) {

            // yes, retain incoming data
            $data = $this->request->getPost('blog');

            // create a blog post instance
            $blog = $this->model->newInstance($data);

            // is the new instance valid?
            if ($blog->isValid()) {
                // yes, insert it
                $blog->save();
            }

        } else {
            // not a POST request
            $blog = $this->model->newInstance();
        }

        // use the responder to build a response
        return $this->responder->response($blog);
    }
}
```

```php
<?php
class BlogCreateResponder
{
    public function __construct(
        Response $response,
        TemplateView $view
    ) {
        // ...
    }

    public function response(BlogModel $blog)
    {
        // is there an ID on the blog instance?
        if ($blog->id) {
            // yes, which means it was saved already.
            // redirect to editing.
            $this->response->setHeader('Location', '/blog/edit/{$blog->id}');
        } else {
            // no, which means it has not been saved yet.
            // show the creation form with the current data.
            $html = $this->view->render(
                'create.php',
                ['blog' => $blog]
            );
            $this->response->setContent($html);
        }

        return $this->response;
    }
}
```

> Note that we use the PHP magic method `__invoke()` as the "main" method for action invocation; this could be any other method name we wanted to standardize on.

At this point we have successfully separated all presentation (response-building) work to the _Responder_.

#### Separate Domain Logic

The _BlogCreateAction_ is still performing some business logic. We can refactor it to create a domain-layer _BlogService_ to handle that for us.

```php
<?php
class BlogService
{
    public function __construct(BlogModel $model)
    {
        // ...
    }

    public function newInstance()
    {
        return $this->model->newInstance();
    }

    public function create(array $data)
    {
        $blog = $this->model->newInstance($data);

        if ($blog->isValid()) {
            $blog->save();
        }

        return $blog;
    }
}
```

```php
<?php
class BlogCreateAction
{
    public function __construct(
        Request $request,
        BlogCreateResponder $responder,
        BlogService $domain
    ) {
        // ...
    }

    public function __invoke()
    {
        if ($this->request->isPost()) {
            $data = $this->request->getPost('blog');
            $blog = $this->domain->create($data);
        } else {
            $blog = $this->domain->newInstance();
        }

        return $this->responder->response($blog);
    }
}
```

> Note that a _BlogService_ is now being injected into the _BlogCreateAction_ as `$domain`, instead of a _BlogModel_ as `$model`.

At this point, all presentation (response-building) work is being handled in _Responder_ code, and all business logic is being handled in _Domain_ code.

#### Remove Action Conditionals

The remaining logic in the _BlogCreateAction_ proceeds along two different paths:

- one, to present the form for adding a new blog entry;
- and another, to actually save the new blog entry.

We can extract one of the paths to a _BlogAddAction_, perhaps like so:

```php
<?php
class BlogAddAction
{
    public function __construct(
        Request $request,
        BlogCreateResponder $responder,
        BlogService $domain
    ) {
        // ...
    }

    public function __invoke()
    {
        $blog = $this->domain->newInstance();
        return $this->responder->response($blog);
    }
}

class BlogCreateAction
{
    public function __construct(
        Request $request,
        BlogCreateResponder $responder,
        BlogService $domain
    ) {
        // ...
    }

    public function __invoke()
    {
        $data = $this->request->getPost('blog');
        $blog = $this->domain->create($data);
        return $this->responder->response($blog);
    }
}
```

> Some notes:
>
> - Both actions continue to use the same Responder and Domain classes.
>
> - We will need to modify the web handler (probably a router) to dispatch to one _BlogAddAction_ on GET, and _BlogCreateAction_ on POST.

At this point we have fulfilled the ADR pattern of components and collaborations:

- each _Action_ only does three things: it collects input, calls the domain, then calls the responder;

- the _Domain_ code handles all business logic;

- the _Responder_ code handles all presentation logic.

## Introducing A Domain Payload

> N.b.: A _Domain Payload_ can be complementary to ADR, but is not a required component of the pattern.

Currently, the _Responder_ still has to inspect the _Domain_ results to figure out how to present those results. However, the _Domain_ already knows what the results mean. The _Responder_ should not have to do extra work to divine the meaning of the _Domain_ results; instead, the _Domain_ should communicate that status explicitly.

To do so, the _Domain_ can return its results wrapped in a _Payload_ object along with a status value. The _Responder_ can then work with the _Payload_ instead of picking apart domain object values to find meaning.

First, we introduce a _Payload_:

```php
<?php
class Payload
{
    public function __construct($status, array $result = [])
    {
        $this->status = $status;
        $this->result = $result;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getResult()
    {
        return $this->result;
    }
}
```

Then we modify the _BlogService_ to return a _Payload_ instead of unwrapped domain objects:

```php
class BlogService
{
    public function __construct(BlogModel $model)
    {
        // ...
    }

    public function newInstance()
    {
        return new Payload(
            'NEW',
            ['blog' => $this->model->newInstance()]
        );
    }

    public function create(array $data)
    {
        $blog = $this->model->newInstance($data);

        if (! $blog->isValid()) {
            return new Payload('INVALID', [$blog => 'blog']);
        }

        $blog->save();
        return new Payload('SAVED', [$blog => 'blog']);
    }
}
```

Each _Action_ calling the service now receives a _Payload_ in return, and passes that _Payload_ to the _Responder_:

```php
<?php
class BlogAddAction
{
    public function __construct(
        Request $request,
        BlogCreateResponder $responder,
        BlogService $domain
    ) {
        // ...
    }

    public function __invoke()
    {
        $payload = $this->domain->newInstance();
        return $this->responder->response($payload);
    }
}
```

```php
<?php
class BlogCreateAction
{
    public function __construct(
        Request $request,
        BlogCreateResponder $responder,
        BlogService $domain
    ) {
        // ...
    }

    public function __invoke()
    {
        $data = $this->request->getPost('blog');
        $payload = $this->domain->create($data);
        return $this->responder->response($payload);
    }
}
```

And finally, the _Responder_ can use the _Payload_ status to determine how to present the results:

```php
<?php
class BlogCreateResponder
{
    public function __construct(
        Response $response,
        TemplateView $view
    ) {
        // ...
    }

    public function response(Payload $payload)
    {
        $blog = $payload->getResult()['blog'];

        switch ($payload->getStatus()) {
            case 'SAVED':
                $this->response->setHeader('Location', "/blog/edit/{$blog->id}")
                break;
            case 'INVALID':
            case 'NEW':
                $html = $this->view->render(
                    'create.php',
                    ['blog' => $blog]
                );
                $this->response->setContent($html);
                break;
            case default:
                $this->response->setStatus(500, 'Unknown Payload Status')
        }

        return $this->response;
    }
}
```

By using a _Domain Payload_, we can avoid having to inspect domain objects directly to determine how to present them, and instead check the status that the domain passed back explicitly. THis makes presentation logic easier to read and follow.
