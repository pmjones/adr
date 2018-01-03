# Implementation Notes

This is a collection of notes and suggestions from ADR implementors.

### DOMAIN WORK

- Authentication. The presence or absence of client credentials, and their validity, may curtail the need to dispatch to an _Action_ in the first place, or to interact with the _Domain_ while in an _Action_.

- Authorization. Access-control systems may deny the client's request for the given _Action_, or cause the _Action_ to bypass interactions with _Domain_, and possibly return a response of their own.

- Content validation. If the incoming request data is malformed in some way, the _Action_ might not interact with the _Domain_ at all and move directly to interacting with a _Responder_ to send an error response.

### RESPONDER WORK

- Content negotiation. The _Front Controller_, _Action_, or other intermediary layers may negotiate the various `Accept` headers in the client request. Unsuccessful negotiation may pre-empt _Action_ or _Domain_ behaviors, and/or result in an early-exit response. (Note that a Router might inspect the incoming request and bypass an Action if a acceptable type is not available.)

### Ambiguous Domain

_Domain_ covers a lot: not just the business domain, but environment and application state as well. It might be better to call this a _Model_, but that too is somewhat ambiguous.

Additionally, it may be that the _Action_ should pass a [_Presentation Model_](http://martinfowler.com/eaaDev/PresentationModel.html) to the _Responder_ instead of _Domain_ data. But then, maybe the _Domain_ service layer used by the _Action_ returns a _Presentation Model_ that encapsulates application state.  Domiain Payload!

Regardless, recall that ADR is presented as a refinement to MVC. Thus, ADR has only as much to say about the _Domain_ as MVC has to say about the _Model_.

### Expanding Actions

One commenter noted that the _Action_ element might be interpreted to allow for different logic based on the incoming request. For example, he noted that readers might expand a single _Action_ to cover different HTTP methods, and put the logic for the different HTTP methods into the same _Action_.

While I believe the pattern implies that each _Action_ should do only one thing, that implication rising from the [_Controller_ vs _Action_](#controller-vs-action) and [RMR vs ADR](#rmr-resource-method-representation) comparisons, I will state it more explicitly here: the idea is that each _Action_ should express one, and only one, action in response to the incoming request.


### Can The _Action_ Return The _Responder_ Instead Of Invoking It?

An earlier draft of this pattern noted, "the _Action_ may return a _Responder_, which is then invoked to return a response, which is then invoked to send itself." Doing should be considered a degenerate form of ADR.


