# History of ADR

## Origin

March 2014

While writing Chapter 10 of [MLAPHP](https://leanpub.com/mlaphp), on extracting presentation logic to _View_ files in an MVC structure, I realized that there was no logical place to put the refactored HTTP Response header work. HTTP headers and status are not business logic, but at the same time no template system had any support for them. This led me to realize that in a server-side application, in a request/response over-the-network environment, the presentation being delivered from a server-side application is the entire HTTP response and not just the body of the response message.

## Initial Draft

May 2014

As a result, I ended up doing the research (and asking questions of practitioners) that led to the initial draft of the ADR pattern. It was origially titled [_Action Domain Response_](./original.html) but I shortly retitled [_Action Domain Responder_](./original-renamed.md) to make clear that the component is not itself the HTTP Response, but instead something that *builds* the HTTP Response.

## Revision 1

Jan 2018

In this revision, ADR is now described as an "alternative to" and not merely a "refinement of" _Model View Controller_. This is based on the expanded presentation regarding background of _Model View Controller_ and "Model 2". There are no significant changes to the pattern itself, though the implementation advice and considerations have been updated based on four years of experience and experimentation. The offering as a whole has been broken up into several pages instead of a single page.
