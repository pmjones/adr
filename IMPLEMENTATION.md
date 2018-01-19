# Implementation Notes

_Action Domain Responder_ is a user interface pattern. It is not an entire application architecture in itself. With that in mind:

This section describes other components, collaborations, and patterns that might be used in conjunction with ADR within an application architecture, along with notes and suggestions from ADR implementors.

## Action

The key heuristic of the _Action_ is that it should contain almost no logic at all. It collects input from the HTTP Request, passes that input to the _Domain_, then hands control over to the _Responder_.

The _Action_ is intentionally very spare. Any business logic should be handled in the _Domain_, and any presentation logic should be handled in the _Responder_.

The only exception here is that the _Action_ may provide default values for user inputs when they are not present in the HTTP Request; this is easily handled via ternaries rather than if/then blocks.

### How To Pass The HTTP Request?

The HTTP Request might be injected into the _Action_ constructor, or it might be passed as a method argument invoking the _Action_ logic. Each is a valid way to pass the HTTP Request, with its own tradeoffs.

### Should The _Action_ Validate Input?

A *user interface* component should not perform *domain logic* input validations.

It is better to pass the user input to the _Domain_, and let the domain logic peform the validation. The _Domain_ can report back if the inputs are invalid, perhaps with domain-specific messages.

### Can The _Action_ Build A DTO As The Domain Input?

Yes. However, note that the DTO construction has to be completed without conditionals. If the DTO emits exceptions or errors, it is better to pass the necessary inputs to the _Domain_, then let the _Domain_ build the DTO.

### Can The _Action_ Use A Command Bus?

Command Bus is a domain logic pattern, not a user interface pattern, and should be used in the _Domain_, not in the _Action_. For a longer discussion of this, see [Command Bus and Action-Domain-Responder](Command Bus and Action-Domain-Responder).

### Should The _Action_ Handle Domain Exceptions?

No. A *user interface* element should not be in charge of handling *domain logic* exceptions. The _Domain_ should handle its own exceptions, and report that back the _Action_, perhaps as part of a _Domain Payload_.

### Can The _Action_ Return The _Responder_ Instead Of Invoking It?

An earlier draft of this pattern noted, "the _Action_ may return a _Responder_, which is then invoked to return a response, which is then invoked to send itself."

Why would code calling the _Action_ need back anything other than a response? If there is any logic to modifying how the _Responder_ builds the HTTP Response, it would best be incorporated or composed into the _Responder_.

As such, returning the _Responder_ to be invoked by something else to create the response (instead of returning a response directly) still maintains the separations properly, but should be considered an inferior form of the pattern.

## Domain

The main thing to remember is that the _Domain_ in ADR is an *entry point* into domain logic. The domain logic itself might be as simple as a single infrastructure interaction, or it might be a complex layer of interconnected services and objects notifying and observing each other before returning their final status.

Neither the _Action_ nor the _Responder_ care about the internal workings of the _Domain_, only that the _Domain_ can be invoked by the _Action_, and that the result (if any) can be presented by the _Responder_.

### What Goes In The _Domain_?

ADR is a *user interface* pattern. Anything that has to do with reading the HTTP request goes in the _Action_; anything that has to do with building the HTTP Response goes in the _Responder_.  Everything else, then, must go in the _Domain_.

One easy heuristic to remember is this: "If it touches storage, it goes in the _Domain_." ("Storage" includes any infrastructure or external resource: database, cache, filesystem, network, etc.)

Now, what if the storage interaction is to retrieve only presentation values; for example, translations for template text, that require no business logic at all? It may be reasonable for the _Responder_ to retrieve such values itself.

Even so, for the sake of a consistent heuristic, I opine that it would be better for the _Action_ to pass to the _Domain_, as part of the _Domain_ input, an instruction to return those values as part of its returned payload.

### Proper Separation From User Interface

The _Domain_ be separated completely from any HTTP-specific dependencies. That is, although it is reasonable for the _Action_ and _Responder_ to depend on the _Domain_, it is *not* reasonable for the _Domain_ to depend on any particular user interface.

As a test for this, try to find out how well the _Domain_ entry point would work with a command-line interface instead of an HTTP interface. If the answer is "not easily" then the _Domain_ is probably too dependent on HTTP.

### How Should The _Domain_ Receive Input?

