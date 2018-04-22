# Implementation Notes

_Action Domain Responder_ is a user interface pattern. It is not an entire application architecture in itself. With that in mind, this section describes other components, collaborations, and patterns which might be used in conjunction with ADR within an application architecture, along with notes and suggestions from ADR implementors.

## Action

The key heuristic of the _Action_ is it should contain almost no logic at all. It collects input from the HTTP request, passes that input to the _Domain_, then hands control over to the _Responder_.

The _Action_ is intentionally very spare. Handle any business logic in the _Domain_ and any presentation logic in the _Responder_.

The only exception here is the _Action_ may provide default values for user inputs when they are not present in the HTTP request; this is easily handled via ternaries rather than if/then blocks.

### How Should the _Action_ Receive the HTTP Request?

The HTTP Request might be injected into the _Action_ constructor, or it might be passed as a method argument invoking the _Action_ logic. Each is a valid way to pass the HTTP request, with its own tradeoffs.

### Should the _Action_ Validate Input?

A **user interface** component should not perform **domain logic** input validations.

It is better to pass the user input to the _Domain_, and let the domain logic perform the validation. The _Domain_ can report back if the inputs are invalid, perhaps with domain-specific messages.

### Can the _Action_ Build a DTO as the Domain Input?

Yes. However, note that building the _data transfer object_ has to be completed without conditionals. If building the DTO can cause exceptions or errors, it is better to pass the necessary inputs to the _Domain_, then let the _Domain_ build the DTO, and then pass that DTO to the necessary domain logic.

### Can the _Action_ Use a Command Bus?

