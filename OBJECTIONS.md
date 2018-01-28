# Objections

## Applicability

Adherents to _Model View Controller_, knowing its origins as graphical user interface pattern, may rationalize its validity as a pattern on the web like so:

- The _Controller_ exists on the client browser. The browser receives button clicks, mouse movements, and key presses, just like in the GUI pattern. The client browser (as _Controller_) sends an HTTP Request "event" to the server when the user clicks a link, or a submits a form, etc.

- The _Model_ exists on the server. When the client browser (as _Controller_) sends an HTTP Request "event" to the server, the client receives back the updated _Model_ data as an HTTP Response "event". (The data may be an entire page or only a portion thereof.) This is like the GUI pattern as well, except that the client is interacting with remote, instead of local, resources encapsulating business logic.

- The _View_ exists on the client browser. The client browser (as _View_), receiving the updated _Model_ data in the form of an HTTP Response, re-renders its screen with the changes, either as an entirely new page or as changes to an existing page.

While this might be a reasonable way to describe the user interface of the **client** side of the network interaction, it says nothing at all about the user interface on the **server** side of that interaction.

_Action Domain Responder_ addresses specifically the user interface components and collaborations that exist on the **server** side.

> N.b.: One correspondent suggests there is "an interesting proof that something is up with MVC in a web context" to be found when there is no browser involved, such as using `curl` or `wget` to interact with a server-side application that emits JSON response bodies. "The HTML message body is no more a user interface than a JSON message body -- both are simply serialization formats" as far as the server is concerned.

## Naming

Adherents to "Model 2" MVC may object that _Action Domain Responder_ could be described using variations on _Controller_ and _View_, instead of as a pattern of its own. For example:

- It could be that an _Action_ is a variation similar to [_Page Controller_](https://www.martinfowler.com/eaaCatalog/pageController.html), and thus better termed _Action Controller_ , [_Focused Controller_](http://www.jonathanleighton.com/articles/2012/explaining-focused-controller/), or [_Invokable Controller_](https://github.com/woohoolabs/harmony#using-invokable-controllers). It would thereby fit the _Controller_ component of "Model 2" MVC.  (Indeed, the formal description for _Page Controller_ says that it represents a "page or action.")

- Likewise, it could be that _Responder_ is a variation similar to [_Template View_](https://www.martinfowler.com/eaaCatalog/templateView.html) or [_Transform View_](https://www.martinfowler.com/eaaCatalog/transformView.html), and thus better termed a _Response View_. It would thereby fit the _View_ portion of "Model 2" MVC.

Having said that, I believe those alternative formulations are not as good of a description of web-based interactions as ADR.

One reason has to do with the origins of MVC in graphical user interfaces, in particular the continuous interaction between many small MVC triads in memory to notify each other of updates. In ADR, no such continuous messaging occurs: the Action invokes the Domain, then it invokes the Responder, but neither the Domain nor Responder communicate any changes to each other. Also, there only one interaction, that of receiving the HTTP Request and then sending the HTTP Response in return.

Another reason is the "Model 2" exhortation to place all processing logic in the _Controller_. This strikes me as a poor separation of concerns; business logic should go in a domain layer, not in a user interface component.

In all, I believe it it better to make a clean break with the term MVC (with its graphical user interface history) and use a new term (ADR) that is specifically for server-side over-the-network request/response environments.

## Missing Components

Some critics feel that _Action Domain Responder_ is missing some elements.

### HTTP Request and Response

Because ADR is an HTTP-specific user interface pattern, the presence of HTTP Request and Response elements is presumed as a sine qua non.

### Front Controller

The ADR pattern does not describe routing, dispatching, or other web handler elements. Those elements are more properly the purview of _Front Controller_.

When it comes to ADR, a _Front Controller_ might ...

- pass control directly to a _Responder_ without passing through an _Action_, in particular when there is no _Domain_ interaction needed.

- modify the HTTP Request before dispatching it to the _Action_; likewise, it may modify the HTTP Response returned by the _Action_.

- inspect and/or modify the HTTP Request URL with a router

- dispatch the HTTP Request to an Action, and receive back the HTTP Response.

- dispatch the HTTP Request directly to a Responder, and receive back the HTTP Response.

- bypass any ADR subsystem entirely in favor of some other subsystem ...

    - when routing fails due to URL path or HTTP method mismatches.
    - when the requested content-type cannot be presented by a _Responder_.
    - when authentication credentials or session identifiers are not present.

... among other things.  This is to say that although _Action Domain Responder_ may be one part of the request/response user interface, it may not be the entirety of the user interface.

## Other Patterns

These are some of the other patterns that are generally seen as refinements of, replacements for, or complements to _Model View Controller_. See also [the pattern discussion from Derek Greer at LosTechies](http://lostechies.com/derekgreer/2007/08/25/interactive-application-architecture/). Is ADR really one of these other, pre-existing patterns, just under a different name? Here are the other patterns that have been mentioned by critics of ADR, with their similiarties and differences explained.

### Entity Boundary Interactor

[EBI](http://www.whitewashing.de/2012/08/13/oop_business_applications_entity_boundary_interactor.html) appears to go by several synonyms: ports and adapters, hexagonal architecture, and [ECB](http://www.cs.sjsu.edu/~pearce/modules/patterns/enterprise/ecb/ecb.htm) (Entity-Control-Boundary). It is further described as part of a [Clean Architecture](http://blog.8thlight.com/uncle-bob/2012/08/13/the-clean-architecture.html) by Robert Martin.

EBI is in part an alternative to MVC where the core application elements and behaviors, represented by _Interactor_ and _Entity_ objects, are separated from the incoming and outgoing data streams by a _Boundary_. This has the effect of cleanly separating the application itself from the details of the input and output mechanisms, so that the core behaviors are never dependent on any particular element of the receiving and delivery systems. There is a great deal more to EBI architectures, such as "use cases" and "roles".

I confess to being unfamiliar with EBI, and so that description may be incorrect in whole or in part.  It occurs to me from my limited reading that EBI may better describe domain interactions rather than MVC architectural patterns. If the above description is accurate, it appears that ADR maps only roughly to EBI:

-  the ADR _Action_ and _Responder_ elements may represent a web-specific EBI _Boundary_

- the ADR _Domain_ element may represent an EBI _Interactor_ element, encapsulating or otherwise hiding the EBI _Entity_ elements from the ADR _Action_.

Alternatively, in ports-and-adapters or hexagonal architecture terms, it may be reasonable to think of the _Action_ as a "port" through which an EBI _Boundary_ is invoked as part of the ADR _Domain_. Finally, the _Responder_ could be seen as an "adapter" back through which the application data is returned.

Regardless, it does not appear that ADR is a direct replacement for EBI. It seems more likely that they are complements to each other.

### Data Context Interaction

[DCI is described as a complement to MVC](https://en.wikipedia.org/wiki/Data,_Context,_and_Interaction), not a replacement for MVC. I think it is fair to call it a complement to ADR as well.

### Model View Adapter

> N.b.: "Adapter" is also called "Mediating Controller".

At first, MVA looks like it might be an exact fit for ADR. Via [Stefano Borini](https://stefanoborini.gitbooks.io/modelviewcontroller/content/02_mvc_variations/variations_on_the_triad/10_model_view_adapter.html):

> Model-View-Adapter is a variation of the Triad where all communication between Model and View must flow through a Controller, instead of interacting directly as in a Traditional MVC Triad.

This fits very well the idea of an _Action_ invoking a _Domain_ element, then passing the result to a _Responder_ for presentation.

But then we find this:

> The Controller becomes a communication hub, accepting change notifications from Model objects and UI events from the View.

It appears MVA is still very much an in-memory GUI pattern dependent on a subject/observer system. Other articles on MVA reinforce this, both one by [Palantir](https://web.archive.org/web/20160413130113/https://www.palantir.com/2009/04/model-view-adapter/) and the one at [Wikipedia](https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93adapter).

ADR, in contrast, is for request/response environments.

### Model View Presenter

> N.b.: [MVP has been retired](http://www.martinfowler.com/eaaDev/ModelViewPresenter.html) in favor of [_Supervising Controller_](http://www.martinfowler.com/eaaDev/SupervisingPresenter.html) and [_Passive View_](http://www.martinfowler.com/eaaDev/PassiveScreen.html).

At first this seems like a candidate match for ADR, especially in that the _Passive View_ and the _Model_ have no dependencies on each other as noted on the _Passive View_ page. From Fowler's narrative:

> Supervising Controller uses a controller both to handle input response but also to manipulate the view to handle more complex view logic ...
>
> A Passive View handles this by reducing the behavior of the UI components to the absolute minimum by using a controller that not just handles responses to user events, but also does all the updating of the view. This allows testing to be focused on the controller with little risk of problems in the view.

Let us examine a little more closely:

- _Model_ and the _Domain_ map closely, as they do in MVC.

- _Passive View_ does not map well to either _Action_ or _Responder_; it might better be regarded as the response that gets returned to the client.

- _Supervising Controller_ might map to _Responder_, in that it "manipulate[s] the view to handle more complex view logic". However, _Responder_ is not responsible for interacting with the _Domain_, and it does not receive the client input, so does not seem to be a good fit for _Supervising Controller_.

- Alternatively, _Supervising Controller_ might map to _Action_, but the _Action_ is not responsible for manipulating the view (i.e. the response).

In all, this seems a case of close-but-not-quite.

### Model View ViewModel

[MVVM](https://en.wikipedia.org/wiki/Model_View_ViewModel) seems to map only incompletely to ADR. The _Model_ in MVVM maps closely to the _Model_ in MVC and the _Domain_ in ADR. Similarly, the _View_ in MVVM maps closely to the _View_ in MVC and the _Responder_ in ADR.

However, the _ViewModel_ does not map well to a _Controller_ in MVC or an _Action_ in ADR. Because ADR is a refinement of MVC, it seems reasonable to think that comparisons between MVVM and MVC would apply equally well to ADR.

For an extended description of those differences, please see these articles from [Joel Wenzel](http://joel.inpointform.net/software-development/mvvm-vs-mvp-vs-mvc-the-differences-explained/), [Avtar Singh Sohi](http://www.codeproject.com/Articles/228214/Understanding-Basics-of-UI-Design-Pattern-MVC-MVP), [Rachel Appel](http://www.rachelappel.com/comparing-the-mvc-and-mvvm-patterns-along-with-their-respective-viewmodels), and [Niraj Bhatt](https://nirajrules.wordpress.com/2009/07/18/mvc-vs-mvp-vs-mvvm/).

(In email discussions with an interested party, I was informed that MVVM is just like MVC, but with an added _ViewModel_ to intermediate between the _View_ and _Model_. If this is true, then a _ViewModel_ is just as useful in ADR as it would be in MVC. Perhaps this could considered a variation on _Domain Payload_ as used in ADR.)

### Models Operations Views Events

From [the originating site](http://cirw.in/blog/time-to-move-on):

> - Models encapsulate everything that your application knows.
> - Operations encapsulate everything that your application does.
> - Views mediate between your application and the user.
> - Events are used to join all these components together safely.

This is an interesting pattern in itelf. The idea of _Models_ and _Operations_ seems to map well to Domain-Driven Design idioms.

However, I do not think MOVE is a close fit for ADR, specifically because of this paragraph:

> Listening on events is what gives MOVE (and MVC) the inversion of control that you need to allow models to update views without the models being directly aware of which views they are updating.

In ADR, the _Domain_ and the _Responder_ do not "update each other". The _Domain_ work is completed and passed to the _Responder_ for it to present to the client.

### Presentation Abstraction Control

[From Wikipedia](https://en.wikipedia.org/wiki/Presentation-abstraction-control):

> PAC is used as a hierarchical structure of agents, each consisting of a triad of presentation, abstraction and control parts. The agents (or triads) communicate with each other only through the control part of each triad. It also differs from MVC in that within each triad, it completely insulates the presentation (view in MVC) and the abstraction (model in MVC). This provides the option to separately multithread the model and view which can give the user experience of very short program start times, as the user interface (presentation) can be shown before the abstraction has fully initialized.

This does not seem to fit the description of ADR very well.

### Resource Method Representation

I had not heard of [RMR](http://www.peej.co.uk/articles/rmr-architecture.html) before it was pointed out to me by [ircmaxell on Reddit](http://www.reddit.com/r/PHP/comments/24s8yn/actiondomainresponse_a_tentative_mvc_refinement/cha8jo1).

ADR and RMR seem very similar, and seem to map well to each other:

    Resource       <--> Domain
    Method         <--> Action
    Representation <--> Responder

However, some nuances of RMR make me think they are still somewhat different from each other. For example:

> So in an OO language, a HTTP resource can be thought of as an object with private member variables and a number of public methods that correspond to the standard HTTP methods. From an MVC point of view, a resource can be thought of as a model with a bit of controller thrown in.

To me, this seems like mixing concerns just a bit too much. I'd rather see a cleaner separation of the domain model from the action being applied to the domain.

> So the representation is like a view in MVC, we give it a resource object and tell it to serialize the data into it's output format.

There seems to be no allowance for other kinds of HTTP responses, such as "Not Found".  That kind of response is clearly not a representation of the requested resource.

Having said all that, it may be that ADR could be considered an expanded or superset variation of RMR, one where a _Resource_ and an action one can perform on it are cleanly separated into a _Domain_ and an _Action_, and where the _Representation_ (i.e., the building of the response) is handled by a _Responder_.

### Separated Presentation

There are hints of ADR, espeically the _Responder_ element, in [Separated Presentation](http://martinfowler.com/eaaDev/SeparatedPresentation.html). Although the article is well worth reading, Separated Presentation sounds more like a meta-pattern that describes the general concern of separating data from presentation, not a specific approach to doing so.
