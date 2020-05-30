# The History of ADR

## Origin (March 2014)

While writing Chapter 10 of [MLAPHP](https://leanpub.com/mlaphp), on extracting presentation logic to _View_ files in an MVC structure, I realized there was no logical place to put the refactored HTTP response header work. HTTP headers and status are not business logic, but at the same time, no template system had any support for them. This led me to realize in a server-side application, in a request/response over-the-network environment, the presentation delivered from a server-side application is the entire HTTP response and not just the body of the response message.

## Initial Draft (May 2014)

As a result, I researched (and asking questions of practitioners) which led to the initial draft of the ADR pattern. It was initially titled [_Action Domain Response_](./original.html), but I soon retitled it [_Action Domain Responder_](./original-renamed.md) to make it clear the component is not itself the HTTP response, but instead something which *builds* the HTTP response.

## Revision 1 (Jan 2018)

In this revision, ADR is now described as an "alternative to" and not merely a "refinement of" _Model View Controller_. This is based on the expanded presentation regarding background of _Model View Controller_ and "Model 2". There are no significant changes to the pattern itself, though the implementation advice and considerations have been updated based on four years of experience and experimentation. The offering as a whole has been broken up into several pages instead of a single page.

## Revision 2 (May 2020)

Updates to examples, and light editing.
