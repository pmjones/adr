# MVC vs ADR

The dominant pattern name used to describe server-side web application interactions is _Model View Controller_ (MVC). However, it turns out that what server-side developers have come to think of as "MVC" is not actually MVC at all.

First, it is important to remember that MVC is not an application architecture in itself; it is a *user interface* pattern. That is, MVC is a pattern than is used in application architectures, but is not itself an architecture. (Cf. <http://paul-m-jones.com/archives/6079>.)

As a user interface pattern, MVC was originally proposed in 1979 for client-side, in-memory, graphical user interfaces. This is obvious from even a cursory review of MVC literature and papers:

- [MVC: Xerox PARC 1978-79](https://heim.ifi.uio.no/~trygver/themes/mvc/mvc-index.html)
- [Applications Programming in Smalltalk-80:
How to use Model-View-Controller (MVC)](https://web.archive.org/web/20150518095937/http://st-www.cs.illinois.edu/users/smarch/st-docs/mvc.html)
- [A Description of the Model-View-Controller User Interface Paradigm in the Smalltalk-80 System](http://www.create.ucsb.edu/~stp/PostScript/mvc.pdf)
- [Model-View-Controller (MVC) Architecture](https://www.scribd.com/document/130366010/125469296-Model-View-Controller-MVC-Architecture)

Here is what [Krasner and Pope](http://www.create.ucsb.edu/~stp/PostScript/mvc.pdf), among others, give as a description of how MVC is supposed to work:

- The _Model_ sounds like what server-side developers already expect: "Models are those components of the system application that actually do the work ... the domain-specific software simulation or implementation of the application's central structure."

- But the _View_ doesn not: "views deal with everything graphical; they request data from their model, and display the data." Do your views ask for data from the model, e.g. asking it to pull data from the database? Typically the answer is "no"; indeed, we usually think that a view interacting with the database means concerns have been poorly separated.

- Likewise, neither does the _Controller_: "Controllers contain the interface between their associated models and views and the input devices (keyboard, pointing device, time).  Controllers also deal with scheduling interactions with other view-controller pairs: they track mouse movement between application views, and implement messages for mouse button activity and input from the input sensor."

To continue confounding server-side developer expectations, there is not just one MVC triad for the application as a whole. Instead, each element on the screen is an MVC triad: one MVC triad for each text field, each button, each popup menu, and so on. Martin Fowler in his [GUI Architectures](http://martinfowler.com/eaaDev/uiArchs.html) essay confirms that "there's not just one view and controller, you have a view-controller pair for each element of the screen, each of the controls, and the screen as a whole." So there are lots of little models, views, and controllers operating as user interfaces within the application as a whole.

Further, there is a subject/observer messaging system that connects all these little MVC triads together, so that they can continuously notify each other of changes. Back to Krasner and Pope:

> Views and controllers of a model are registered in a list as dependents of the model, to be informed whenever some aspect of the model is changed. When a model has changed, a message is broadcast to notify all of its dependents about the change.

Finally, the interaction cycle is dramatically different from what a server-side developer is used to. Krasner and Pope again:

> [T]he user takes some input action and the active controller notifies the model to change itself accordingly. The model carries out the prescribed operations, possibly changing its state, and broadcasts to its dependents (views and controllers) that it has changed, possibly telling them the nature of the change. Views can then inquire of the model about its new state, and update their display if necessary. Controllers may change their method of interaction depending on the new state of the model.

That is not at all how a server-side over-the-network HTTP-oriented user interface operates.

Server-side applications do not present a graphical user interface of many interconnected screen elements all continuously communicating with each other back and forth in memory, responding to mouse movements, button clicks, and key presses. Instead, the server-side application is centered around *exactly one* user interface interaction: it receives an entire HTTP Request, and delivers an entire HTTP Response in return.

All of this is to say that MVC was never intended for server-side, over-the-network, HTTP Request/Response user interfaces.  Any reference to MVC on the server side exists only because Sun Microsystems appropriated the components of _Model View Controller_ for their own applications, then subverted the purpose of and collaborations between those components. (For a well-written history of this, see <https://lostechies.com/derekgreer/2007/08/25/interactive-application-architecture/>, esp. "The Model-View-Controller Pattern for Web Applications.")

This appropriation and subversion was codified by Sun under the name "Model 2" and later solidified by Struts. For all intents and purposes, server-side developers now equate "Model 2" with "MVC" on the server side as a result, even though any connection between the two is misleading at best and entirely unfounded at worst.

Thus, _Action Domain Responder_ is offered as an alternative to the "Model 2" misappropriation of the original MVC user interface pattern for graphical user interfaces. ADR is specifically for server-side applications operating in an over-the-network, request/response environment.

## Aligning With ADR

It turns out that aligning expectations, and factoring concerns, away from "Model 2" MVC toward _Action Domain Responder_ is not difficult. Here is one way of working through the change in approach.

### _Model_ vs _Domain_

There are few if any significant differences here, other than that the _Responder_ does not interact with the _Domain_. The _Responder_ might use domain objects like entities and collections, perhaps wrapped in a _Domain Payload_, but only for presentation purposes. It does not itself request new information from the domain or send information back to the domain.

Thus, the main difference is in the name. Using the word _Domain_ instead of _Model_ is intended to make implementors think of "POEAA" Domain Logic patterns such as _Service Layer_ and _Transaction Script_, or "Domain Driven Design" patterns such as _Application Service_ or _Use Case_.

Further, remember that in the original MVC, there are lots of Models being presented continuously. In "Model 2" MVC, the _Model_ is almost completely undefined. In ADR, the _Domain_ is defined as an entry point into whatever it is that does the domain work (Transaction Script, Service Layer, Application Service, etc).

### _View_ vs _Responder_

In a "Model 2" MVC system, a _Controller_ method will usually generate body content via a _View_ (e.g. a _Template View_ or a _Two Step View_). The _Controller_ then injects the generated body content into the response.  The _Controller_ action method will manipulate the response directly to the status code, headers, cookies, and so on.

Some _Controller_ action methods may present alternative content-types for the same domain data. Because these alternatives may not be consistent over all the different methods, this leads to the presentation logic being somewhat different in each method, each with its own preconditions.

However, in a server-side web application, the presentation being delivered as output is not merely the *body* of the HTTP Response. The presentation is the *entire* HTTP Response, including the HTTP status, headers, cookies, and so on. As such, doing any sort of HTTP Response building work in a _Controller_ indicates a mixing of concerns.

To fully separate the presentation logic, each _Action_ in ADR invokes a _Responder_ to build the HTTP Response. The _Responder_ is entirely in charge of setting headers, picking content types, rendering templates, and so on.

Note that a _Responder_ may incorporate a _Template View_, _Two Step View_, _Transform View_, or any other kind of body content building system. Note also that a _Responder_ may be used by more than one _Action_. The point here is that the _Action_ leaves all header and content work to the _Responder_, not that there must be a different _Responder_ for each different _Action_.

### _Controller_ vs _Action_

In common usage, most "Model 2" MVC _Controller_ classes contain several methods corresponding to different actions. Because these differing action methods reside in the same _Controller_, the _Controller_ ends up needing additional wrapper logic to deal with each action method properly, such as pre- and post-action hooks.  Notable exceptions here are:

-  micro-frameworks, where each _Controller_ is an individual closure or invokable object, mapping more closely to a single _Action_ (cf. [Slim](http://slimframework.com))

- [Hanami](http://hanamirb.org/guides/1.0/actions/overview/), where "an action is an object, while a controller is a Ruby module that groups them."

In an ADR system, a single _Action_ is the main purpose of a class or closure. Each _Action_ would be represented by a individual class or closure.

The _Action_ interacts with the _Domain_ in the same way a _Controller_ interacts with a _Model_, but does not interact with a _View_ or template system. It sends data to the _Responder_ and invokes it so that it can build the HTTP Response.

These limitations on the _Action_ make it a very simple bit of logic. It does only these things:

1. collects input from the HTTP Request (if needed);
2. invokes the _Domain_ with those inputs (if required) and retains the result;
2. invokes the _Responder_ with any data the _Responder_ needs to build an HTTP Response (typically the HTTP Request and/or the _Domain_ invocation results).

All other logic, including all forms of input validation, error handling, and so on, are therefore pushed out of the _Action_ and into the _Domain_ (for domain logic concerns) or the _Responder_ (for presentation logic concerns).

## Alternative Formulations

I believe it is a good idea to make a clean break with the term MVC (with its graphical user interface history) and use a new term (ADR) that is specifically for the over-the-network request/response environment.

Even so, ADR might be formulated using variations on _Controller_ and _View_ from _Model View Controller_ instead of a pattern of its own. For example:

- It could be that an _Action_ is a variation similar to _Page Controller_, and thus better termed an _Action Controller_ or an [_Invokable Controller_](https://github.com/woohoolabs/harmony#using-invokable-controllers). It would thereby fit the _Controller_ component of "Model 2" MVC.  (Indeed, the formal description for _Page Controller_ says that it represents a "page or action.")

- Likewise, it may be that _Responder_ is a variation similar to _Template View_ or _Transform View_, and thus better termed a _Response View_. It would thereby fit the _View_ portion of "Model 2" MVC.

Having said that, I believe those alternative formulations are not as good of a description of web-based interactions as ADR. This is mostly because of the origins of MVC in graphical user interfaces, in particular the continuous communication between many small MVC triads in memory to notify each other of updates. In ADR, no such continuous messaging occurs: the Action invokes the Domain, then it invokes the Responder, but neither the Domain nor Responder communicate any changes to each other.
