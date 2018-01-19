# Objections

## Applicability

Adherents to _Model View Controller_, knowing its origins as graphical user interface pattern, may rationalize its validity as a pattern on the web like so:

- The _Controller_ exists on the client. The client receives button clicks, mouse movements, and key presses, just like in the GUI pattern. The client _Controller_ sends an HTTP Request "event" to the server when the user clicks a link, or a submits a form, etc.

- The _Model_ exists on the server. When the client _Controller_ sends an HTTP Request "event" to the server, the client receives back the updated _Model_ data as an HTTP Response "event". This is like the GUI pattern as well, except that the client is interacting with remote, instead of local, resources encapsulating business logic.

- The _View_ exists on the client. The client, receiving the updated _Model_ data in the form of an HTTP Response, re-renders its screen with the changes, either as an entirely new page or as changes to an existing page.

While this might be a reasonable way to describe the user interface of the *client* side of the network interaction, it says nothing at all about the user interface on the *server* side of that interaction.

_Action Domain Responder_ addresses specifically user interface components and collaborations on the server side.

## Naming

Adherents to "Model 2" MVC may object that _Action Domain Responder_ could be described using variations on _Controller_ and _View_, instead of as a pattern of its own. For example:

- It could be that an _Action_ is a variation similar to _Page Controller_, and thus better termed _Action Controller_ , [_Focused Controller_](http://www.jonathanleighton.com/articles/2012/explaining-focused-controller/), or [_Invokable Controller_](https://github.com/woohoolabs/harmony#using-invokable-controllers). It would thereby fit the _Controller_ component of "Model 2" MVC.  (Indeed, the formal description for _Page Controller_ says that it represents a "page or action.")

- Likewise, it could be that _Responder_ is a variation similar to _Template View_ or _Transform View_, and thus better termed a _Response View_. It would thereby fit the _View_ portion of "Model 2" MVC.

Having said that, I believe those alternative formulations are not as good of a description of web-based interactions as ADR.

One reason has to do with the origins of MVC in graphical user interfaces, in particular the continuous interaction between many small MVC triads in memory to notify each other of updates. In ADR, no such continuous messaging occurs: the Action invokes the Domain, then it invokes the Responder, but neither the Domain nor Responder communicate any changes to each other. Also, there only one interaction, that of receiving the HTTP Request and then sending the HTTP Response in return.

Another reason is the "Model 2" exhortation to place all processing logic in the _Controller_. This strikes me as a poor separation of concerns; business logic should go in a domain layer, not in a user interface component.

In all, I believe it it better to make a clean break with the term MVC (with its graphical user interface history) and use a new term (ADR) that is specifically for server-side over-the-network request/response environments.

## Missing Components

Some critics feel that _Action Domain Responder_ is missing some elements.

### HTTP Request and Response

Because ADR is an HTTP-specific user interface pattern, the presence of HTTP Request and Response elements is presumed as a sine qua non.

### Front Controller

The ADR pattern does not describe routing, dispatching, or other web handler elements. Those elements are more properly the purview of _Front Controller_.

When it comes to ADR, a _Front Controller_ might ...

- pass control directly to a _Responder_ without passing through an _Action_, in particular when there is no _Domain_ interaction needed.

- modify the HTTP Request before dispatching it to the _Action_; likewise, it may modify the HTTP Response returned by the _Action_.

- inspect and/or modify the HTTP Request URL with a router

- dispatch the HTTP Request to an Action, and receive back the HTTP Response.

- dispatch the HTTP Request directly to a Responder, and receive back the HTTP Response.

- bypass any ADR subsystem entirely in favor of some other subsystem ...

    - when routing fails due to URL path or HTTP method mismatches.
    - when the requested content-type cannot be presented by a _Responder_.
    - when authentication credentials or session identifiers are not present.

... among other things.  This is to say that although _Action Domain Responder_ may be one part of the request/response user interface, it may not be the entirety of the user interface.
