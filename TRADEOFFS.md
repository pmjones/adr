## Tradeoffs

One benefit overall is that the pattern more closely describes the day-to-day work of web interactions. A request comes in and gets dispatched to an action; the action interacts with the domain, and then builds a response. The response work, including both headers and content, is cleanly separated from the action work.

One drawback is that we end up with more classes in the application. Not only does each _Action_ go in its own class, each _Responder_ also goes in its own class.

This drawback may not be so terrible in the longer term. Individual classes may lead to cleaner or shallower inheritance hierachies. It may also lead to better testability of the _Action_ separate from the _Responder_. These will play themselves out differently in different systems.  Others have noted that "many classes" may be more easily manageable via IDEs and editors than "fewer classes but more methods" since class lookups are frequently easier than method lookups.

