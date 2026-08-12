---
name: laravel-controllers
description: Thin Laravel HTTP controllers with zero explicit database queries and zero domain logic. Use when creating, reviewing, or refactoring browser, Inertia, or API controllers and when deciding whether work belongs in a controller, Form Request, repository or query object, action, service, policy, or resource.
---

# Laravel Controllers

Controllers are HTTP adapters. They validate and authorize the request, call an application boundary, and return a response. They do not query the database or implement domain behavior.

Project `AGENTS.md` conventions override examples from the upstream skill source.

## Non-negotiable boundary

A controller may:

1. receive route-bound models and typed dependencies;
2. read already-validated request data;
3. invoke a repository, query object, action, or service;
4. authorize through policies or route middleware; and
5. return an Inertia response, API Resource, redirect, JSON response, or empty response.

A controller must not:

- call `Model::query()`, `DB`, the query builder, relationship query methods, `load()`, `refresh()`, or persistence methods;
- calculate domain outcomes or coordinate a transaction;
- transform a substantial payload inline;
- perform non-trivial validation with an inline `Request::validate()` call;
- make remote API or filesystem calls directly; or
- depend on a package that is not already installed.

Route model binding may resolve a model before the controller runs. Any additional reads, eager loading, filtering, pagination, aggregates, and response shaping belong in a repository or focused query object.

## Project naming and location

Use Laravel's conventional singular resource controller names:

```php
OrderController
OrderItemController
CancelOrderController
```

Keep controllers under the existing `app/Http/Controllers` hierarchy. Use its current `Auth`, `Api/V1`, or other bounded subdirectories rather than introducing `Http/Web/Controllers` or another parallel top-level structure.

Do not rename established controllers or routes solely to satisfy an external style guide.

## Method shape

Resource controllers use Laravel's conventional methods:

- browser/Inertia: `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`;
- API: `index`, `store`, `show`, `update`, `destroy`.

Represent a non-resource operation with a focused invokable controller when that improves the route contract:

```php
final class CancelOrderController extends Controller
{
    public function __invoke(Order $order, CancelOrder $cancel): RedirectResponse
    {
        $cancel($order);

        return to_route('orders.show', $order)->with('status', 'Order cancelled.');
    }
}
```

Do not hide a large controller behind many custom method names.

## Reads

Put all explicit reads in a repository or query object built from installed Laravel and Eloquent capabilities:

```php
final class OrderController extends Controller
{
    public function index(OrderIndexQuery $orders): Response
    {
        return Inertia::render('orders/index', [
            'orders' => $orders->paginate(),
        ]);
    }

    public function show(Order $order, OrderDetailQuery $orders): Response
    {
        return Inertia::render('orders/show', [
            'order' => $orders->find($order),
        ]);
    }
}
```

The query boundary owns eager loading, selected columns, authorization-sensitive filters, ordering, pagination, aggregates, and serialization preparation. Do not install Spatie Query Builder or another dependency unless the user explicitly approves it for a demonstrated requirement.

## Writes

Use a Form Request for non-trivial HTTP validation and a focused action or service for persistence:

```php
final class OrderController extends Controller
{
    public function store(StoreOrderRequest $request, CreateOrder $create): RedirectResponse
    {
        $order = $create($request->validated(), $request->user());

        return to_route('orders.show', $order)->with('status', 'Order created.');
    }

    public function update(
        UpdateOrderRequest $request,
        Order $order,
        UpdateOrder $update,
    ): RedirectResponse {
        $update($order, $request->validated(), $request->user());

        return to_route('orders.show', $order)->with('status', 'Order updated.');
    }
}
```

The action owns database writes, locks, transactions, integrity checks, retry-safe side effects, and the returned domain result.

## Authorization and responses

- Prefer route middleware or policies for resource authorization.
- Keep authorization on the server even when the frontend hides a control.
- Use API Resources or explicit typed serializers for stable JSON contracts.
- Keep Inertia prop names and TypeScript types aligned with the query or resource payload.
- Use named routes and Wayfinder-generated frontend helpers instead of hardcoded URLs.

## Review checklist

- No explicit database, relationship, filesystem, or remote API call appears in the controller.
- Non-trivial validation uses a Form Request.
- Authorization is explicit and tested.
- Reads are delegated to a focused repository or query object.
- Writes and multi-step workflows are delegated to a focused action or service.
- The controller uses the existing singular naming and directory conventions.
- The response contract is explicit and the frontend type is current.
- Feature tests cover success, authentication, authorization, and validation behavior.
