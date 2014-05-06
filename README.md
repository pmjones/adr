# Action-Domain-Response

Organizes a single interaction between a web client and a web application into three distinct roles.

```
    Action ---> Response
      ||
    Domain 
```

## Terms

_Action_ (taken from `<form action="...">`) is the logic that connects the _Domain_ and _Response_.

_Domain_ is the domain logic. It manipulates the domain, session, application, and environment data, modifying state and persistence as needed.

_Response_ is logic to build an HTTP response or response description. It deals with body content, templates and views, headers and cookies, status codes, and so on.

## Narrative

The term MVC has experienced some [semantic diffusion](http://martinfowler.com/bliki/SemanticDiffusion.html) from its original meaning, especially in a web context. Because of this diffusion, the _Action-Domain-Response_ pattern description is intended as a web-specific refinement of the MVC pattern. 

I think ADR more closely fits what we actually do in web development on a daily basis. For example, this pattern is partly revealed by how we generally do web routing and dispatch. We generally route and dispatch *not* to a controller class per se, but to a particular action method within a controller class. 

It is also partly revealed by the fact that we commonly think of the *temnplate* as the _View_, when in a web context it may be more accurate to say that the *HTTP response* is the _View_.  As such, I think ADR may represent a better separation of concerns than MVC does in a web context.

### Operational Description

1. The web handler dispatches the incoming request to an _Action_.

1. The _Action_ interacts with the _Domain_ and gets back _Domain_ data.

1. The _Action_ feeds the _Domain_ data to the _Response_ logic, and then gives
control to the _Reponse_.

1. The web handler sends the response back to the client.

### Compare and Contrast With MVC

The dominant pattern describing web interactions is _Model-View-Controller_. Is _Action-Domain-Response_ really just _Model-View-Controller_ in drag?  We can see that the ADR terms map very neatly to MVC terms:

    _Model_      <--> _Domain_
    _View_       <--> _Response_
    _Controller_ <--> _Action_

The two seem very similar. How are they different?

The _View_ does not update the _Model_ or respond back to the _Controller_. Typically, the _View_ does not consider response headers, only the response body. Also typically, a _Controller_ has more than one _Action_ method in it, and managing the different preconditions for these _Action_ methods carries its own overhead.

#### _Model_ vs _Domain_

I can think of no significant differences here, other than that the _Response_ does not interact with the _Domain_ in meaningful ways. The _Response_ might use _Domain_ objects like entities and collections, but only for presentation purposes; it does not modify the _Domain_ or feed information back to the _Domain_ as described under MVC.

#### _Controller_ vs _Action_

In common usage, most _Controller_ classes in an MVC architecture contain several methods corresponding to different actions. Because these differing action methods reside in the same _Controller_, the _Controller_ ends up needing additional wrapper logic to deal with each method properly, such as pre- and post-action hooks.  A notable exception here is in micro-frameworks, where each _Controller_ is an individual closure or invokable object, mapping more closely to a single _Action_ (cf. [Slim](http://slimframeworkcom)).

In an ADR architecture, a single _Action_ is the main purpose of a class or closure. Multiple _Action_s would be represented by multiple classes.

The _Action_ interacts with the _Domain_ in the same way a _Controller_ interacts with a _Model_, but does *not* interact with a _View_ or template system. It sets data on the _Response_ and hands over control to it.

#### _View_ vs _Response_

In an MVC architecture, a _Controller_ method will usually generate body content via a _View_ (e.g. a _Template View_ or a _Two Step View_). The _Controller_ then injects the generated body content into the response.  The _Controller_ action method will manipulate the response directly to set any needed headers.

Some _Controller_ action methods may present alternative content-types for the same domain data. Because these alternatives may not be consistent over all the different methods, this leads to the presentation logic being somewhat different in each method, each with its own preconditions.

In an ADR architecture, each _Action_ has a separate corresponding _Response_. When the _Action_ is done with the _Domain_, it delivers any needed _Domain_ data to the _Response_ and then hands off to the _Response_ completely. The _Response_ is entirely in charge of setting headers, picking content types, rendering a _View_ for the body content, and so on.

### Other MVC Pattern Alternatives

How does ADR compare to other MVC alternatives?

#### DCI (Data-Context-Interaction)

[DCI is described as a complement to MVC](https://en.wikipedia.org/wiki/Data,_Context,_and_Interaction), not a replacement for MVC. I think it is fair to call it a complement to ADR as well.

#### MVP (Model-View-Presenter)

[MVP has been retired](http://www.martinfowler.com/eaaDev/ModelViewPresenter.html) in favor of [_Supervising Controller_](http://www.martinfowler.com/eaaDev/SupervisingPresenter.html) and [_Passive View_](http://www.martinfowler.com/eaaDev/PassiveScreen.html), neither of which seem to fit the ADR description very closely.

#### PAC (Presentation-Abstraction-Control)

[From Wikipedia](https://en.wikipedia.org/wiki/Presentation-abstraction-control):

> PAC is used as a hierarchical structure of agents, each consisting of a triad of presentation, abstraction and control parts. The agents (or triads) communicate with each other only through the control part of each triad. It also differs from MVC in that within each triad, it completely insulates the presentation (view in MVC) and the abstraction (model in MVC). This provides the option to separately multithread the model and view which can give the user experience of very short program start times, as the user interface (presentation) can be shown before the abstraction has fully initialized.

This does not seem to fit the description of ADR very well.

#### Model-View-ViewModel

[MVVM](https://en.wikipedia.org/wiki/Model_View_ViewModel) seems more suited to desktop applications than web interactions. (Recall that ADR is specifically intended for web interactions.)

### Examples of MVC vs ADR

#### MVC Starting Point

An MVC directory structure for a naive blogging system might look like the following. Note that index and read present an alternative JSON type, and the comments template is a "partial" that also presents an alternative JSON type.

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

A typical _Controller_ class in MVC might looks something like the following. Note that there are multiple actions within the _Controller_ class, and that
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

### ADR Comparison

In comparison, an ADR directory structure might instead look like this. Note how each _Action_ has a corresponding _Response_.

    Blog/
        Action/
            BlogIndexAction.php
            BlogCreateAction.php
            BlogReadAction.php
            BlogUpdateAction.php
            BlogDeleteAction.php
        Domain/
            # Model, Gateway, Mapper, Entity, Collection, Service, etc.
        Response/
            BlogIndexResponse.php
            BlogCreateResponse.php
            BlogReadResponse.php
            BlogUpdateResponse.php
            BlogDeleteResponse.php
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

The _Action_ and _Response_ class pair corresponding to the above _Controller_ `create()` example might look like this:

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
        $this->response->setData(array('blog' => $blog));
        $this->response->__invoke();
    }
}
?>
```

```php
<?php
use Framework\Response;

class BlogCreateResponse extends Response
{
    public function __invoke()
    {
        // is there an ID on the blog instance?
        if ($this->data->blog->id) {
            // yes, which means it was saved already.
            // redirect to editing.
            $this->setRedirect('/blog/edit/{$blog->id}');
        } else {
            // no, which means it has not been saved yet.
            // show the creation form with the current response data.
            $this->setContent($this->view->render(
                'create.html.php',
                $this->data
            ));
        }
    }
}
?>
```

Again, we can see numerous refactoring opportunities here, especially in the domain model work. The point is that the _Action_ does not perform any  _Response_ work at all. That work is handled entirely by the _Response_ logic.

## Benefits and Drawbacks

One benefit overall is that the pattern more closely describes the day-to-day work of web interactions. A request comes in and gets dispatched to an action; the action interacts with the domain, and then builds a response. The response work, including both headers and content, is cleanly separated from the action work.

One drawback is that we end up with more classes in the application. Not only does each _Action_ go in its own class, each _Response_ also goes in its own class.

This drawback may not be so terrible in the longer term. Invididual classes may lead cleaner or less-deep inheritance hierachies. It may also lead to  better testability of the _Action_ separate from the _Response_. These will play themselves out differently in different systems.
