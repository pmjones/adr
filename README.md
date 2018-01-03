# Action-Domain-Responder

_Action Domain Responder_ organizes a single user interface interaction between an HTTP client and a HTTP server-side application into three distinct roles.

![ADR](adr.png)

## Components

_Action_ is the logic that connects the _Domain_ and _Responder_. It collects input from the HTTP Request to interact with the _Domain_, then passes the HTTP Request and any _Domain_ output to the _Responder_.

_Domain_ is an entry point to the domain logic forming the core of the application, modifying state and persistence as needed. Think of this in terms of Service Layer, Transaction Script, Application Service, and the like.

_Responder_ is the presentation logic to build an HTTP Response from the HTTP Request and _Domain_ output. It deals with the status codes, headers and cookies, content, formatting and transformation, templates and views,and so on.

## Collaborations

1. The web handler receives an HTTP Request and dispatches it to an _Action_.

1. The _Action_ invokes the _Domain_, collecting any required inputs to the _Domain_ from the HTTP Request.

1. The _Action_ then invokes the _Responder_ with the HTTP Request and the output from the _Domain_ (if any).

1. The _Responder_ builds an HTTP Response using the data fed to it by the _Action_.

1. The _Action_ returns the HTTP Response to the web handler sends the HTTP Response.

Notes:

- The web handler may pass control directly to a _Responder_ without passing through an _Action_, in particular when there is no _Domain_ interaction needed.

- The web handler may modify the HTTP Request before dispatching it to the _Action_; likewise, it may modify the HTTP Response returned by the _Action_.

## Supporting Elements

The ADR pattern is a user interface alternative to MVC. Neither ADR nor MVC are entire application architectures themselves. This section describes other components, collaborations, and patterns that might be used in conjunction with ADR within an application architecture.

### HTTP Request and Response

Because ADR is an HTTP-specific user interface pattern, the presence of HTTP Request and Response elements is presumed as a sine qua non.

## Front Controller

The ADR pattern does not describe a routing, dispatching, or middleware element. Those elements are more properly the purview of _Front Controller_.  When it comes to ADR, a_Front Controller_ may, among other things:

- Inspect and/or modify the HTTP Request URL with a router

- Dispatch the HTTP Request to an Action, and receive back the HTTP Response.

- Dispatch the HTTP Request directly to a Responder, and receive back the HTTP Response.

- Bypass any ADR subsystem entirely in favor of some other subsystem; for example:

    - when routing fails due to URL path or HTTP method mismatches
    - when the requested content-type is not available.
    - when authentication credentials or session identifiers are not present

- Pass the HTTP Request and Response through one or more layers of middleware.


## Presentation

    Template View
    Two Step View

## Domain Logic

    Domain Payload

## Authentication and Authorization

Authorization


## Commentary

### Front Controller Omission

The ADR pattern does not describe any pre-filter or request-validation elements, especially those that may be part of a _Front Controller_. Note that pre-filter or request-validation logic may or may not bypass the _Action_ to invoke the _Responder_ directly, or it may deliver a response of its own, or it may invoke a separate _Action_ as a result of its logic, and so on. Reasons for these short-circuiting behaviors may include:

### Other Commentary

The original blog post that led to this offering is at <http://paul-m-jones.com/archives/5970>.

Stephan Hochdörfer responded to that offering at <http://blog.bitexpert.de/blog/controller-classes-vs.-action-classes>; follow-up discussion appears at <http://paul-m-jones.com/archives/5987> and <http://www.reddit.com/r/PHP/comments/25y89a/stephan_hochdörfer_and_actiondomainresponder>.

Jon Leighton writes about a "Focused Controller" that maps well to the _Action_ element in ADR at <http://www.jonathanleighton.com/articles/2012/explaining-focused-controller>.

A follow-up post regarding _View_ vs _Responder_ is at <http://paul-m-jones.com/archives/5993> with Reddit commentary at <http://www.reddit.com/r/PHP/comments/26j3nf/the_template_is_not_the_view/> and <http://www.reddit.com/r/webdev/comments/26j5o9/the_template_is_not_the_view_xpost_from_rphp/>.

Akihito Koritama offers these notes: <https://koriym.github.io/blog/2014/06/08/action-domain-responder/>

## Acknowledgements

My thanks to the many people who have helped refine this offering, whether through questions, comments, criticism, or commendation. In no particular order, these include:

- Matthew Weier O'Phinney
- Hari KT
- Stephan Hochdörfer
- Adam Culp
- Dan Horrigan
- Josh Lockhart
- Beau Simensen
- Nate Abele, for opining that ADR should be described as an alternative to (not merely a refinement of) MVC <http://paul-m-jones.com/archives/5993#comment-2597>
