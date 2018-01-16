# Implementation Notes

The ADR pattern is a user interface alternative to MVC. Neither ADR nor MVC are entire application architectures themselves. This section describes other components, collaborations, and patterns that might be used in conjunction with ADR within an application architecture.

This is a collection of notes and suggestions from ADR implementors.

## Front Controller

The ADR pattern does not describe a routing, dispatching, or middleware element. Those elements are more properly the purview of _Front Controller_.  When it comes to ADR, a_Front Controller_ may, among other things:

- The web handler may pass control directly to a _Responder_ without passing through an _Action_, in particular when there is no _Domain_ interaction needed.

- The web handler may modify the HTTP Request before dispatching it to the _Action_; likewise, it may modify the HTTP Response returned by the _Action_.

- Inspect and/or modify the HTTP Request URL with a router

- Dispatch the HTTP Request to an Action, and receive back the HTTP Response.

- Dispatch the HTTP Request directly to a Responder, and receive back the HTTP Response.

- Bypass any ADR subsystem entirely in favor of some other subsystem; for example:

    - when routing fails due to URL path or HTTP method mismatches
    - when the requested content-type is not available.
    - when authentication credentials or session identifiers are not present

- Pass the HTTP Request and Response through one or more layers of middleware.

### HTTP Request and Response

Because ADR is an HTTP-specific user interface pattern, the presence of HTTP Request and Response elements is presumed as a sine qua non.

## Presentation

    Template View
    Two Step View

## Domain Logic

Domain Payload

Addendum to "touching storage" when it comes to domain:

Usually I say "if it touches storage it probably goes in the domain."

But if the storage is to retrieve only presentation values, that do not get used
in domain work at all, it might make sense to load values in the presentation
layer from the database. E.g. translations.

Or maybe even then, it should be delivered from the Domain as part of the
Payload, to populate a Translation helper?

## Authentication and Authorization

How to do authentication? (Make an allowance for it happening in presentation, given PHP sessions; alternatively, avoid PHP sessions and separate concerns more rigorously.)

    Authorization

    https://www.reddit.com/r/PHP/comments/64910c/laravel_auth_gates_and_user_roles/?sort=old

Where does authentication go? (Given "action or domain?" Tobias Gies answers says "action prob. ok")

I think it may be that authentication goes in *infrastructure*, though domain activity may need to access that infrastructure. (Alternatively, it is a
Shared Kernel element that works across multiple Domain elements.)

How actually to *do* authentication?

- Login: Action passes credentials, domain checks and starts a session with them, retains session ID, stops session, send session ID back to Action; Action passes Payload to Responder, Responder checks if a session ID is present, puts it into response as a cookie.

- Logout: Action calls domain, domain clears session, returns Payload; Action passes Paylod to responder, which unsets cookie in the response.

- Resume: Action passes session ID from Request to domain; domain tries to start a session with that ID (and failure means early return), does its work, and returns Payload; Responder looks to see if session ID is present *and changed from Request ID*, then sends Payload ID if changed.

This is not an ADR problem per se, but a "problem" with how PHP session functions combine request-reading, storage-interaction, and response-sending.  The "problem" appears when you start using Request/Response objects.

### DOMAIN WORK

Addendum to "touching storage" when it comes to domain:

Usually I say "if it touches storage it probably goes in the domain."

But if the storage is to retrieve only presentation values, that do not get used
in domain work at all, it might make sense to load values in the presentation
layer from the database. E.g. translations.

Or maybe even then, it should be delivered from the Domain as part of the Payload, to populate a Translation helper?


- Authentication. The presence or absence of client credentials, and their validity, may curtail the need to dispatch to an _Action_ in the first place, or to interact with the _Domain_ while in an _Action_.

- Authorization. Access-control systems may deny the client's request for the given _Action_, or cause the _Action_ to bypass interactions with _Domain_, and possibly return a response of their own.

- Content validation. If the incoming request data is malformed in some way, the _Action_ might not interact with the _Domain_ at all and move directly to interacting with a _Responder_ to send an error response.

