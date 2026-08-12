---
name: inertia-react-ui-conventions
description: Design, build, review, and refactor reusable frontend UI architecture for Laravel Inertia.js v3 applications using React, TypeScript, and Tailwind CSS 4. Use when working on page shells and layouts, shared components, tables and data lists, search or filter toolbars, Laravel-backed pagination, form fields and validation errors, selects or comboboxes, responsive or dark-mode behavior, NativePHP WebView compatibility, component extraction, or deciding whether UI belongs in a page, an application component, or components/ui. Apply alongside inertia-react-development and tailwindcss-development, and with wayfinder-development whenever frontend code calls backend routes.
---

# Inertia React UI Conventions

Build consistent interfaces by sharing semantics and behavior without erasing
feature-specific meaning. Prefer small composable primitives and application
patterns over universal configuration-driven components.

## Workflow

1. Read `development/scope.md`, `package.json`, `resources/css/app.css`, and the
   relevant sibling pages and components before choosing a pattern.
2. Inventory existing primitives, application patterns, and feature components.
   Reuse them before creating another implementation.
3. Classify each proposed component into the narrowest layer below.
4. Choose the Inertia form, visit, filter, and pagination behavior explicitly.
5. Implement accessibility, responsive behavior, dark mode, and WebView needs as
   part of the component contract.
6. Run focused checks, then the repository's frontend quality commands.

Read [references/patterns.md](references/patterns.md) when implementing or
reviewing a page shell, form field, search/filter bar, table, pagination,
select, or combobox.

## Component layers

Use these layers in order:

1. **UI primitives** under `resources/js/components/ui`: domain-free controls
   and semantic building blocks such as Button, Field, Input, NativeSelect,
   Textarea, Table, and Pagination. Keep backend routes, Inertia visits, domain
   copy, and resource types out of this layer.
2. **Application patterns** under a focused `resources/js/components` subtree:
   page frames, list toolbars, empty states, resource-form shells, and other
   compositions with application-wide behavior.
3. **Feature components** grouped by feature: domain-specific forms, row
   renderers, dialogs, summaries, and responsive presentations.
4. **Pages** under `resources/js/pages`: compose the feature, own page props,
   set metadata/layout props, and coordinate server visits. Do not make pages a
   second primitive library.

Do not share components across deliberately different visual systems merely
because their markup resembles each other. Admin UI and a branded public or
NativePHP experience may share semantics while retaining separate compositions.

## Extraction rules

- Extract a primitive when repeated semantics, accessibility, or interaction
  must stay correct even if only two sites currently need it.
- Extract an application pattern after three consistent uses, or earlier when
  a second use exposes a stable interface.
- Keep feature-specific markup local when only styling is similar or the
  behavior is still evolving.
- Prefer composition, children, and named slots over growing boolean props.
- Use narrow union variants for genuine visual or behavioral alternatives.
- Do not create a universal `DataTable`, `FormField`, or page schema that turns
  ordinary JSX into configuration. Preserve explicit feature markup.
- Do not add a component library, table engine, form library, or state manager
  without demonstrated need and explicit approval.

## Inertia boundaries

- Use Inertia `<Link>` for internal navigation and Wayfinder-generated route or
  controller functions for backend endpoints. Never hardcode application URLs.
- Use `<Form>` for simple forms whose payload follows native HTML controls.
- Use `useForm` when the UI needs controlled values, derived fields, previews,
  dynamic arrays, conditional transformations, or programmatic submission.
- Submit through the same form object whose `processing`, errors, and progress
  state the UI displays. Do not show `useForm().processing` for a separate
  `router` request.
- Keep server-backed filter state in the URL. Remove empty filters, reset the
  page when filters change, and preserve unrelated query parameters.
- Use partial reloads only for visits to the same page component and keep the
  corresponding server props lazy when that avoids real work.
- Keep transport types explicit and synchronized with Laravel Resources or
  paginator payloads.

## Form contracts

- Compose every field from a label, control, optional description, and error.
- Give every control a stable `id` and submission `name` where applicable.
- Set `aria-invalid` when invalid and connect descriptions and errors with
  `aria-describedby`.
- Give rendered validation errors stable IDs and an appropriate live or alert
  role without announcing the same error twice.
- Use `fieldset` and `legend` for related choices or grouped controls.
- Prefer native input, textarea, checkbox, and select behavior for ordinary
  controls, especially in mobile WebViews.
- Use an installed accessible headless primitive for comboboxes, listboxes,
  dialogs, and menus. Do not hand-roll incomplete ARIA or keyboard behavior.
- Keep Laravel as the validation authority. Add client validation only for
  immediate interaction constraints or when the product explicitly adopts
  Precognition.

## Tables, search, and pagination

- Build tables from semantic primitives while keeping columns and row content
  feature-specific.
- Provide a caption or equivalent accessible name, column header scope, stable
  row keys, a content empty state, and an intentional responsive strategy.
- Use a card/list presentation on narrow screens when horizontal scrolling
  makes important actions or relationships difficult to understand.
- Introduce a table engine only when several advanced requirements exist, such
  as sorting, selection, column visibility, resizing, or virtualization.
- Give search controls accessible names and use GET semantics for list filters.
  Choose explicit-submit or debounced-live behavior rather than mixing them.
- Treat Laravel paginator links and metadata as the navigation contract. Keep
  filter query parameters in those links instead of reconstructing them in
  every page.
- Render pagination inside a named navigation landmark, mark the current page,
  and make disabled previous/next states non-interactive.
- Choose scroll and state preservation deliberately. After pagination, users
  must land where the new results are understandable.

## Responsive, dark, and native behavior

- Build mobile-first and preserve the surrounding interface's dark-mode
  variants.
- Use Tailwind CSS 4 CSS-first tokens from `resources/css/app.css`; do not add a
  JavaScript Tailwind configuration for component styling.
- Preserve visible focus, keyboard navigation, reduced motion, contrast, touch
  targets, safe areas, and keyboard avoidance.
- Verify interactions in both a browser and the relevant NativePHP WebView when
  the screen can run in both contexts.
- Keep platform-independent UI state and domain behavior outside native shell
  integrations.

## Verification

Run the checks defined by the repository, normally:

```bash
npm run lint:check
npm run format:check
npm run types:check
npm run build
```

Also verify the changed interaction with keyboard-only navigation and at narrow
and wide viewport widths. Add focused tests for reusable behavior, filter URL
contracts, validation states, and complex keyboard interaction when the
installed test stack supports them.