The signature of _Domain_ entry point into the domain logic may be anything that logic requires: a single specific argument, a series of typehinted arguments, a data transfer object, even a catch-all array of all possible inputs from the HTTP Request. What it should *not* receive is the HTTP Request itself.

### What Should The _Domain_ Return?

It depends on the specifics of the domain logic, and on what the application requires for output to the user. This is necessarily particular to the core application concerns, and should not be dictated by the user interface.

The _Domain_ might not return anything at all in some cases. In others, it might return a simple value or a single object. In yet others, it might return a complex of object and values. Any or all of these might further be wrapped in a _Domain Payload_, which can simplify the transfer and interpretation of domain results and status across the user interface boundary.

It is then up to the _Responder_ to figure out how to build the HTTP Response presenting the _Domain_ results.

## _Responder_

### How Does The _Responder_ Create The HTTP Response?

The _Responder_ may create and return an HTTP Response object of its own, whether by `new` or by an injected factory.

Alternatively, the _Responder_ may receive an HTTP Response object injected as a constructor parameter. The _Responder_ can then modify the injected Response object before returning it to the _Action_. In fact, if the HTTP Response object is shared throughout the existing system, the _Responder_ might not have to return the HTTP Response object at all, since the web handler may already have access to the shared object.

Each of these approaches has its own tradeoffs, and is a valid implementation of the _Responder_.

### Generic or Parent _Responders_

It may be that response-building logic is so straightforward that a single _Responder_ can handle all response-building work for all _Action_ and _Domain_ interactions.  Likewise, it may be that a parent _Responder_ with base functionality would be extended by a child _Responder_ with added or modified functionality.

These are both acceptable implementations for ADR. The point is not the simplicity or complexity of the response-building work, only that such work is fully separated from the _Action_ and the _Domain_.

### Templates and Transformations

The use of _Template View_, _Two Step View_, and _Transform View_ implementations within a _Responder_ is perfectly reasonable for building the HTTP Response content.

### Widgets and Panels

Some presentations may have several different panels, content areas, or subsections that have different data sources. These may be handled through a template system or other presentation subsystem, but they should not be in charge of retrieving their own data from the _Domain_. Instead, the _Domain_ should provide all the needed data for these widgets when the _Action_ invokes it.

