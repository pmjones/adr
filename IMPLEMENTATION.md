# Implementation Notes

_Action Domain Responder_ a user interface pattern. It is not an entire application architectures itself. With that in mind:

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

One easy heuristic to remember is this: "If it touches storage, it goes in the _Domain_." ("Storage" includes any external resource: database, cache, filesystem, network, etc.)

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

- doing sessions in an ADR system; specifically, the Domain and Responder

This is not an ADR problem per se, but a "problem" with how PHP session functions combine request-reading, storage-interaction, and response-sending.  The "problem" appears when you start using Request/Response objects.


http://paul-m-jones.com/archives/6310

http://paul-m-jones.com/archives/6585

https://www.futureproofphp.com/2017/05/02/best-way-handle-sessions-adr/

https://github.com/juliangut/sessionware/tree/2.x

### Authentication

- Authentication. The presence or absence of client credentials, and their validity, may curtail the need to dispatch to an _Action_ in the first place, or to interact with the _Domain_ while in an _Action_.

How to do authentication? (Make an allowance for it happening in presentation, given PHP sessions; alternatively, avoid PHP sessions and separate concerns more rigorously.)

Where does authentication go? (Given "action or domain?" Tobias Gies answers says "action prob. ok")

I think it may be that authentication goes in *infrastructure*, though domain activity may need to access that infrastructure. (Alternatively, it is a Shared Kernel element that works across multiple Domain elements.)

How actually to *do* authentication?

- Login: Action passes credentials, domain checks and starts a session with them, retains session ID, stops session, send session ID back to Action; Action passes Payload to Responder, Responder checks if a session ID is present, puts it into response as a cookie.

- Logout: Action calls domain, domain clears session, returns Payload; Action passes Paylod to responder, which unsets cookie in the response.

- Resume: Action passes session ID from Request to domain; domain tries to start a session with that ID (and failure means early return), does its work, and returns Payload; Responder looks to see if session ID is present *and changed from Request ID*, then sends Payload ID if changed.

### Authorization

Authorization. Access-control systems may deny the client's request for the given _Action_, or cause the _Action_ to bypass interactions with _Domain_, and possibly return a response of their own.


### ACTION WORK

q: What does the action do? E.g. what are it’s defined responsibilities in ADR?

a: Marshals input, sends input to domain, gets back domain result, passes that result to responder. Essentially logic-less.

q: So basically handles the incoming request, and any domain exceptions to produce a reasonable response.

a: no, the action does not handle exception. domain exceptions should be caught by the domain.

q: So, something I’ve been thinking about is the need for generic request object that doesn’t follow HTTP. To make actions useful for CLI implementations as well as web.

a: i think that's a mis-direction -- MVC and ADR are *user interface* patterns. by definition, you'll need a different "thing" for a different user interface. what you want is a *domain* that works will in multiple user interfaces

q: Whereas for the CLI you would simply use the domain directly.

a: no, in CLI your command would read the CLI input, then pass that input to the domain; then get back the output from the domain and present it ont he CLI

q: Right, makes sense. What about JSON/XML APIS? can you use ADR for that?

a: sure, the Responder translates the Domain payload to JSON or whatever

q: Ok, so basically the user interface is “the web” Versus “the CLI”

a: HTTP, yeah
