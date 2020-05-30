# _Model View Controller_ and "Model 2"

The dominant pattern name used to describe server-side web application interactions is _Model View Controller_ (MVC). However, what server-side developers have come to think of as "MVC" is not MVC at all.

## Model View Controller

First, it is important to remember MVC is not an application architecture in itself; it is a *user interface* pattern. That is, MVC is a pattern *used* in application architectures, but it is not *itself* an architecture. (Note that Martin Fowler's "Patterns of Enterprise Application Architecture" [categorizes MVC as a “Web Presentation Pattern”](http://martinfowler.com/eaaCatalog/) and not as an application architecture.)

As a user interface pattern, MVC was originally proposed in 1979 for client-side, in-memory, graphical user interfaces. This is obvious from even a cursory review of MVC literature and papers:

- [MVC: Xerox PARC 1978-79](https://heim.ifi.uio.no/~trygver/themes/mvc/mvc-index.html)
- [Applications Programming in Smalltalk-80: How to use Model-View-Controller (MVC)](https://web.archive.org/web/20150518095937/http://st-www.cs.illinois.edu/users/smarch/st-docs/mvc.html)
- [A Description of the Model-View-Controller User Interface Paradigm in the Smalltalk-80 System](http://www.create.ucsb.edu/~stp/PostScript/mvc.pdf)

Here is how "Pattern Oriented Software Architecture, Volume 1" (p 130) describes the operation of _Model View Controller_:

> - The controller accepts user input in its event-handling procedure, interprets the event, and activates a service procedure of the model.
>
> - The model performs the requested service. This results in a change to its internal data.

So far, so good, even if a server-side developer has to squint a little at the _Controller_. But the authors continue with this:

> - The model notifies all views and controllers registered with the change-propagation mechanism of the change by calling their update procedures.
>
> - Each view requests the changed data from the model and re-displays itself on the screen.
>
> - Each registered controller retrieves data from the model to enable or disable certain user functions. For example, enabling the menu entry for saving data can be a consequence of modifications to the data of the model.
>
> - The original controller regains control and returns from its event-handling procedure.

Here is a corroborating description, by [Krasner and Pope](http://www.create.ucsb.edu/~stp/PostScript/mvc.pdf), of how MVC is supposed to work:

- "Models are those components of the system application that actually do the work ... the domain-specific software simulation or implementation of the application's central structure." *(This sounds similar to what a server-side developer might expect.)*

- "[V]iews deal with everything graphical; they request data from their model, and display the data." *(Do server-side views ask for data from the model; e.g., do they ask it to pull data from the database? Typically the answer is "no"; indeed, a view interacting with the database indicates that concerns have been poorly separated.)*

- "Controllers contain the interface between their associated models and views and the input devices (keyboard, pointing device, time).  Controllers also deal with scheduling interactions with other view-controller pairs: they track mouse movement between application views, and implement messages for mouse button activity and input from the input sensor." *(This sounds unlike any _Controller_ a server-side developer might put together.)*

Indeed, it turns out there is not just one MVC triad for the application as a whole. Instead, each element on the screen is backed by its own MVC triad: one MVC triad for each text field, each button, each popup menu, and so on. Martin Fowler in his [GUI Architectures](http://martinfowler.com/eaaDev/uiArchs.html) essay confirms that "there's not just one view and controller, you have a view-controller pair for each element of the screen, each of the controls, and the screen as a whole." So there are lots of little models, views, and controllers interacting as user interfaces within the application as a whole.

Further, there is a subject/observer messaging system conncting all these separate MVC triads together, so that they can continuously notify each other of changes. Back to Krasner and Pope:

> Views and controllers of a model are registered in a list as dependents of the model, to be informed whenever some aspect of the model is changed. When a model has changed, a message is broadcast to notify all of its dependents about the change.

Finally, the interaction cycle is dramatically different from what a server-side developer is used to. Krasner and Pope again:

> [T]he user takes some input action and the active controller notifies the model to change itself accordingly. The model carries out the prescribed operations, possibly changing its state, and broadcasts to its dependents (views and controllers) that it has changed, possibly telling them the nature of the change. Views can then inquire of the model about its new state, and update their display if necessary. Controllers may change their method of interaction depending on the new state of the model.

That is not at all how a server-side over-the-network HTTP-oriented user interface operates. Server-side applications do not present a graphical user interface of many interconnected screen elements all continuously communicating with each other back and forth in memory, responding to mouse movements, button clicks, and key presses. Instead, a server-side application is centered around *exactly one* user interface interaction: it receives an entire HTTP Request, and delivers an entire HTTP Response in return.

All of this is to say MVC was never intended for server-side, over-the-network, HTTP request/response user interfaces.

## "Model 2"

Any reference to MVC on the server side exists only because Sun Microsystems appropriated the components of _Model View Controller_ for their own web applications, then subverted the purpose of and collaborations between those components. (For a well-written history of this, see [Interactive Application Architecture Patterns](https://lostechies.com/derekgreer/2007/08/25/interactive-application-architecture/), esp. "The Model-View-Controller Pattern for Web Applications.")

This appropriation and subversion was codified by Sun under the name "Model 2" and later solidified by Struts. Server-side developers now equate "Model 2" with "MVC" on the server side as a result, even though any connection between the two is misleading at best and entirely unfounded at worst. Readers will find [the 1999 article introducing Model 2](https://www.javaworld.com/article/2076557/java-web-development/understanding-javaserver-pages-model-2-architecture.html) concludes:

> Properly applied, the Model 2 architecture should result in the concentration of all of the processing logic in the hands of the controller servlet, with the JSP pages responsible only for the view or presentation.

In other words, "Model 2" prescribes business logic should reside in the _Controller_---a user interface component! While it may be true this is *done* by a wide range of server-side developers, it is obviously a poor separation of concerns. The prescription is certainly not supported by the original MVC pattern description.

## Moving Forward

Since MVC (in its original GUI incarnation) is not suited to server-side applications, and "Model 2" misappropriates the term MVC in addition to prescribing an improper separation of concerns, what pattern (if any) can be applied to a server-side, over-the-network, request/response user interface?

The answer is [_Action Domain Responder_](./ADR.md).
