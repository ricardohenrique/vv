# Reusable UI patterns

Use this reference while implementing or reviewing reusable Inertia React UI.
Adapt names and styles to the repository instead of copying examples blindly.

## Contents

- [Directory and ownership model](#directory-and-ownership-model)
- [Page-shell composition](#page-shell-composition)
- [Accessible field composition](#accessible-field-composition)
- [Inertia form selection](#inertia-form-selection)
- [Search and filter visits](#search-and-filter-visits)
- [Laravel paginator contract](#laravel-paginator-contract)
- [Semantic table composition](#semantic-table-composition)
- [Control selection](#control-selection)
- [Review checklist](#review-checklist)

## Directory and ownership model

Prefer a structure that explains ownership:

```text
resources/js/
├── components/
│   ├── ui/                  # domain-free semantic primitives
│   ├── admin/               # reusable admin compositions
│   └── fixtures/            # feature-specific UI
├── pages/                   # Inertia page orchestration
├── lib/                     # transport, mapping, and pure utilities
└── types/                   # shared payload and UI contracts
```

A component belongs in `ui` only when it can move to another feature without
renaming domain props or importing Inertia, Wayfinder, application routes, or
resource types.

Keep domain copy and rendering local:

```tsx
<Table>
    <TableCaption className="sr-only">
        Players matching the current filters
    </TableCaption>
    <TableHeader>{/* player columns */}</TableHeader>
    <TableBody>
        {players.map((player) => (
            <PlayerRow key={player.id} player={player} />
        ))}
    </TableBody>
</Table>
```

Avoid generic column descriptions until the application has several genuinely
interactive tables that share the same behavior.

## Page-shell composition

Let the application shell own repeated structure while the page supplies
feature content:

```tsx
<AdminPage
    title="Players"
    description="Manage the league roster."
    breadcrumbs={breadcrumbs}
    actions={<CreatePlayerLink />}
    meta={<PlayerFilterSummary filters={filters} />}
>
    <PlayerList players={players} />
</AdminPage>
```

Prefer a small set of stable slots such as `actions`, `meta`, and `children`.
Do not add flags such as `compact`, `withoutHeader`, `withStatus`,
`centerActions`, and `hasFilters` when slots or a focused subcomponent express
the structure more clearly.

## Accessible field composition

A field primitive should compose semantics; it should not know the form's data
shape or mutate Inertia state.

```tsx
const errorId = error ? `${id}-error` : undefined;
const descriptionId = description ? `${id}-description` : undefined;
const describedBy =
    [descriptionId, errorId].filter(Boolean).join(' ') || undefined;

<Field data-invalid={Boolean(error)}>
    <FieldLabel htmlFor={id}>{label}</FieldLabel>
    <Input
        id={id}
        name={name}
        aria-invalid={Boolean(error)}
        aria-describedby={describedBy}
        {...inputProps}
    />
    {description ? (
        <FieldDescription id={descriptionId}>{description}</FieldDescription>
    ) : null}
    {error ? <FieldError id={errorId}>{error}</FieldError> : null}
</Field>;
```

Prefer composable `Field*` parts over one highly generic component that accepts
every possible input prop, selects its own control type, and owns form state.
Composite controls still need an accessible name for each focusable element.

Use `fieldset` and `legend` for groups:

```tsx
<FieldSet>
    <FieldLegend>Notification methods</FieldLegend>
    <FieldGroup>{/* related checkboxes */}</FieldGroup>
</FieldSet>
```

## Inertia form selection

Use `<Form>` when native named controls describe the payload:

```tsx
<Form action={store()} resetOnSuccess={['name']} disableWhileProcessing>
    {({ errors, processing }) => (
        <>
            <TextField name="name" label="Name" error={errors.name} />
            <Button type="submit" disabled={processing}>
                Create
            </Button>
        </>
    )}
</Form>
```

Use `useForm` when values must be controlled or transformed as the user works:

```tsx
const form = useForm<CreateFixture>({
    home_team_id: '',
    away_team_id: '',
    scorers: [],
});

function submit(event: FormEvent) {
    event.preventDefault();
    form.submit(store());
}
```

Do not read `form.processing` while sending the request through
`router.post()` or another form instance. The displayed state and submission
must share one owner.

## Search and filter visits

For explicit-submit search, use GET and keep the filter in the URL:

```tsx
const search = useForm({
    search: filters.search ?? '',
    status: filters.status ?? '',
});

function submit(event: FormEvent) {
    event.preventDefault();
    search.get(index.url(), {
        preserveScroll: true,
        preserveState: true,
        replace: true,
        only: ['players', 'filters'],
    });
}
```

Normalize empty values before submission when the server distinguishes missing
query parameters from empty strings. If another control owns a filter, either
include that value in the same form or use one `router.get()` visit with
separately tracked pending state.

When a filter changes:

- omit empty values;
- remove the old `page` parameter;
- retain unrelated active filters;
- use `replace` for live/debounced changes so typing does not flood history;
- cancel or supersede stale live-search visits; and
- request only changed props when a same-page partial reload provides a real
  benefit.

Use `type="search"`, an associated label, and a visible or programmatic clear
action. Do not use placeholder text as the accessible name.

## Laravel paginator contract

Represent the server contract once:

```ts
export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export type LengthAwarePage<T> = {
    data: T[];
    current_page: number;
    first_page_url: string;
    from: number | null;
    last_page: number;
    last_page_url: string;
    links: PaginationLink[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number | null;
    total: number;
};
```

Prefer paginator-provided URLs after the server applies `withQueryString()`.
This keeps filter preservation in one place. A shared pagination component may
render Inertia `Link` elements, but page-specific result copy belongs to the
application or feature layer.

Minimum semantics:

```tsx
<nav aria-label="Player result pages">
    {/* previous, numbered links with aria-current="page", next */}
</nav>
```

Do not render disabled navigation as a clickable link. Decide whether a page
change preserves scroll, resets to the page heading, or focuses the result
summary; do not inherit that behavior accidentally.

## Semantic table composition

Keep table primitives intentionally thin:

```tsx
<Table>
    <TableCaption className="sr-only">Current league standings</TableCaption>
    <TableHeader>
        <TableRow>
            <TableHead scope="col">Position</TableHead>
            <TableHead scope="col">Team</TableHead>
            <TableHead scope="col" className="text-right">
                Points
            </TableHead>
        </TableRow>
    </TableHeader>
    <TableBody>
        {rows.map((row) => (
            <TableRow key={row.team_id}>
                <TableCell>{row.position}</TableCell>
                <TableCell>{row.team_name}</TableCell>
                <TableCell className="text-right">{row.points}</TableCell>
            </TableRow>
        ))}
    </TableBody>
</Table>
```

For narrow screens, choose among:

1. keep the semantic table and allow horizontal scrolling for compact numeric
   data;
2. hide genuinely secondary columns while preserving critical meaning; or
3. render a dedicated card/list presentation for action-heavy or relational
   rows.

Do not make every cell interactive or add ARIA grid behavior unless the table
implements a complete keyboard-navigable data grid.

## Control selection

| Need                            | Preferred control                           |
| ------------------------------- | ------------------------------------------- |
| Free text, number, date, file   | Native input wrapped by a UI primitive      |
| Multi-line text                 | Native textarea wrapped by a UI primitive   |
| Small single-choice list        | Native select, especially in WebViews       |
| Styled select-only popup        | Existing accessible Select primitive        |
| Searchable suggestions          | Existing accessible Combobox primitive      |
| Arbitrary text plus suggestions | Combobox with explicit custom-value support |

Do not replace native controls merely for visual consistency. Do not create a
custom combobox from an input and positioned buttons unless it implements the
complete naming, expanded state, popup roles, active option, focus management,
Escape, Enter, and arrow-key contract.

## Review checklist

- Is the component in the narrowest correct ownership layer?
- Does it reuse an installed primitive before introducing another abstraction?
- Are routes generated by Wayfinder and internal visits handled by Inertia?
- Does displayed pending/error/progress state belong to the request being sent?
- Are labels, descriptions, errors, landmarks, tables, and composite widgets
  accessible to keyboard and assistive technology users?
- Are empty, loading, error, disabled, stale, and success states explicit where
  relevant?
- Does the design work at narrow widths, in dark mode, and in the NativePHP
  WebView when applicable?
- Has feature meaning remained outside domain-free UI primitives?
- Is a new dependency or universal abstraction actually justified?
- Do lint, format, type, build, and focused behavior checks pass?