For a beginning example of how to do so, see [Solving The “Widget Problem” In ADR](http://paul-m-jones.com/archives/6760).

## Other Topics

### Content Negotiation

Content negotiation, since it deals with how to present the body content of the HTTP Response, most properly belongs in the _Responder_. Therefore, a _Responder_ may negotiate the content type of the HTTP Response based on the HTTP Request `Accept` header.

However, it would be inefficient for an HTTP Request to pass through the web handler, _Action_, _Domain_ (possibly involving expensive resource usage), then finally to the _Responder_, only for the _Responder_ to determine that it cannot fulfill any of the acceptable content types.

As such, it may be reasonable for the web handler, after routing a request, to "look ahead" to the _Responder_ for the routed _Action_ and determine if the _Responder_ can provide any of the acceptable content types in the request.

This would not a negotiaion per se, merely a check to see if the `Accepts` header contains any of the types that the _Responder_ states it can handle. Furhter, since the web handler and the _Responder_ all exist in the user interface layer, this does not represent an inappropriate depndency.

For one example of this, see the Radar framework, specifically:

- The default _Responder_ class, which reports the content types it cand respond to ([code](https://github.com/radarphp/Radar.Adr/blob/1.x/src/Route.php#L121-L147))

- The route class, which associates a _Responder_ with a route ([code](https://github.com/radarphp/Radar.Adr/blob/1.x/src/Route.php#L121-L147))

- The router rule that tests for acceptability ([code](https://github.com/auraphp/Aura.Router/blob/3.x/src/Rule/Accepts.php))


### Sessions

Recall the above heuristic that "If it touches storage, it belongs in the _Domain_."  Sessions read and write from storage (whether disk, database, or cache). Therefore, all session work *should* be done in the _Domain_. To do so:

- An _Action_ can read the incoming session ID (if any) and pass it as an input to the _Domain_.
- The _Domain_ can then use that ID to read & write to a stored session (or create a new one), and later return the session ID and related data as part of its results, perhaps in a _Domain Payload_.
- The _Responder_ can then read the session ID and data from the domain result and set a cookie based on it.

However, some session implementations may be so thoroughly intertwined in a language or framework as to make them unsuitable for pure _Domain_ work. For example, the PHP session extension combines multiple concerns:

- `session_start()` reads the session ID from the incoming request data directly, then reads the `$_SESSION` superglobal data from storage itself. This combines the concerns of input collection and infrastructure interactions.

- `session_commit()` writes the `$_SESSION` superglobal data back to storage, and emits a cookie header directly to the outgoing response buffer. The combines the concerns of infrastructure interactions and presentations.

These kinds of situations make it difficult to intercept the automatic input collection process with an HTTP Request object, the automatic output process with an HTTP Response object, and of course the infrastructure and domain logic concerns.

This is not an ADR issue per se, but a "problem" with how PHP session functions combine request-reading, storage-interaction, and response-sending.  The "problem" appears when you start using Request/Response objects that are not hooked into automated PHP behaviors.

As such, if you *can* find a way around it, you should. One way is to [disable some elements of automatic session handling while leaving others in place](http://paul-m-jones.com/archives/6310). Another may be to avoid automatic session handling entirely, in favor of a domain-logic-friendly solution such as [the one presented here](https://www.futureproofphp.com/2017/05/02/best-way-handle-sessions-adr/).

Unfortunately, while there *ought* to a clean separation of input collection, reading and writing from storage, and output presentation, doing so might not be practical under some language and framework constraints. Session work might have to be done in way that is not as clean as we might prefer.

### Authentication

As with sessions, authentication work *should* be done in the _Domain_, since it is likely to touch storage at some point. For example:

- The _Action_ can collect credentials (including tokens or session IDs) from the HTTP Request and pass them to the _Domain_ as input.

- The _Domain_ can interact with a storage system to check credentials for validity, expiration, and so on, and load up any user-specific data tied to those credentials.

- Based on authentication state, the _Domain_ may return early for anonymous or invalid users, or it may continue on to other domain logic, later returning the authentication state (or lack thereof) as part of its results, perhaps as part of a _Domain Payload_.

- The _Responder_ can then inspect user information in the domain results to present them appropriately.

#### Routing

If authentication work belongs in the _Domain_, how does one do routing? Often, developers will want to restrict some routes to authenticated users only. Does that mean the router, a user interface component, has to have access to the domain layer?

The answer is "maybe not."  If the route condition is based on something like "Is the user authenticated at all, regardless of who it is?" then the answer is to check not for authentication, but for *anonymity*. That is, if the incoming HTTP Request has no credentials or tokens associated with it, then the request is anonymous, and can be routed appropriately. Authentication work involving reading and validating credentials can then be removed from the router and placed into the _Domain_.

#### Applicability

There is a reasonable case to be made that, because authentication identitifies and managers a user interaction, it belongs in the user interface code. I am intuitively opposed to doing so, but it is a fair point of view.

The problem then is, how is the user to be represented to the domain logic? We want to avoid the domain logic being dependent on a particular user interface component.

One solution here is for the user-interface code to create a user component provided by the domain layer. The domain-layer user component can then be passed into the _Action_, whether as a constructor parameter, as an added HTTP Request parameter, or in some other way. The _Action_ can then treat the user component as input to the _Domain_, and everything proceeds from there.

While I can imagine problems with that approach, it may be that no other is possible, given the constraints of the user interface framework presenting the results of the _Domain_ work.

## Authorization

Whereas "authentication" identifies a user, "authorization" controls what that user is allowed to do. Authorization work *definitely* belongs in the _Domain_.

As with authentication, how is one to do routing (a user interface concern) if the router cannot tell if the user may be dispatched to a particular route?  The answer is to realize that authorization is not over particular routes, but over particular _Domain_ functionality to which those routes lead. It might also be over functionality regarding a specific resource within the _Domain_, in which case the resource must be loaded (in part or in whole) by the _Domain_ as part of the authorization check.

Thus, it is the _Domain_ that should check if the user is allowed to perform a particular function; if so, the _Domain_ continues, but if not, the _Domain_ can report back appropriately. This keeps the _Domain_ as the proper authority over its behavior, instead of a user interface component.

For an extended conversation about this, see <https://www.reddit.com/r/PHP/comments/64910c/laravel_auth_gates_and_user_roles/>.