Command Bus is a domain logic pattern, not a user interface pattern, and should be used in the _Domain_, not in the _Action_. For a longer discussion of this, see [Command Bus and Action-Domain-Responder](http://paul-m-jones.com/archives/6268).

### Should the _Action_ Handle Domain Exceptions?

No. A **user interface** element should not be in charge of handling **domain logic** exceptions. The _Domain_ should handle its own exceptions, and report that back the _Action_, perhaps as part of a _Domain Payload_.

### Can the _Action_ Manipulate the _Responder_ Before Invoking It?

Some _Responder_ implementations may require the _Action_ set data elements into it individually, or require other method calls before it can be invoked.

For example, instead of this:

    return $this->responder->createResponse($request, $payload);

The following is also reasonable:

    $this->responder->setRequest($request);
    $this->responder->setPayload($payload);
    return $this->responder->createResponse();

However, take care there is no conditional logic required.  All presentation logic should go in the _Responder_; the _Action_ should only pass values to it and then invoke it.

### Can the _Action_ Return the _Responder_ Instead of Invoking It?

An earlier draft of this pattern noted, "the _Action_ may return a _Responder_, which is then invoked to return a response, which is then invoked to send itself."

Why would code calling the _Action_ need back anything other than a response? If there is any logic to modifying how the _Responder_ builds the HTTP response, it would best be incorporated or composed into the _Responder_.

As such, returning the _Responder_ to be invoked by something else to create the response (instead of returning a response directly) still maintains the separations properly, but should be considered an inferior form of the pattern.

### Can There Be a Single _Action_ for the Entire Interface?

Because _Action_ logic is purposely very simple and always occurs in the same order, it is possible to create a single generic _Action_ class which handles all user interface interactions. However, this may preclude the use of dependency injection systems. In turn, the implementor will have to figure out how to give the _Action_ access to the necessary the _Domain_ and _Responder_ logic, and perhaps how to collect input in different cases.

One such implementation is [Arbiter](https://github.com/arbiterphp/Arbiter.Arbiter). In short, a router or other web handler component builds an _Action_ [description](https://github.com/arbiterphp/Arbiter.Arbiter/blob/1.x/src/Action.php) composed of an input-collection callable, a domain-logic callable, and a response-building callable. The _Action_ [handler](https://github.com/arbiterphp/Arbiter.Arbiter/blob/1.x/src/ActionHandler.php) then invokes the callables in the proper order, resolving object instances as needed along the way.

## Domain

Remember the _Domain_ in ADR is an **entry point** into domain logic. The domain logic itself might be as simple as a single infrastructure interaction, or it might be a complex layer of interconnected services and objects notifying and observing each other before returning their final status.

Neither the _Action_ nor the _Responder_ care about the internal workings of the _Domain_, only that the _Domain_ can be invoked by the _Action_, and the result (if any) can be presented by the _Responder_.

### What Goes in the _Domain_?

ADR is a **user interface** pattern. Anything that has to do with reading the HTTP request goes in the _Action_; anything that has to do with building the HTTP response goes in the _Responder_.  Everything else, then, must go in the _Domain_.

One easy heuristic to remember is this: "If it touches storage, it goes in the _Domain_." (Storage includes any infrastructure or external resource: database, cache, filesystem, network, etc.)

Now, what if the storage interaction is to retrieve only presentation values; for example, translations for template text, which require no business logic at all? It may be reasonable for the _Responder_ to retrieve such values itself.

Even so, for the sake of a consistent heuristic, I opine it would be better for the _Action_ to pass to the _Domain_, as part of the _Domain_ input, an instruction to return those values as part of its returned payload.

### Proper Separation From User Interface

The _Domain_ should be separated completely from any HTTP-specific dependencies. That is, although it is reasonable for the _Action_ and _Responder_ to depend on the _Domain_, it is **not** reasonable for the _Domain_ to depend on any particular user interface.

As a test for this, try to find out how well the _Domain_ entry point would work with a command line interface instead of an HTTP interface. If the answer is "not easily" then the _Domain_ is probably too dependent on HTTP.

### How Should the _Domain_ Receive Input?

The signature of the _Domain_ entry point into the domain layer may be anything its logic requires from the user input: one or more separate arguments (type-hinted or not), a _Data Transfer Object_, even a catch-all array of all possible inputs from the HTTP request. What it should **not** receive is the HTTP request itself.

### What Should the _Domain_ Return?

It depends on the specifics of the domain logic, and on what the application requires for output to the user. This is necessarily particular to the core application concerns, and should not be dictated by the user interface.

The _Domain_ might not return anything at all in some cases. In others, it might return a simple value or a single object. In yet others, it might return a complex collection of object and values. Any or all of these might further be wrapped in a _Domain Payload_, which can simplify the transfer and interpretation of domain results and status across the user interface boundary.

It is then up to the _Responder_ to figure out how to build the HTTP response presenting the _Domain_ results.

## _Responder_

### How Does the _Responder_ Create the HTTP Response?

The _Responder_ may create and return an HTTP Response object of its own, whether by `new` or by an injected factory.

Alternatively, the _Responder_ may receive an HTTP Response object injected as a constructor parameter. The _Responder_ can then modify the injected Response object before returning it to the _Action_. In fact, if the HTTP Response object is shared throughout the existing system, the _Responder_ might not have to return the HTTP Response object at all, since the web handler may already have access to the shared object.

Each of these approaches has its own tradeoffs and is a valid implementation of the _Responder_.

### Generic or Parent _Responders_

It may be that response-building logic is so straightforward a single _Responder_ can handle all response-building work for the entire user interface. Likewise, it may be that a parent _Responder_ with base functionality could be extended by a child _Responder_ with added or modified functionality.

These are both acceptable implementations for ADR. The important point is not the simplicity or complexity of the response-building work, but that such work is fully separated from the _Action_ and the _Domain_.

### Templates and Transformations

The use of _Template View_, _Two Step View_, and _Transform View_ implementations within a _Responder_ is perfectly reasonable for building the HTTP Response content.

### Widgets and Panels

Some presentations may have several different panels, content areas, or subsections which have different data sources. These may be handled through a template system or other presentation subsystem, but they should not be in charge of retrieving their own data from the _Domain_. Instead, the _Domain_ should provide all the needed data for these widgets when the _Action_ invokes it.

For a beginning example of how to do so, see [Solving The “Widget Problem” In ADR](http://paul-m-jones.com/archives/6760).

## Other Topics

### Content Negotiation

Content negotiation, since it deals with how to present the body content of the HTTP response, most properly belongs in the _Responder_. For example, a _Responder_ may negotiate the content type of the HTTP response body to be presented based on the HTTP request `Accept` header.

However, it would be inefficient for an HTTP request to pass through the web handler, _Action_, _Domain_ (possibly involving expensive resource usage), then finally to the _Responder_, only for the _Responder_ to determine it cannot fulfill any of the acceptable content types.

As such, it may be reasonable for the web handler, after routing a request, to look ahead to the _Responder_ for the routed _Action_ and determine if the _Responder_ can provide any of the acceptable content types in the request.

This would not be a negotiation per se, merely a check to see if the `Accepts` header contains any of the types the _Responder_ states it can handle. Since the web handler and the _Responder_ all exist in the user interface layer, this does not represent an inappropriate dependency.

For an example, see the Radar framework; specifically:

- The default _Responder_ class, which reports the content types it can respond to ([code](https://github.com/radarphp/Radar.Adr/blob/1.x/src/Route.php#L121-L147)).

- The route class, which associates a _Responder_ with a route ([code](https://github.com/radarphp/Radar.Adr/blob/1.x/src/Route.php#L121-L147)).

- The router rule which tests for acceptability ([code](https://github.com/auraphp/Aura.Router/blob/3.x/src/Rule/Accepts.php)).


### Sessions

Recall the above heuristic, "If it touches storage, it belongs in the _Domain_."  Sessions read and write from storage (whether filesystem, database, or cache). Therefore, all session work **should** be done in the _Domain_. For example:

- An _Action_ can read the incoming session ID (if any) and pass it as an input to the _Domain_.

- The _Domain_ can then use that ID to read and write to a session store (or create a new one), and later return the session ID and related data as part of its results, perhaps in a _Domain Payload_.

- The _Responder_ can then read the session ID and data from the domain result and set a cookie based on it.

However, some session implementations may be so thoroughly intertwined in a language or framework as to make them unsuitable for pure _Domain_ work. For example, the PHP session extension combines multiple concerns:

- `session_start()` reads the session ID from the incoming request data directly, then reads the `$_SESSION` superglobal data from storage itself. This combines the concerns of input collection and infrastructure interactions.

- `session_commit()` writes the `$_SESSION` superglobal data back to storage, and writes a cookie header directly to the outgoing response buffer. The combines the concerns of infrastructure interactions and presentations.

These kinds of situations make it difficult to intercept the automatic input collection process with an HTTP request object, the automatic output process with an HTTP response object, and of course the infrastructure and domain logic concerns.

This is not an ADR issue per se, but with how PHP session functions combine request-reading, storage-interaction, and response-sending. This becomes a problem only when using HTTP request/response objects that are not hooked into automated PHP behaviors.

If you *can* find a way around it, you should. One way is to [disable some elements of automatic session handling while leaving others in place](http://paul-m-jones.com/archives/6310). Another may be to avoid automatic session handling entirely, in favor of a domain-logic-friendly solution such as [the one presented here](https://www.futureproofphp.com/2017/05/02/best-way-handle-sessions-adr/).

Unfortunately, while there **ought** to a clean separation of input collection, reading from and writing to storage, and output presentation, doing so might not be practical under some language and framework constraints. In those cases, session work with HTTP request and response objects might have to be done in a way which is not as clean as we might prefer.

### Authentication

As with sessions, authentication work **should** be done in the _Domain_, since it is likely to touch storage at some point. For example:

- The _Action_ can collect credentials (including tokens or session IDs) from the HTTP request and pass them to the _Domain_ as input.

- The _Domain_ can interact with a storage system to check credentials for validity, expiration, and so on, and load up any user-specific data tied to those credentials.

- Based on authentication state, the _Domain_ may return early for anonymous or invalid users, or it may continue on to other domain logic, later returning the authentication state (or lack thereof) as part of its results, perhaps as part of a _Domain Payload_.

- The _Responder_ can then inspect user information in the domain results to present them appropriately.

#### Routing

If authentication work belongs in the _Domain_, how does one do routing? Often, developers will want to restrict some routes to authenticated users only. Does this mean the router, a user interface component, must have access to the domain layer?

The answer is maybe not. If the route condition is based on something like "Is the user authenticated at all, regardless of who it is?" then the answer is to check not for **authentication** but **anonymity**. That is, if the incoming HTTP request has no credentials or tokens associated with it, then the request is anonymous and can be routed appropriately. Authentication work involving credential storage and validation can then be removed from the router and placed into the _Domain_.

#### Applicability

There is a case to be made, because authentication identifies and manages a user interaction, it belongs in the user interface code. I am intuitively opposed to doing so, but it is a supportable point of view.

The problem then is, how is the user to be represented to the domain logic? We want to avoid the domain logic being dependent on a particular user interface component.

One solution here is for the user-interface code to create a user component provided by the domain layer. That user component can then be passed into the _Action_, whether as a constructor parameter, as an added HTTP request parameter, or in some other way. The _Action_ can then treat the user component as input to the _Domain_, and everything proceeds from there.

While I can imagine problems with that approach, it may be that no other is possible, given the constraints of the user interface framework presenting the results of the _Domain_ work.

### Authorization

Whereas authentication identifies a user, authorization controls what the user is allowed to do. Authorization work definitely belongs in the _Domain_.

As with authentication, how is one to do routing (a user interface concern) if the router cannot tell if the user is allowed to be dispatched along a particular route? The answer is to realize authorization is not over particular routes, but over the particular _Domain_ functionality to which those routes lead. It might also be over functionality regarding a specific resource within the _Domain_, in which case the resource must be loaded (in part or in whole) by the _Domain_ as part of the authorization check.

Thus, it is the _Domain_ that should check if the user is allowed to perform a particular function; if so, the _Domain_ continues, but if not, the _Domain_ can report back appropriately. This keeps the _Domain_, instead of a user interface component, as the proper authority over its functionality.

For an extended conversation about this, see this discussion on [Auth Gates and User Roles](https://www.reddit.com/r/PHP/comments/64910c/laravel_auth_gates_and_user_roles/?sort=old).

### Client-Side Use

The ADR pattern is not intended for client-side use. On the client side, there are many perfectly good pre-existing user interface patterns, such as the original _Model View Controller_, _Model View Presenter_, and so on.

### Command Line Use

Although ADR is envisioned as a user interface pattern for server-side applications, it can work as a user interface pattern for non-interactive command line applications. That is, if the command can be completed with only the values provided at the moment it is invoked, then:

- the _Action_ collects input from arguments, flags, and options as passed via the command line, invokes the _Domain_ to get back a result, and invokes to the _Responder_ to generate output (if any);

- the _Domain_ remains the same;

- the _Responder_, instead of generating an HTTP response, uses the _Domain_ result to write to STDOUT and STDERR.

The _Domain_, in this case, may use a logging system writing to STDOUT and STDERR as well, to provide continuous output back to the user. This is not necessarily a violation of ADR, since the logging is incidental to the operation of the _Domain_, but it does show how the pattern is not necessarily well-suited the environment.

For one example of this kind of non-interactive use, see [Cadre CliAdr](https://github.com/cadrephp/Cadre.CliAdr).

The ADR pattern *will not* work well with interactive command line applications which require additional user input after the command has been invoked.
