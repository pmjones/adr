# MVC vs ADR

The dominant pattern describing server-side web application interactions is _Model View Controller_ (MVC). Note that it is not an application architecture in itself. It is a *user interface* pattern; it is a pattern than is used in application architectures, but is not itself an architecture. Cf. <http://paul-m-jones.com/archives/6079>.

As a user interface pattern, MVC is designed for client-side, in-memory, graphical user interfaces. It was not designed for server-side, over-the-network, HTTP Request/Response user interfaces. Server-side application architectures show only the faintest superficial resemblance to MVC, using the same names for similar components, but completely subverting the collaborations between those components.

Cf. <http://www.create.ucsb.edu/~stp/PostScript/mvc.pdf> among others for a description of how MVC works.

- The model is what you expect: "Models are those components of the system application that actually do the work ... the domain-specific software simulation or implementation of the application's central structure."

- The view is not: "views deal with everything graphical; they request data from their model, and display the data." Do your views ask for data from the model, e.g. asking it to pull data from the database?

- The controller is not: "Controllers contain the interface between their associated models and views and the input devices (keyboard, pointing device, time).  Controllers also deal with scheduling interactions with other view-controller pairs: they track mouse movement between application views, and implement messages for mouse button activity and input from the input sensor."

Further, there is a messaging system between all the components:

> Views and controllers of a model are registered in a list as dependents of the model, to be informed whenever some aspect of the model is changed. When a model has changed, a message is broadcast to notify all of its dependents about the change.

Do you have that in your server-side MVC system?

Finally, the interaction cycle is dramatically different:

> the user takes some input action and the active controller notifies the model to change itself accordingly. The model carries out the prescribed operations, possibly changing its state, and broadcasts to its dependents (views and controllers) that it has changed, possibly telling them the nature of the change. Views can then inquire of the model about its new state, and update their display if necessary. Controllers may change their method of interaction depending on the new state of the model.

Indeed, each element on the screen is a MVC triad.

That is not at all how a server-side over-the-network HTTP-oriented application operations.

It was Sun that misapproariate these components and collaborations, under the name Model 2. For a good written history of this, see <https://lostechies.com/derekgreer/2007/08/25/interactive-application-architecture/>, esp. "The Model-View-Controller Pattern for Web Applications." This was solidified by Struts.

