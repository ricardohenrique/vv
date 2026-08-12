# Viral Verdict product scope

## Product goal

Viral Verdict is a public review website that helps readers discover products,
read complete editorial reviews, and optionally follow disclosed affiliate
links. The MVP prioritizes publishing and reading articles over community,
commerce, or mobile features.

## Users and access

- **Reader:** can browse the home page and read published articles without an
  account.
- **Administrator:** signs in through the browser and can create, edit,
  publish, and unpublish articles.
- Public registration is not required for the MVP. Existing boilerplate
  registration must be removed or restricted before launch rather than being
  treated as an administrator-provisioning mechanism.
- Authorization must be enforced on the server. Authentication alone does not
  grant editorial access.

## MVP pages

### Home

The home page displays all and only published articles, ordered by publication
date with the newest first. Each list item presents enough information to
identify and open the review:

- product title;
- product image;
- short summary;
- author;
- publication date;
- category;
- tags, when present; and
- rating or score.

Selecting an item opens its dedicated article page. The empty state explains
that no reviews have been published yet. Pagination is required once the list
can no longer be reasonably returned as a single page.

### Article page

The article page displays the complete published article:

- product title and image;
- author;
- short summary;
- full description or review;
- rating or score;
- category and tags;
- publication and last-updated dates; and
- affiliate link, when present.

The affiliate action must be clearly labelled, open only a validated HTTP or
HTTPS destination, and be accompanied by a visible affiliate disclosure.
Unpublished articles return a not-found response to public readers so their
existence and contents are not disclosed.

### Administration

The protected administration area includes:

- administrator login;
- an article list showing draft and published status;
- article creation and editing; and
- explicit publish and unpublish actions.

Publishing requires all public-facing required fields and records the initial
publication date. Unpublishing removes the article from public pages without
deleting its content. Republishing retains the original publication date unless
a later product decision introduces publication scheduling or publication
history.

Deletion, media-library management, revision history, and multi-step editorial
approval are deferred.

## Article data model

An article represents one product review.

| Field | Requirement |
| --- | --- |
| Product title | Required text; used as the article title. |
| Slug | Required, unique, URL-safe public identifier derived from the title and editable to resolve collisions. |
| Product image | Required for publication; stores a managed image path and descriptive alternative text, not an arbitrary embedded image URL. |
| Author | Required relationship to the administrator who authors the article. |
| Short summary | Required concise plain-text introduction for article listings and metadata. |
| Full description/review | Required long-form article body. The supported formatting and sanitization contract must be chosen during implementation. |
| Rating or score | Required for publication. The MVP uses a numeric score from 0 to 10 with one decimal place. |
| Category | Required single category. |
| Tags | Optional, zero or more reusable tags. |
| Affiliate link | Optional validated HTTP or HTTPS URL. |
| Created at | System-managed timestamp. |
| Published at | Nullable system-managed timestamp; null means the article is unpublished. |
| Updated at | System-managed timestamp changed when the article is edited. |

Category and tags are normalized records rather than free-form comma-separated
values. Category and tag administration beyond what is needed in the article
editor is deferred.

## Publishing rules

- An article is public only when `published_at` is not null and is not in the
  future.
- Drafts may be incomplete. Publishing must validate every field required for
  public display.
- Publish and unpublish operations must require explicit administrator intent
  and be protected by authorization and CSRF controls.
- Article slugs remain stable after publication unless an administrator
  deliberately edits one. Redirects for changed slugs are deferred.
- Public queries must enforce publication status at the query boundary rather
  than filtering articles only in the interface.

## Architecture and trust boundaries

- Laravel and its database are canonical for articles, taxonomy, users, and
  publication state.
- Public and admin browser pages use Inertia with Laravel's `web` session guard
  and CSRF protection.
- Keep controllers HTTP-only. Queries, article payload composition, and
  publishing workflows belong in focused query objects and actions, with
  policies enforcing administrative access.
- Use explicit resources or typed serializers for stable article payloads and
  keep TypeScript types synchronized.
- Product images are validated by actual file type and size and stored through
  Laravel's configured filesystem. Production storage and image size limits
  must be selected before launch.
- Affiliate URLs and article body content are untrusted input and must be
  validated or sanitized before rendering.
- NativePHP and the versioned Sanctum API remain foundations for a future
  client, not part of this MVP. If added, the remote server remains canonical
  and native clients use per-device bearer tokens over HTTPS.

## MVP acceptance criteria

- A guest can open the home page and see only published articles in newest-first
  publication order.
- A guest can open a published article by its slug and read every configured
  public field.
- A guest receives a not-found response for a draft or unpublished article.
- An authorized administrator can sign in, create or edit a draft, publish it,
  and unpublish it.
- An unauthorized authenticated user cannot access or mutate administrative
  article data.
- An article missing required publication data cannot be published and returns
  clear validation errors.
- Publishing records a publication timestamp; unpublishing preserves the
  article; republishing follows the timestamp rule above.
- Images have useful alternative text, controls are keyboard accessible, and
  layouts work on small and large screens with matching dark-mode behaviour
  where dark mode is present.
- Affiliate links are optional, visibly disclosed, safely rendered, and never
  required to read a review.

## Deferred from the MVP

- Reader accounts, comments, reactions, favourites, and user-submitted reviews.
- Search, advanced filtering, and category or tag landing pages.
- Scheduled publishing, editorial approvals, revisions, previews, and slug
  redirect history.
- Article deletion and recovery workflows.
- Affiliate click analytics, advertising integrations, and revenue reporting.
- SEO enhancements beyond sensible titles and metadata, including structured
  review data, feeds, and sitemaps.
- Native iOS and Android article experiences, offline access, and push
  notifications.

## Decisions still required before implementation reaches them

- Choose the article body's authoring format (for example, sanitized rich text
  or Markdown) and its sanitization rules.
- Set product-image dimensions, maximum upload size, accepted formats,
  transformations, and production storage disk.
- Confirm whether the 0–10 rating scale and one-decimal precision match the
  desired editorial style.
- Define how administrator accounts are provisioned in production and whether
  more than one administrator role is needed.
- Supply the final affiliate disclosure copy and any jurisdiction-specific
  privacy, cookie, and commercial-disclosure requirements before launch.

## Current implementation status

The repository currently provides the Laravel, Inertia, React, authentication,
Sanctum API, and NativePHP boilerplate foundations. The article domain, public
catalogue, and administrative editorial workflow described above have not yet
been implemented.
