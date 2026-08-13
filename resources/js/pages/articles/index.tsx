import { Head, Link } from '@inertiajs/react';
import Rating from '@/components/articles/rating';
import Pagination from '@/components/ui/pagination';
import PublicLayout from '@/layouts/public-layout';
import { show } from '@/routes/articles';
import type { Article, Paginated } from '@/types';

export default function ArticleIndex({
    articles,
}: {
    articles: Paginated<Article>;
}) {
    return (
        <PublicLayout>
            <Head title="Independent product reviews" />
            <main>
                <section
                    aria-labelledby="latest-reviews"
                    className="mx-auto max-w-7xl px-5 py-12 sm:px-8 sm:py-16"
                >
                    <div className="flex items-end justify-between gap-6">
                        <div>
                            <p className="text-xs font-bold tracking-[0.18em] text-brand-700 uppercase">
                                Fresh verdicts
                            </p>
                            <h1
                                id="latest-reviews"
                                className="mt-2 text-3xl font-black tracking-tight text-navy sm:text-4xl"
                            >
                                Latest reviews
                            </h1>
                        </div>
                        <p className="text-sm text-muted">
                            {articles.meta.total} published
                        </p>
                    </div>
                    {articles.data.length === 0 ? (
                        <div className="mt-10 rounded-3xl border border-dashed border-brand-200 bg-white px-6 py-20 text-center shadow-sm">
                            <h3 className="text-xl font-bold">
                                No verdicts yet
                            </h3>
                            <p className="mt-2 text-muted">
                                The first review is being put through its paces.
                            </p>
                        </div>
                    ) : (
                        <div className="mt-10 grid gap-x-7 gap-y-12 md:grid-cols-2 lg:grid-cols-3">
                            {articles.data.map((article, index) => (
                                <article
                                    key={article.id}
                                    className={
                                        index === 0
                                            ? 'md:col-span-2 lg:col-span-2'
                                            : ''
                                    }
                                >
                                    <Link
                                        href={show(article)}
                                        prefetch
                                        className="group block rounded-2xl focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-500"
                                    >
                                        <div
                                            className={`relative overflow-hidden rounded-2xl bg-brand-100 shadow-[0_12px_32px_rgba(11,42,91,0.08)] ring-1 ring-brand-100 ${index === 0 ? 'aspect-[16/9]' : 'aspect-[4/3]'}`}
                                        >
                                            <img
                                                src={article.image_url}
                                                alt={article.image_alt}
                                                className="h-full w-full object-cover transition duration-500 motion-safe:group-hover:scale-[1.03]"
                                            />
                                            <span className="absolute top-4 right-4">
                                                <Rating
                                                    value={article.rating}
                                                />
                                            </span>
                                        </div>
                                        <div className="mt-5">
                                            <div className="flex flex-wrap items-center gap-2 text-xs font-bold tracking-wide text-muted uppercase">
                                                <span>
                                                    {article.category.name}
                                                </span>
                                                <span aria-hidden="true">
                                                    •
                                                </span>
                                                <time
                                                    dateTime={
                                                        article.published_at ??
                                                        undefined
                                                    }
                                                >
                                                    {article.published_at
                                                        ? new Date(
                                                              article.published_at,
                                                          ).toLocaleDateString()
                                                        : ''}
                                                </time>
                                            </div>
                                            <h3
                                                className={`${index === 0 ? 'text-3xl sm:text-4xl' : 'text-2xl'} mt-3 font-black tracking-tight text-navy transition group-hover:text-brand-600`}
                                            >
                                                {article.title}
                                            </h3>
                                            <p className="mt-3 line-clamp-3 leading-7 text-muted">
                                                {article.summary}
                                            </p>
                                            <p className="mt-4 text-sm font-bold text-ink">
                                                By {article.author.name}
                                            </p>
                                        </div>
                                    </Link>
                                </article>
                            ))}
                        </div>
                    )}
                    <Pagination page={articles} />
                </section>
            </main>
        </PublicLayout>
    );
}
