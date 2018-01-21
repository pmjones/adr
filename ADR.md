# Action Domain Responder

_Action Domain Responder_ is offered as an alternative to the "Model 2" misappropriation (for server-side over-the-network request/response interfaces) of the original MVC user interface pattern (for client-side in-memory graphical user interfaces). ADR is a user interface pattern specifically intended for server-side applications operating in an over-the-network, request/response environment.

Aligning expectations, and factoring concerns, away from the modern derivations of "Model 2" MVC toward _Action Domain Responder_ is not difficult. Here is one way of working through the change in approach.

## _Model_ vs _Domain_

There are few if any significant differences here, other than that the _Responder_ does not interact with the _Domain_. The _Responder_ might use domain objects like entities and collections, perhaps wrapped in a _Domain Payload_, but only for presentation purposes. It does not itself request new information from the domain or send information back to the domain.

Thus, the main difference is in the name. Using the word _Domain_ instead of _Model_ is intended to make implementors think of "POEAA" Domain Logic patterns such as _Service Layer_ and _Transaction Script_, or "Domain Driven Design" patterns such as _Application Service_ or _Use Case_.

Further, remember that in the original MVC, there are lots of Models being presented continuously. In "Model 2" MVC, the _Model_ is almost completely undefined. In ADR, the _Domain_ is defined as an entry point into whatever it is that does the domain work (Transaction Script, Service Layer, Application Service, etc).

## _View_ vs _Responder_

In a "Model 2" MVC system, a _Controller_ method will usually generate body content via a _View_ (e.g. a _Template View_ or a _Two Step View_). The _Controller_ then injects the generated body content into the response.  The _Controller_ action method will manipulate the response directly to the status code, headers, cookies, and so on.

Some _Controller_ action methods may present alternative content-types for the same domain data. Because these alternatives may not be consistent over all the different methods, this leads to the presentation logic being somewhat different in each method, each with its own preconditions.

However, in a server-side web application, the presentation being delivered as output is not merely the *body* of the HTTP Response. The presentation is the *entire* HTTP Response, including the HTTP status, headers, cookies, and so on. As such, doing any sort of HTTP Response building work in a _Controller_ indicates a mixing of concerns.

To fully separate the presentation logic, each _Action_ in ADR invokes a _Responder_ to build the HTTP Response. The _Responder_ is entirely in charge of setting headers, setting the body content, picking content types, rendering templates, and so on.

Note that a _Responder_ may incorporate a _Template View_, _Two Step View_, _Transform View_, or any other kind of body content building system.

Note also that a particular _Responder_ may be used by more than one _Action_. The point here is that the _Action_ leaves all header and content work to the _Responder_, not that there must be a different _Responder_ for each different _Action_.

In trivial cases, it may be reasonable to collect different _Responder_ logic, e.g. corresponding to the presentation logic for different _Actions_, into a single class as well. Doing so may be considered a valid, but degenerate or inferior, implementation of the pattern.

## _Controller_ vs _Action_

In common usage, most "Model 2" MVC _Controller_ classes contain several methods corresponding to different actions. Because these differing action methods reside in the same _Controller_, the _Controller_ ends up needing additional wrapper logic to deal with each action method properly, such as pre- and post-action hooks.  Additionally, different action methods may have different dependencies, leading to over-long constructors and/or attempts at "action injection" of dependencies. Notable exceptions here are:

-  micro-frameworks, where each _Controller_ is an individual closure or invokable object, mapping more closely to a single _Action_ (cf. [Slim](http://slimframework.com))

- [Hanami](http://hanamirb.org/guides/1.0/actions/overview/), where "an action is an object, while a controller is a Ruby module that groups them."

In an ADR system, a single _Action_ is the main purpose of a class or closure. Each _Action_ would be represented by a individual class or closure.

The _Action_ interacts with the _Domain_ in the same way a _Controller_ interacts with a _Model_, but does not interact with a _View_ or template system. It sends data to the _Responder_ and invokes it so that it can build the HTTP Response.

These limitations on the _Action_ make it a very simple bit of logic. It does only these things:

1. collects input from the HTTP Request (if needed);
2. invokes the _Domain_ with those inputs (if required) and retains the result;
2. invokes the _Responder_ with any data the _Responder_ needs to build an HTTP Response (typically the HTTP Request and/or the _Domain_ invocation results).

All other logic, including all forms of input validation, error handling, and so on, are therefore pushed out of the _Action_ and into the _Domain_ (for domain logic concerns) or the _Responder_ (for presentation logic concerns).

In trivial cases, it may be acceptable to collect different _Action_ logic into a class as methods, provided that they keep the _Domain_ and _Responder_ separations in place.  Doing so may be considered a valid, but degenerate or inferior, implementation of the pattern.

## Tradeoffs

Of course, nothing comes with pros and cons. [What are the tradeoffs of the ADR pattern?](./TRADEOFFS.md)
