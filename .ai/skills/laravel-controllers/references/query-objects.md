# Query Objects

Query objects keep explicit reads, eager loading, ordering, filtering,
pagination, and payload aggregation out of controllers. Build them with the
installed Laravel and Eloquent APIs unless a consuming project has explicitly
approved another query package.

## When to use one

Use a focused query object when a read needs any of the following:

- eager loading or selected columns;
- authorization-sensitive scoping;
- reusable filtering, sorting, aggregates, or search;
- stable pagination behavior; or
- preparation for an API Resource or Inertia prop.

For a truly trivial route-bound model, the controller may pass the bound model
directly to a Resource. Do not create a query object solely to rename one
property.

## Structure

Keep query objects under `app/Queries` unless the repository already has a
more specific established location. Expose an outcome-oriented method rather
than extending a third-party builder.

```php
<?php

namespace App\Queries;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class OrderIndexQuery
{
    /**
     * @return LengthAwarePaginator<int, Order>
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Order::query()
            ->select(['id', 'customer_id', 'status', 'total', 'created_at'])
            ->with('customer:id,name')
            ->latest()
            ->paginate($perPage);
    }
}
```

The controller remains an HTTP adapter:

```php
public function index(OrderIndexQuery $orders): Response
{
    return Inertia::render('orders/index', [
        'orders' => OrderResource::collection($orders->paginate()),
    ]);
}
```

For an API controller, return the same resource contract directly:

```php
public function index(OrderIndexQuery $orders): AnonymousResourceCollection
{
    return OrderResource::collection($orders->paginate());
}
```

## Contract-first reuse

Browser Inertia and versioned API controllers may invoke the same query object
and serialize with the same API Resource. The browser controller does this
in-process; it must not call its own `/api` route over HTTP. A NativePHP screen
or embedded web experience running on the device is a remote client and must
use the configured HTTPS API instead of querying the device database for
server-canonical data.

## Guardrails

- Keep request parsing and HTTP responses out of the query object.
- Keep writes, transactions, locks, and side effects in actions or services.
- Make ordering explicit before pagination.
- Select needed columns, including relationship keys required by Eloquent.
- Use `whenLoaded()` in Resources for optional relationships.
- Confirm indexes and query plans for real hot paths; do not add speculative
  indexes or caching.
- Do not install Spatie Query Builder, a repository package, or another query
  abstraction without an explicit requirement and approval.
