# Action-Domain-Responder

_Action Domain Responder_ organizes a single user interface interaction between an HTTP client and a HTTP server-side application into three distinct roles.

![ADR](adr.png)

## Components

_Action_ is the logic that connects the _Domain_ and _Responder_. It collects input from the HTTP Request to interact with the _Domain_, then passes the HTTP Request and any _Domain_ output to the _Responder_.

_Domain_ is an entry point to the domain logic forming the core of the application, modifying state and persistence as needed. Think of this in terms of Service Layer, Transaction Script, Application Service, and the like.

_Responder_ is the presentation logic to build an HTTP Response from the HTTP Request and _Domain_ output. It deals with the status codes, headers and cookies, content, formatting and transformation, templates and views,and so on.

## Collaborations

1. The web handler receives an HTTP Request and dispatches it to an _Action_.

1. The _Action_ invokes the _Domain_, collecting any required inputs to the _Domain_ from the HTTP Request.

1. The _Action_ then invokes the _Responder_ with the HTTP Request and the output from the _Domain_ (if any).

1. The _Responder_ builds an HTTP Response using the data fed to it by the _Action_.

1. The _Action_ returns the HTTP Response to the web handler sends the HTTP Response.

## Further Reading

- [MVC-VS-ADR.md](./MVC-VS-ADR.md)
- [COMPARISONS.md](./COMPARISONS.md)
- [EXAMPLES.md](./EXAMPLES.md)
- [IMPLEMENTATION.md](./IMPLEMENTATION.md)
- [PREVIOUS.md](./PREVIOUS.md)
- [TRADEOFFS.md](./TRADEOFFS.md)

## Discussions/Mentions

(Not all of it positive.)

- [How should errors be handled in ADR?](https://www.reddit.com/r/PHP/comments/6yd366/how_should_errors_be_handled_in_adr_pattern/)

- [Am I understanding ADR correctly?](https://www.reddit.com/r/PHP/comments/790ejb/am_i_understanding_adr_correctly/)

- [Is it possible to short-circuit the domain and go straight from input to responder?](https://github.com/arbiterphp/Arbiter.Arbiter/issues/8)

- [Implementaing ADR in Laravel](http://martinbean.co.uk/blog/2016/10/20/implementing-adr-in-laravel/)

- [Goodbye Controllers, Hello Request Handlers](https://jenssegers.com/85/goodbye-controllers-hello-request-handlers)

- [OOP API Responders](http://ryantablada.com/post/oop-api-responders)

- [What are Repositories, Services, and Actions/Controllers?](https://softwareengineering.stackexchange.com/questions/337274/what-are-repositories-services-and-actions-controllers)

- [PHP framework for ADR (Action Domain Responder) Pattern?](https://softwarerecs.stackexchange.com/questions/19189/php-framework-for-adr-action-domain-responder-pattern)

- [Action-Domain-Response for Symfony 3 or 4?](https://github.com/symfony/symfony/issues/11594)

- https://dunglas.fr/2016/01/dunglasactionbundle-symfony-controllers-redesigned/

- [Let go of "Action Methods"](https://matthiasnoback.nl/2014/06/framework-independent-controllers-part-3/)

- [Should request and action be decoupled, so that an action can be fired off from anywhere (e.g. CLI or web)?](https://github.com/pmjones/adr/issues/50)

- <https://www.entropywins.wtf/blog/2016/11/24/implementing-the-clean-architecture/>, and <http://paul-m-jones.com/archives/6535> as a followup

- [Does the Action know about HTTP?](https://www.reddit.com/r/PHP/comments/5x6m7z/random_thoughts_on_the_state_of_php_mvc/deg1a1b/?sort=old)

- [Redditor 'deleted' argues at length against the Response as 'View'](https://www.reddit.com/r/PHP/comments/6tw6jr/the_micro_framework_as_user_interface_framework/dloslkh/)

- [THOUGHTS ON WORDPRESS AND THE MVC PATTERN](https://carlalexander.ca/thoughts-wordpress-and-mvc-pattern/) ("ADR: Rethinking MVC for the web"), cf. [Reddit](https://www.reddit.com/r/PHP/comments/36vj01/wordpress_mvc_and_actiondomainresponder/)

- [Model View Controller, can you help me grasp this concept? ](https://www.reddit.com/r/PHP/comments/2q6uki/model_view_controller_can_you_help_me_grasp_this/)

- [MVC vs. ADR and what it means to web-aware projects/languages](https://www.reddit.com/r/PHP/comments/27psgs/mvc_vs_adr_and_what_it_means_to_webaware/)

- [Action-Domain-Responder](https://www.reddit.com/r/PHP/comments/26ogvj/actiondomainresponder/)

- http://www.darrenmothersele.com/blog/2017/03/28/php-middleware/

## Sightings

[API Platform](https://api-platform.com/docs/core/operations/)

https://github.com/woohoolabs/harmony (Invokable controllers)

http://hanamirb.org/guides/1.0/actions/overview/ ("In a Hanami application, an action is an object, while a controller is a Ruby module that groups them.")

Equip (nee Spark)

Radar

Adroit

- http://spartan-php.iuliann.ro

## Supporting

- https://web.archive.org/web/20160324211929/http://aredridel.dinhe.net/2015/01/30/why-mvc-does-not-fit-the-web/

- https://web.archive.org/web/20160324213254/http://aredridel.dinhe.net/2015/10/31/why-mvc-does-not-fit-the-browser/

- https://www.reddit.com/r/webdev/comments/4d07l8/is_this_mvc/d1mq3yl/

## Commentary

The original blog post that led to this offering is at <http://paul-m-jones.com/archives/5970>.

Stephan Hochdörfer responded to that offering at <http://blog.bitexpert.de/blog/controller-classes-vs.-action-classes>; follow-up discussion appears at <http://paul-m-jones.com/archives/5987> and <http://www.reddit.com/r/PHP/comments/25y89a/stephan_hochdörfer_and_actiondomainresponder>.

Jon Leighton writes about a "Focused Controller" that maps well to the _Action_ element in ADR at <http://www.jonathanleighton.com/articles/2012/explaining-focused-controller>.

A follow-up post regarding _View_ vs _Responder_ is at <http://paul-m-jones.com/archives/5993> with Reddit commentary at <http://www.reddit.com/r/PHP/comments/26j3nf/the_template_is_not_the_view/> and <http://www.reddit.com/r/webdev/comments/26j5o9/the_template_is_not_the_view_xpost_from_rphp/>.

Akihito Koritama offers these notes: <https://koriym.github.io/blog/2014/06/08/action-domain-responder/>

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
