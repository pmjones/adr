# Action Domain Responder

_Action Domain Responder_ organizes a single user interface interaction between an HTTP client and a HTTP server-side application into three distinct roles.

![ADR](adr.png)

## Components

_Action_ is the logic to connect the _Domain_ and _Responder_. It invokes the _Domain_ with inputs collected from the HTTP Request, then invokes the _Responder_ with the data it needs to build an HTTP Response.

_Domain_ is an entry point to the domain logic forming the core of the application, modifying state and persistence as needed. This may be a Transaction Script, Service Layer, Application Service, or something similar.

_Responder_ is the presentation logic to build an HTTP Response from the data it receives from the _Action_. It deals with status codes, headers and cookies, content, formatting and transformation, templates and views,and so on.

## Collaborations

1. The web handler receives an HTTP Request and dispatches it to an _Action_.

1. The _Action_ invokes the _Domain_, collecting any required inputs to the _Domain_ from the HTTP Request.

1. The _Action_ then invokes the _Responder_ with the data it needs to build an HTTP Response (typically the HTTP Request and the _Domain_ results, if any).

1. The _Responder_ builds an HTTP Response using the data fed to it by the _Action_.

1. The _Action_ returns the HTTP Response to the web handler sends the HTTP Response.

## Further Reading

- [_Model View Controller_ and "Model 2"](./MVC-MODEL-2.md)
- [Comparing "Model 2" MVC to ADR](./ADR.md)
- [Comparing ADR to other patterns](./COMPARISONS.md)
- [Objections to ADR](./OBJECTIONS.md)
- [Refactoring from "Model 2" MVC to ADR](./REFACTORING.md)
- [Tradeoffs in ADR](./TRADEOFFS.md)
- [An example ADR codebase](https://github.com/pmjones/adr-example)
- [Implementation notes and advice](./IMPLEMENTATION.md)
- [Short history of this offering](./HISTORY.md)
- [ADR discussions, mentions, implementations, etc.](./MENTIONS.md)
- [Acknowldgements](./ACKNOWLEDGEMENTS.md)

Example code resides in the [`example`](https://github.com/pmjones/adr-example) repository.
