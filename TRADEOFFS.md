# Tradeoffs

One benefit overall is ADR more closely describes the current day-to-day practice and work of web interactions than "Model 2" MVC. A request comes in and gets dispatched to an action; the action interacts with the domain and emits a response. The response work, including both headers and content, is cleanly separated from the input collection and the domain logic.

One drawback is we end up with more classes in the application. Although there are degenerate forms which still maintain separation between input collection, domain logic, and presentation, it will usually be the case that each _Action_ goes in its own class and each _Responder_ also goes in its own class. For a "Model 2" MVC class of five action methods, that may mean as many as five _Action_ classes and five _Responder_ classes.

This drawback may not be so terrible in the longer term. Individual classes may lead to cleaner or shallower inheritance hierarchies. It may also lead to better testability of the _Action_ separate from the _Responder_. These will play themselves out differently in different systems. Others have noted many classes may be more easily manageable via IDEs and editors than fewer classes but more methods since class lookups are frequently easier than method lookups.

Another benefit is that a clean separation of business logic into the _Domain_ makes it easier to test the domain logic without spinning up an entire user interface system. Likewise, a clean separation of presentation logic makes it easier to test the response-building work in isolation from the input collection and domain operations.

These separations may feel like overkill in simple cases. Reading a row from a database and emitting a JSON response should hardly require a separate input collection class and separate response-building class:

```php
class Api
{
    // ...

    public function read(int $id)
    {
        return new JsonResponse(json_encode(
            $this->db->fetchRow("SELECT * FROM foo WHERE id = :id", ['id' => $id])
        ));
    }
}
```

However, cases that start out simple may become complex later, whether gradually or suddenly. So, even simple cases might benefit from the cleaner separations of ADR as a hedge against the unknowns of the future.

Besides these technical tradeoffs, there are some [criticisms and objections to the presentation of the _Action Domain Responder_ pattern itself](./OBJECTIONS.md).
