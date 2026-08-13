import { Form } from '@inertiajs/react';
import type { Article, CategoryOption } from '@/types';
import type { RouteDefinition } from '@/wayfinder';

type ArticleFormProps = {
    action: RouteDefinition<'post'> | RouteDefinition<'put'>;
    article?: Article;
    categories: CategoryOption[];
    submitLabel: string;
};

const control =
    'mt-2 min-h-11 w-full rounded-xl border border-line bg-white px-3.5 py-2.5 text-sm text-ink outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-3 focus:ring-brand-500/15';

function FieldError({ id, error }: { id: string; error?: string }) {
    return error ? (
        <p
            id={id}
            role="alert"
            className="mt-2 text-sm font-medium text-red-600"
        >
            {error}
        </p>
    ) : null;
}

export default function ArticleForm({
    action,
    article,
    categories,
    submitLabel,
}: ArticleFormProps) {
    return (
        <Form action={action} disableWhileProcessing>
            {({ errors, processing, progress }) => (
                <div className="grid gap-8 lg:grid-cols-[minmax(0,1fr)_320px]">
                    <div className="space-y-6 rounded-2xl border border-line bg-white p-5 shadow-sm sm:p-8">
                        <div>
                            <label
                                htmlFor="title"
                                className="text-sm font-bold"
                            >
                                Product title
                            </label>
                            <input
                                id="title"
                                name="title"
                                defaultValue={article?.title}
                                required
                                maxLength={255}
                                aria-invalid={Boolean(errors.title)}
                                aria-describedby={
                                    errors.title ? 'title-error' : undefined
                                }
                                className={control}
                            />
                            <FieldError id="title-error" error={errors.title} />
                        </div>
                        <div>
                            <label htmlFor="slug" className="text-sm font-bold">
                                URL slug
                            </label>
                            <input
                                id="slug"
                                name="slug"
                                defaultValue={article?.slug}
                                required
                                maxLength={255}
                                pattern="[A-Za-z0-9_-]+"
                                aria-invalid={Boolean(errors.slug)}
                                aria-describedby="slug-help slug-error"
                                className={control}
                            />
                            <p
                                id="slug-help"
                                className="mt-2 text-xs text-muted"
                            >
                                Letters, numbers, dashes, and underscores only.
                            </p>
                            <FieldError id="slug-error" error={errors.slug} />
                        </div>
                        <div>
                            <label
                                htmlFor="summary"
                                className="text-sm font-bold"
                            >
                                Short summary
                            </label>
                            <textarea
                                id="summary"
                                name="summary"
                                defaultValue={article?.summary}
                                required
                                maxLength={500}
                                rows={4}
                                aria-invalid={Boolean(errors.summary)}
                                aria-describedby={
                                    errors.summary ? 'summary-error' : undefined
                                }
                                className={control}
                            />
                            <FieldError
                                id="summary-error"
                                error={errors.summary}
                            />
                        </div>
                        <div>
                            <label htmlFor="body" className="text-sm font-bold">
                                Full review
                            </label>
                            <textarea
                                id="body"
                                name="body"
                                defaultValue={article?.body}
                                required
                                maxLength={50000}
                                rows={18}
                                aria-invalid={Boolean(errors.body)}
                                aria-describedby="body-help body-error"
                                className={control}
                            />
                            <p
                                id="body-help"
                                className="mt-2 text-xs text-muted"
                            >
                                Plain text with paragraph breaks.
                            </p>
                            <FieldError id="body-error" error={errors.body} />
                        </div>
                    </div>
                    <aside className="space-y-6">
                        <div className="space-y-5 rounded-2xl border border-line bg-white p-5 shadow-sm">
                            {article ? (
                                <img
                                    src={article.image_url}
                                    alt={article.image_alt}
                                    className="aspect-video w-full rounded-xl object-cover"
                                />
                            ) : null}
                            <div>
                                <label
                                    htmlFor="image"
                                    className="text-sm font-bold"
                                >
                                    Product image
                                </label>
                                <input
                                    id="image"
                                    name="image"
                                    type="file"
                                    accept="image/jpeg,image/png,image/webp"
                                    required={!article}
                                    aria-invalid={Boolean(errors.image)}
                                    aria-describedby="image-help image-error"
                                    className="mt-2 block w-full text-sm text-muted file:mr-3 file:rounded-full file:border-0 file:bg-brand-100 file:px-4 file:py-2 file:font-bold file:text-brand-800 hover:file:bg-brand-200"
                                />
                                <p
                                    id="image-help"
                                    className="mt-2 text-xs text-muted"
                                >
                                    JPG, PNG, or WebP. Maximum 5 MB.
                                </p>
                                <FieldError
                                    id="image-error"
                                    error={errors.image}
                                />
                            </div>
                            <div>
                                <label
                                    htmlFor="image_alt"
                                    className="text-sm font-bold"
                                >
                                    Image description
                                </label>
                                <input
                                    id="image_alt"
                                    name="image_alt"
                                    defaultValue={article?.image_alt}
                                    required
                                    maxLength={255}
                                    aria-invalid={Boolean(errors.image_alt)}
                                    aria-describedby={
                                        errors.image_alt
                                            ? 'image-alt-error'
                                            : undefined
                                    }
                                    className={control}
                                />
                                <FieldError
                                    id="image-alt-error"
                                    error={errors.image_alt}
                                />
                            </div>
                        </div>
                        <div className="space-y-5 rounded-2xl border border-line bg-white p-5 shadow-sm">
                            <div>
                                <label
                                    htmlFor="rating"
                                    className="text-sm font-bold"
                                >
                                    Rating (0–10)
                                </label>
                                <input
                                    id="rating"
                                    name="rating"
                                    type="number"
                                    min="0"
                                    max="10"
                                    step="0.1"
                                    defaultValue={article?.rating}
                                    required
                                    aria-invalid={Boolean(errors.rating)}
                                    aria-describedby={
                                        errors.rating
                                            ? 'rating-error'
                                            : undefined
                                    }
                                    className={control}
                                />
                                <FieldError
                                    id="rating-error"
                                    error={errors.rating}
                                />
                            </div>
                            <div>
                                <label
                                    htmlFor="category"
                                    className="text-sm font-bold"
                                >
                                    Category
                                </label>
                                <input
                                    id="category"
                                    name="category"
                                    list="category-options"
                                    defaultValue={article?.category.name}
                                    required
                                    maxLength={100}
                                    aria-invalid={Boolean(errors.category)}
                                    aria-describedby={
                                        errors.category
                                            ? 'category-error'
                                            : undefined
                                    }
                                    className={control}
                                />
                                <datalist id="category-options">
                                    {categories.map((category) => (
                                        <option
                                            key={category.id}
                                            value={category.name}
                                        />
                                    ))}
                                </datalist>
                                <FieldError
                                    id="category-error"
                                    error={errors.category}
                                />
                            </div>
                            <div>
                                <label
                                    htmlFor="tags"
                                    className="text-sm font-bold"
                                >
                                    Tags
                                </label>
                                <input
                                    id="tags"
                                    name="tags"
                                    defaultValue={article?.tags
                                        .map((tag) => tag.name)
                                        .join(', ')}
                                    maxLength={500}
                                    aria-invalid={Boolean(errors.tags)}
                                    aria-describedby="tags-help tags-error"
                                    className={control}
                                />
                                <p
                                    id="tags-help"
                                    className="mt-2 text-xs text-muted"
                                >
                                    Separate up to 10 tags with commas.
                                </p>
                                <FieldError
                                    id="tags-error"
                                    error={errors.tags}
                                />
                            </div>
                            <div>
                                <label
                                    htmlFor="affiliate_url"
                                    className="text-sm font-bold"
                                >
                                    Affiliate link
                                </label>
                                <input
                                    id="affiliate_url"
                                    name="affiliate_url"
                                    type="url"
                                    defaultValue={article?.affiliate_url ?? ''}
                                    maxLength={2048}
                                    placeholder="https://"
                                    aria-invalid={Boolean(errors.affiliate_url)}
                                    aria-describedby={
                                        errors.affiliate_url
                                            ? 'affiliate-error'
                                            : undefined
                                    }
                                    className={control}
                                />
                                <FieldError
                                    id="affiliate-error"
                                    error={errors.affiliate_url}
                                />
                            </div>
                        </div>
                        {progress ? (
                            <progress
                                value={progress.percentage}
                                max="100"
                                className="w-full"
                            >
                                {progress.percentage}%
                            </progress>
                        ) : null}
                        <button
                            type="submit"
                            disabled={processing}
                            className="min-h-12 w-full rounded-full bg-brand-600 px-5 font-bold text-white transition hover:bg-navy focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-500 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            {processing ? 'Saving…' : submitLabel}
                        </button>
                    </aside>
                </div>
            )}
        </Form>
    );
}
