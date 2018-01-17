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

- [MVC-VS-ADR.md](./MVC-VS-ADR.md)
- [COMPARISONS.md](./COMPARISONS.md)
- [REFACTORING.md](./REFACTORING.md)
- [IMPLEMENTATION.md](./IMPLEMENTATION.md)
- [PREVIOUS.md](./PREVIOUS.md)
- [TRADEOFFS.md](./TRADEOFFS.md)
- [HISTORY.md](./HISTORY.md)
- [MENTIONS.md](./MENTIONS.md)

## Discussions/Mentions

(Not all of it positive.)

## Sightings

Equip (nee Spark)

Radar

Adroit

- http://spartan-php.iuliann.ro

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
