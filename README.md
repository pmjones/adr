# Action-Domain-Responder

## Purpose

Organizes a single interaction between a web client and a web application into three distinct roles.

![ADR](adr.png)

## Background

The term MVC has experienced some [semantic diffusion](http://martinfowler.com/bliki/SemanticDiffusion.html) from its original meaning, especially in a web context. Because of this diffusion, the _Action-Domain-Responder_ pattern description is intended as a web-specific refinement of the MVC pattern. 

I think ADR more closely fits what we actually do in web development on a daily basis. For example, this pattern is partly revealed by how we generally do web routing and dispatch. We generally route and dispatch *not* to a controller class per se, but to a particular action method within a controller class. 

It is also partly revealed by the fact that we commonly think of the template as the _View_, when in a web context it may be more accurate to say that the HTTP response is the _View_.  As such, I think ADR may represent a better separation of concerns than MVC does in a web context.

## Components

_Action_ is the logic that connects the _Domain_ and _Responder_.

_Domain_ is the domain logic. It manipulates the domain, session, application, and environment data, modifying state and persistence as needed.

_Responder_ is the logic to build an HTTP response or response description. It deals with body content, templates and views, headers and cookies, status codes, and so on.

## Collaborations

1. The web handler dispatches the incoming request to an _Action_.

1. The _Action_ interacts with the _Domain_ and gets back _Domain_ data.

1. The _Action_ feeds the _Domain_ data to the _Responder_, and then gives control to the _Reponder_.

1. The _Responder_ builds a response, which the web handler sends back to the client.

## Comparisons

### MVC (Model-View-Controller)

The dominant pattern describing web interactions is _Model-View-Controller_. Is _Action-Domain-Responder_ really just _Model-View-Controller_ in drag?  We can see that the ADR terms map very neatly to MVC terms:

    Model      <--> Domain
    View       <--> Responder
    Controller <--> Action

The two seem very similar. How are they different?

#### _Model_ vs _Domain_

I can think of no significant differences here, other than that the _Responder_ does not interact with the _Domain_ in meaningful ways. The _Responder_ might use _Domain_ objects like entities and collections, but only for presentation purposes; it does not modify the _Domain_ or feed information back to the _Domain_ as described under MVC.

#### _Controller_ vs _Action_

In common usage, most _Controller_ classes in an MVC architecture contain several methods corresponding to different actions. Because these differing action methods reside in the same _Controller_, the _Controller_ ends up needing additional wrapper logic to deal with each method properly, such as pre- and post-action hooks.  A notable exception here is in micro-frameworks, where each _Controller_ is an individual closure or invokable object, mapping more closely to a single _Action_ (cf. [Slim](http://slimframeworkcom)).

In an ADR architecture, a single _Action_ is the main purpose of a class or closure. Each _Action_ would be represented by a individual class or closure.

The _Action_ interacts with the _Domain_ in the same way a _Controller_ interacts with a _Model_, but does *not* interact with a _View_ or template system. It sets data on the _Responder_ and hands over control to it.

#### _View_ vs _Responder_

In an MVC architecture, a _Controller_ method will usually generate body content via a _View_ (e.g. a _Template View_ or a _Two Step View_). The _Controller_ then injects the generated body content into the response.  The _Controller_ action method will manipulate the response directly to set any needed headers.

Some _Controller_ action methods may present alternative content-types for the same domain data. Because these alternatives may not be consistent over all the different methods, this leads to the presentation logic being somewhat different in each method, each with its own preconditions.

In an ADR architecture, each _Action_ has a separate corresponding _Responder_. When the _Action_ is done with the _Domain_, it delivers any needed _Domain_ data to the _Responder_ and then hands off to the _Responder_ completely. The _Responder_ is entirely in charge of setting headers, picking content types, rendering a _View_ for the body content, and so on.

### DCI (Data-Context-Interaction)

[DCI is described as a complement to MVC](https://en.wikipedia.org/wiki/Data,_Context,_and_Interaction), not a replacement for MVC. I think it is fair to call it a complement to ADR as well.

### MVP (Model-View-Presenter)

[MVP has been retired](http://www.martinfowler.com/eaaDev/ModelViewPresenter.html) in favor of [_Supervising Controller_](http://www.martinfowler.com/eaaDev/SupervisingPresenter.html) and [_Passive View_](http://www.martinfowler.com/eaaDev/PassiveScreen.html), neither of which seem to fit the ADR description very closely.

### PAC (Presentation-Abstraction-Control)

[From Wikipedia](https://en.wikipedia.org/wiki/Presentation-abstraction-control):

> PAC is used as a hierarchical structure of agents, each consisting of a triad of presentation, abstraction and control parts. The agents (or triads) communicate with each other only through the control part of each triad. It also differs from MVC in that within each triad, it completely insulates the presentation (view in MVC) and the abstraction (model in MVC). This provides the option to separately multithread the model and view which can give the user experience of very short program start times, as the user interface (presentation) can be shown before the abstraction has fully initialized.

This does not seem to fit the description of ADR very well.

### Model-View-ViewModel

[MVVM](https://en.wikipedia.org/wiki/Model_View_ViewModel) seems more suited to desktop applications than web interactions. (Recall that ADR is specifically intended for web interactions.)

### Resource-Method-Representation

I had not heard of [RMR](http://www.peej.co.uk/articles/rmr-architecture.html) before it was pointed out to me by [ircmaxell on Reddit](http://www.reddit.com/r/PHP/comments/24s8yn/actiondomainresponse_a_tentative_mvc_refinement/cha8jo1)

ADR and RMR seem very similar, and seem to map well to each other:

    Resource       <--> Domain
    Method         <--> Action
    Representation <--> Responder

However, some nuances of RMR make me think they are still somewhat different from each other. For example:

> So in an OO language, a HTTP resource can be thought of as an object with private member variables and a number of public methods that correspond to the standard HTTP methods. From an MVC point of view, a resource can be thought of as a model with a bit of controller thrown in.

To me, this seems like mixing concerns just a bit too much. I'd rather see a clearer of the domain model from the action being applied to the domain.

> So the representation is like a view in MVC, we give it a resource object and tell it to serialize the data into it's output format.

There seems to be no allowance for other kinds of HTTP responses, such as "Not Found".  That kind of response is clearly not a representation of the requested resource.

Having said all that, it may be that ADR could be considered an expanded or superset variation of RMR, one where a _Resource_ and an action one can perform on it are cleanly separated into _Domain_ and a _Action_, and where the representation of the response is handled by a _Responder_.


### Examples of MVC vs ADR

#### MVC Starting Point

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

You can review an extended set of sample ADR code [here](/pmjones/mvc-refinement/tree/master/example-code).

## Benefits and Drawbacks

One benefit overall is that the pattern more closely describes the day-to-day work of web interactions. A request comes in and gets dispatched to an action; the action interacts with the domain, and then builds a response. The response work, including both headers and content, is cleanly separated from the action work.

One drawback is that we end up with more classes in the application. Not only does each _Action_ go in its own class, each _Responder_ also goes in its own class.

This drawback may not be so terrible in the longer term. Invididual classes may lead cleaner or less-deep inheritance hierachies. It may also lead to  better testability of the _Action_ separate from the _Responder_. These will play themselves out differently in different systems.

## Missing Elements

 This pattern concentrates on the refinement of _Model-View-Controller_, and not on the entirety of web applications. Therefore, it intentionally omits some elements commonly found in web applications, particularly anything related to a _Front Controller_.

The ADR pattern does not describe a routing or dispatching element, nor how the _Action_ and _Responder_ relate to a dispatcher. Routing and dispatching are more properly the purview of _Front Controller_, and there are many ways for the _Action_, _Responder_, and any _Front Controller_ mechanism to interact:

- the _Action_ may invoke the _Responder_ directly, which then returns a response;

- the _Responder_ and response may be shared with a _Front Controller_ so that it can invoke them directly;

- the _Action_ may return a _Responder_, which is then invoked to return a response, which is then invoked to send itself;

- and so on.

The ADR pattern does not describe any sort of pre-filter or request-validation element.  These things may either be part of the execution chain *before* an _Action_ is invoked, or they may be part of the invoked _Action_, or they may be injected into an _Action_.  The pre-filter or request-validation logic may or may not bypass the _Action_ to invoke the _Responder_ directly, or it may deliver a response of its own, or it may invoke a separate _Action_ as a result of its logic, and so on.

* * *

Note from MWOP, to be revised for the final version of this document:

>I think the main thing is to note that the Front Controller may be
able to send a response without invoking an ADR triad, and that the
Action may be able to return a generic and/or application-generic
response without touching on the domain -- noting the activities I
mentioned above as potential candidates for such treatment.


