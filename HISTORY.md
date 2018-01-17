# History of ADR

## Origin

March 2014

While writing Chapter 10 of [MLAPHP](https://leanpub.com/mlaphp), on extracting presentation logic to _View_ files in an MVC structure, I realized that there was no logical place to put the refactored HTTP Response header work. HTTP headers and status are not business logic, but at the same time no template system had any support for them. This led me to realize that in a server-side application, in a request/response over-the-network environment, the presentation being delivered from a server-side application is the entire HTTP response and not just the body of the response message.

## Revision 0

May 2014

As a result, I ended up doing the research (and asking questions of practitioners) that led to the initial draft of the ADR pattern, then titled "Action Domain Response."  I want to be clear that although I codified it as a named pattern, it was something that already existed in various forms. The only "invention" is in the recognition and naming of the pattern.

## Revision 1

The first revision was to rename the pattern to Action Domain Responder, instead of Response, to make clear that the component is not itself the HTTP Response, but instead something that *builds* the HTTP Response.

## Revision 2

Jan 2018

The second revision incorporates roughly four years of implementation experience, and expands on the background of _Model View Controller_, but includes no other significant changes.  ADR is now described as an "alternative to" and not merely a "refinement of" Model View Controller.
