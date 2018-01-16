## Comparisons to Other Patterns

These are some of the other patterns that are generally seen as refinements of, replacements for, or complements to MVC. See also [the pattern discussion from Derek Greer at LosTechies](http://lostechies.com/derekgreer/2007/08/25/interactive-application-architecture/).

### EBI (Entity-Boundary-Interactor)

[EBI](http://www.whitewashing.de/2012/08/13/oop_business_applications_entity_boundary_interactor.html) appears to go by several synonyms: ports and adapters, hexagonal architecture, and [ECB](http://www.cs.sjsu.edu/~pearce/modules/patterns/enterprise/ecb/ecb.htm) (Entity-Control-Boundary). It is further described as part of a [Clean Architecture](http://blog.8thlight.com/uncle-bob/2012/08/13/the-clean-architecture.html) by Robert Martin.

EBI is in part an alternative to MVC where the core application elements and behaviors, represented by _Interactor_ and _Entity_ objects, are separated from the incoming and outgoing data streams by a _Boundary_. This has the effect of cleanly separating the application itself from the details of the input and output mechanisms, so that the core behaviors are never dependent on any particular element of the receiving and delivery systems. There is a great deal more to EBI architectures, such as "use cases" and "roles".

I confess to being unfamiliar with EBI, and so that description may be incorrect in whole or in part.  It occurs to me from my limited reading that EBI may better describe domain interactions rather than MVC architectural patterns. If the above description is accurate, it appears that ADR maps only roughly to EBI:

-  the ADR _Action_ and _Responder_ elements may represent a web-specific EBI _Boundary_

- the ADR _Domain_ element may represent an EBI _Interactor_ element, encapsulating or otherwise hiding the EBI _Entity_ elements from the ADR _Action_.

Alternatively, in ports-and-adapters or hexagonal architecture terms, it may be reasonable to think of the _Action_ as a "port" through which an EBI _Boundary_ is invoked as part of the ADR _Domain_. Finally, the _Responder_ could be seen as an "adapter" back through which the application data is returned.

Regardless, it does not appear that ADR is a direct replacement for EBI. It seems more likely that they are complements to each other.

### DCI (Data-Context-Interaction)

[DCI is described as a complement to MVC](https://en.wikipedia.org/wiki/Data,_Context,_and_Interaction), not a replacement for MVC. I think it is fair to call it a complement to ADR as well.

### MVP (Model-View-Presenter)

[MVP has been retired](http://www.martinfowler.com/eaaDev/ModelViewPresenter.html) in favor of [_Supervising Controller_](http://www.martinfowler.com/eaaDev/SupervisingPresenter.html) and [_Passive View_](http://www.martinfowler.com/eaaDev/PassiveScreen.html). At first this seems like a candidate match for ADR, especially in that the _Passive View_ and the _Model_ have no dependencies on each other as noted on the _Passive View_ page. From Fowler's narrative:

> Supervising Controller uses a controller both to handle input response but also to manipulate the view to handle more complex view logic ...
>
> A Passive View handles this by reducing the behavior of the UI components to the absolute minimum by using a controller that not just handles responses to user events, but also does all the updating of the view. This allows testing to be focused on the controller with little risk of problems in the view.

Let us examine a little more closely:

- _Model_ and the _Domain_ map closely, as they do in MVC.

- _Passive View_ does not map well to either _Action_ or _Responder_; it might better be regarded as the response that gets returned to the client.

- _Supervising Controller_ might map to _Responder_, in that it "manipulate[s] the view to handle more complex view logic". However, _Responder_ is not responsible for interacting with the _Domain_, and it does not receive the client input, so does not seem to be a good fit for _Supervising Controller_.

- Alternatively, _Supervising Controller_ might map to _Action_, but the _Action_ is not responsible for manipulating the view (i.e. the response).

In all, this seems a case of close-but-not-quite.

### MVVM (Model-View-ViewModel)

[MVVM](https://en.wikipedia.org/wiki/Model_View_ViewModel) seems to map only incompletely to ADR. The _Model_ in MVVM maps closely to the _Model_ in MVC and the _Domain_ in ADR. Similarly, the _View_ in MVVM maps closely to the _View_ in MVC and the _Responder_ in ADR.

However, the _ViewModel_ does not map well to a _Controller_ in MVC or an _Action_ in ADR. Because ADR is a refinement of MVC, it seems reasonable to think that comparisons between MVVM and MVC would apply equally well to ADR.

For an extended description of those differences, please see these articles from [Joel Wenzel](http://joel.inpointform.net/software-development/mvvm-vs-mvp-vs-mvc-the-differences-explained/), [Avtar Singh Sohi](http://www.codeproject.com/Articles/228214/Understanding-Basics-of-UI-Design-Pattern-MVC-MVP), [Rachel Appel](http://www.rachelappel.com/comparing-the-mvc-and-mvvm-patterns-along-with-their-respective-viewmodels), and [Niraj Bhatt](https://nirajrules.wordpress.com/2009/07/18/mvc-vs-mvp-vs-mvvm/).

(In email discussions with an interested party, I was informed that MVVM is just like MVC, but with an added _ViewModel_ to intermediate between the _View_ and _Model_. If this is true, then a _ViewModel_ is just as useful in ADR as it would be in MVC.)

### PAC (Presentation-Abstraction-Control)

[From Wikipedia](https://en.wikipedia.org/wiki/Presentation-abstraction-control):

> PAC is used as a hierarchical structure of agents, each consisting of a triad of presentation, abstraction and control parts. The agents (or triads) communicate with each other only through the control part of each triad. It also differs from MVC in that within each triad, it completely insulates the presentation (view in MVC) and the abstraction (model in MVC). This provides the option to separately multithread the model and view which can give the user experience of very short program start times, as the user interface (presentation) can be shown before the abstraction has fully initialized.

This does not seem to fit the description of ADR very well.

### RMR (Resource-Method-Representation)

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

### Models-Operations-Views-Events (MOVE)

From [the originating site](http://cirw.in/blog/time-to-move-on):

> - Models encapsulate everything that your application knows.
> - Operations encapsulate everything that your application does.
> - Views mediate between your application and the user.
> - Events are used to join all these components together safely.

This is an interesting pattern in itelf. The idea of _Models_ and _Operations_ seems to map well to Domain-Driven Design idioms.

However, I do not think MOVE is a close fit for ADR, specifically because of this paragraph:

> Listening on events is what gives MOVE (and MVC) the inversion of control that you need to allow models to update views without the models being directly aware of which views they are updating.

In ADR, the _Domain_ and the _Responder_ do not "update each other". The _Domain_ work is completed and passed to the _Responder_ for it to present to the client.

### Model-View-Adapter

Adapter aka Mediating Controller

https://web.archive.org/web/20160413130113/https://www.palantir.com/2009/04/model-view-adapter/

https://stefanoborini.gitbooks.io/modelviewcontroller/content/02_mvc_variations/variations_on_the_triad/10_model_view_adapter.html (cf the Web MVC section for a Tanks-like description of how the collborations work, though it is stil HTML centric from the original concentration on GUI screen elements.)

https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93adapter

Again, a GUI pattern, not a Request/Response pattern.

Very similar in that it disconnects the model from the view, but still works by notification events. In ADR it is more direct.

(SIDE NOTE: Noticing that the UI entry point in MVC is generally through the View: it is what receives the mouse click, etc. In ADR, the Action is the entry point.)

### Separated Presentation

There are hints of ADR, espeically the _Responder_ element, in [Separated Presentation](http://martinfowler.com/eaaDev/SeparatedPresentation.html). Although the article is well worth reading, Separated Presentation sounds more like a meta-pattern that describes the general concern of separating data from presentation, not a specific approach to doing so.