We can see from Fowler in his [GUI Architectures](http://martinfowler.com/eaaDev/uiArchs.html) essay that "there's not just one view and controller, you have a view-controller pair for each element of the screen, each of the controls, and the screen as a whole." This is the primary element of semantic diffusion when applying MVC to server-side web applications. An entire HTTP Request is received by the server-side application, and an entire HTTP Response is delivered by it in return.

That is, your server-side application code does not present a graphical user interface of interconnected screen elements all communicating with each other back and forth. Instead, your server-side application code presents an HTTP response message. There is *exactly one* interaction: receive the request, return the response.

Thus, ADR is offered as an alternative to the _Model View Controller_ (MVC) user interface pattern, specifically for HTTP server-side applications.

Is ADR really just MVC in drag?  We can see that the ADR terms map very neatly to MVC terms:

    Model      <--> Domain
    View       <--> Responder
    Controller <--> Action

The two seem superficially similar, but (with the exception of _Model_ vs _Domain_) there are subtle but substantial differences.

(MVC means lots of different hings: https://www.reddit.com/r/webdev/comments/4d07l8/is_this_mvc/?sort=old)

### _Model_ vs _Domain_

There are few if any significant differences here, other than that the _Responder_ does not interact with the _Domain_. The _Responder_ might use domain objects like entities and collections, but only for presentation purposes. It does not itself request new information from the domain or send information back to the domain.

Thus, the only difference is in the name. Using the word _Domain_ instead of _Model_ is intended to make implementors think of "POEAA" Domain Logic patterns such as _Service Layer_ and _Transaction Script_, or "Domain Driven Design" patterns such as _Application Service_ or _Use Case_.

Further, in MVC, there are lots of Models being presented continuously. Whereas in ADR, the Domain is an entry point into whatever it is that does the work (Transaction Script, Service Layer, Application Service, etc).


### _View_ vs _Responder_

In an MVC system, a _Controller_ method will usually generate body content via a _View_ (e.g. a _Template View_ or a _Two Step View_). The _Controller_ then injects the generated body content into the response.  The _Controller_ action method will manipulate the response directly to the status code, headers, cookies, and so on.

Some _Controller_ action methods may present alternative content-types for the same domain data. Because these alternatives may not be consistent over all the different methods, this leads to the presentation logic being somewhat different in each method, each with its own preconditions.

However, in a server-side web application, the presentation being delivered as output is not merely the body of the HTTP Response. The presentation is is the entire HTTP Response, including the HTTP status, headers, cookies, and so on. As such, doing any sort of HTTP Response building work in a _Controller_ indicates a mixing of concerns.

To fully separate the presentation logic, each _Action_ in ADR invokes a _Responder_ to build the HTTP Response. When the _Action_ is done with the _Domain_, it delivers the HTTP Request and the _Domain_ output to the _Responder_ and then hands off to the _Responder_ completely. The _Responder_ is entirely in charge of setting headers, picking content types, rendering templates, and so on.

Note that a _Responder_ may incorporate a _Template View_, _Two Step View_, _Transform View_, or any other kind of _View_ system. Note also that a _Responder_ may be used by more than one _Action_. The point here is that the _Action_ leaves all header and content work to the _Responder_, not that there must be a different _Responder_ for each different _View_.

### _Controller_ vs _Action_

In common usage, most _Controller_ classes in an server-side MVC system contain several methods corresponding to different actions. Because these differing action methods reside in the same _Controller_, the _Controller_ ends up needing additional wrapper logic to deal with each action method properly, such as pre- and post-action hooks.  A notable exception here is in micro-frameworks, where each _Controller_ is an individual closure or invokable object, mapping more closely to a single _Action_ (cf. [Slim](http://slimframework.com)).

In an ADR system, a single _Action_ is the main purpose of a class or closure. Each _Action_ would be represented by a individual class or closure.

The _Action_ interacts with the _Domain_ in the same way a _Controller_ interacts with a _Model_, but does not interact with a _View_ or template system. It sends data to the _Responder_ and invokes it so that it can build the HTTP Response.

These limitations on the _Action_ make it a very simple bit of logic. It does only three things:

1. Collect input from the HTTP Request.
2. Invoke the _Domain_ with that input.
2. Invoke the _Responder_ with the HTTP Request and the _Domain_ output.
3. Return the response built by the Responder.

All other logic, including all forms of input validation, error handling, and so on, are therefore pushed out of the _Action_ and into the _Domain_ (for domain logic concerns) or the _Responder_ (for presentation concerns).

### Alternative Formulations

This pattern may be better formulated as variations on _Controller_ and _View_ from _Model View Controller_ instead of a pattern of its own.

That is, it may be that _Action_ is a variation similar to _Page Controller_, and thus better termed an _Action Controller_. It would thereby fit into the _Controller_ portion of MVC.  (Indeed, the formal description for _Page Controller_ says that it represents a "page or action.")

Likewise, it may be that _Responder_ is a variation similar to _Template View_ or _Transform View_, and thus better termed a _Response View_. It would thereby fit into the _View_ portion of MVC.

Having said that, I believe those alternative formulations are probably not as good of a description of web-based interactions as is ADR. This is mostly because of the implicit interactions between _Model_ and _View_ in MVC.  In MVC, the _View_ updates the _Model_. In ADR, the _Responder_ does not update the _Domain_.

### Note from LordOfWorms

Via lordofworms in private message on Reddit, regarding "view" as "what the user
sees":

> Interesting proof that something is up with MVC in a web context:
>
> ```
> public function show($id)
> {
>     return $this->response->view('template.name', ['users' => $this->users->get($id)]);
> }
> ```
>
> vs
>
> ```
> public function show($id)
> {
>     return $this->response->json(['users' => $this->users->get($id)]);
> }
> ```
>
> save involvement of a template, there is no difference from the server
> application's point of view. It returns a response in both instances and only
> the body changes.
>
> Another one (specifically on the UI point). If you return a rendered template in
> your view, you are returning HTML, not building UI. The UI isn't built until the
> browser gets the HTML and parses it... then loads up any additional resources
> necessary to run the UI and renders the result. The HTML message body is no more
> a UI than a json payload - both are simply serialization formats.
>
> I'm not quite sure why HTML is treated as a special case. In any sense, any of
> the "MVC Frameworks" are equally capable of producing html output or json
> output, using nearly identical mechanisms to achieve it. So they are the same
> thing. Problem is with calling it MVC to begin with ;)
