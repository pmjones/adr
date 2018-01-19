## Tradeoffs

One benefit overall is that ADR more closely describes the current day-to-day practice and work of web interactions than "Model 2" MVC. A request comes in and gets dispatched to an action; the action interacts with the domain, and emits a response. The response work, including both headers and content, is cleanly separated from the input collection and the domain logic.

One drawback is that we end up with more classes in the application. Although there are degenrate forms that maintain separation between input collection, domain logic, and presentation, it will usually be the case that each _Action_ go in its own class, and each _Responder_ also goes in its own class. For a "Model 2" MVC class of 5 action methods, that may mean as many as 5 _Action_ classes and 5 _Responder_ classes.

This drawback may not be so terrible in the longer term. Individual classes may lead to cleaner or shallower inheritance hierachies. It may also lead to better testability of the _Action_ separate from the _Responder_. These will play themselves out differently in different systems.  Others have noted that "many classes" may be more easily manageable via IDEs and editors than "fewer classes but more methods" since class lookups are frequently easier than method lookups.

Another benefit is that a clean separation of business logic into the _Domain_ makes it easier to test the domain logic without spinning up an entire user interface system. Likewise, a clean separation of presentation logic makes it easier to test the response-building work in isolation from the domain.

These separations may feel like overkill in simple cases. Reading a row from a database and emitting a JSON response should hardly require a separate input collection class and separate response-building class:

    public function read($id)
    {
        return new JsonResponse(json_encode(
            $this->db->fetchRow("SELECT * FROM foo WHERE id = :id", ['id' => $id])
        ));
    }

However, cases that start out simple may become complex later; sometimes gradually, sometime quickly and unexpectedly. So, even simple cases might benefit from the cleaner separations of ADR as a hedge against future unknowns.