Where does INPUT validation go? (Tobias Gies suggested a form object, but no: it goes in the Domain, not in the Action putting together the input for the domain. You're going to ask "what if a piece of input is simply missing?" Provide a default, pass a false payload to responder, throw an exception for the web handler to catch, or build a DTO that -- when used by the Domain -- notes that the data is missing and returns an appropriate payload. Or finally you can bust the assumption about Action and put logic in there, but I advise against it.)

### Sessions

- doing sessions in an ADR system; specifically, the Domain and Responder


http://paul-m-jones.com/archives/6310

http://paul-m-jones.com/archives/6585

https://www.futureproofphp.com/2017/05/02/best-way-handle-sessions-adr/

https://github.com/juliangut/sessionware/tree/2.x

### Command Bus

- http://paul-m-jones.com/archives/6268

### Widgets

- What to do with widgets: <http://paul-m-jones.com/archives/6760>


### RESPONDER WORK

- Content negotiation. The _Front Controller_ or other layers prior to the Action may negotiate the various `Accept` headers in the client request. Unsuccessful negotiation may pre-empt _Action_ or _Domain_ behaviors, and/or result in an early-exit response. (Note that a Router might inspect the incoming request and bypass an Action if a acceptable type is not available.) But the negotiation won't actually be *used* until the Responder is invoked, and the negotiation result will have to be stored in the Request somehow so that the Responder can use it. Alternatively, negotiate in the Responder, but at that point you have already done the domain work, so you might end up doing a lot of work before finding out negotiation fails. A split approach is to route not on negotiation per se but on on the presence of a non-zero q-value for a known acceptable type, and fail routing if none is available, then do the actual negotiation in the Responder.

### Ambiguous Domain

_Domain_ covers a lot: not just the business domain, but environment and application state as well. It might be better to call this a _Model_, but that too is somewhat ambiguous.

Additionally, it may be that the _Action_ should pass a [_Presentation Model_](http://martinfowler.com/eaaDev/PresentationModel.html) to the _Responder_ instead of _Domain_ data. But then, maybe the _Domain_ service layer used by the _Action_ returns a _Presentation Model_ that encapsulates application state.  Domiain Payload!

Regardless, recall that ADR is presented as a refinement to MVC. Thus, ADR has only as much to say about the _Domain_ as MVC has to say about the _Model_.

### Expanding Actions

One commenter noted that the _Action_ element might be interpreted to allow for different logic based on the incoming request. For example, he noted that readers might expand a single _Action_ to cover different HTTP methods, and put the logic for the different HTTP methods into the same _Action_.

While I believe the pattern implies that each _Action_ should do only one thing, that implication rising from the [_Controller_ vs _Action_](#controller-vs-action) and [RMR vs ADR](#rmr-resource-method-representation) comparisons, I will state it more explicitly here: the idea is that each _Action_ should express one, and only one, action in response to the incoming request.


### Can The _Action_ Return The _Responder_ Instead Of Invoking It?

An earlier draft of this pattern noted, "the _Action_ may return a _Responder_, which is then invoked to return a response, which is then invoked to send itself." Doing should be considered a degenerate form of ADR.

### ACTION WORK

savage: What does the action do? E.g. what are it’s defined responsibilities in ADR?

pmjones: Marshals input, sends input to domain, gets back domain result, passes that result to responder. Essentially logic-less.

savage: So basically handles the incoming request, and any domain exceptions to produce a reasonable response.

pmjones: no, the action does not handle exception. domain exceptions should be caught by the domain.

savage: So, something I’ve been thinking about is the need for generic request object that doesn’t follow HTTP. To make actions useful for CLI implementations as well as web.

pmjones: i think that's a mis-direction -- MVC and ADR are *user interface* patterns. by definition, you'll need a different "thing" for a different user interface. what you want is a *domain* that works will in multiple user interfaces

savage: Whereas for the CLI you would simply use the domain directly.

pmjones: no, in CLI your command would read the CLI input, then pass that input to the domain; then get back the output from the domain and present it ont he CLI

savage: Right, makes sense. What about JSON/XML APIS? can you use ADR for that?

pmjones: sure, the Responder translates the Domain payload to JSON or whatever

savage: Ok, so basically the user interface is “the web” Versus “the CLI”

pmjones: HTTP, yeah

savage: You need to write a book. :-P
