import { Head, Link } from '@inertiajs/react';
import Rating from '@/components/articles/rating';
import PublicLayout from '@/layouts/public-layout';
import { home } from '@/routes';
import type { Article } from '@/types';

export default function ArticleShow({
    article: resource,
}: {
    article: { data: Article };
}) {
    const article = resource.data;

    return (
        <PublicLayout>
            <Head title={article.title}>
                <meta name="description" content={article.summary} />
            </Head>
            <main>
                <article>
                    <header className="mx-auto max-w-4xl px-5 pt-12 pb-10 sm:px-8 sm:pt-20">
                        <Link
                            href={home()}
                            className="text-sm font-bold text-muted underline decoration-brand-200 underline-offset-4 transition hover:text-brand-700 hover:decoration-brand-500 focus-visible:rounded-sm focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-500"
                        >
                            ← All reviews
                        </Link>
                        <div className="mt-8 flex flex-wrap items-center gap-3">
                            <Rating value={article.rating} />
                            <span className="text-xs font-bold tracking-[0.16em] text-brand-700 uppercase">
                                {article.category.name}
                            </span>
                        </div>
                        <h1 className="mt-6 text-4xl leading-tight font-black tracking-[-0.04em] text-navy sm:text-6xl">
                            {article.title}
                        </h1>
                        <p className="mt-6 max-w-3xl text-xl leading-8 text-muted">
                            {article.summary}
                        </p>
                        <div className="mt-8 flex flex-wrap gap-x-5 gap-y-2 border-t border-line pt-6 text-sm text-muted">
                            <span className="font-bold text-ink">
                                By {article.author.name}
                            </span>
                            {article.published_at ? (
                                <time dateTime={article.published_at}>
                                    Published{' '}
                                    {new Date(
                                        article.published_at,
                                    ).toLocaleDateString()}
                                </time>
                            ) : null}
                            {article.updated_at ? (
                                <time dateTime={article.updated_at}>
                                    Updated{' '}
                                    {new Date(
                                        article.updated_at,
                                    ).toLocaleDateString()}
                                </time>
                            ) : null}
                        </div>
                    </header>
                    <div className="mx-auto max-w-7xl px-5 sm:px-8">
                        <img
                            src={article.image_url}
                            alt={article.image_alt}
                            className="aspect-[16/8] w-full rounded-3xl bg-brand-100 object-cover shadow-[0_18px_50px_rgba(11,42,91,0.10)] ring-1 ring-brand-100"
                        />
                    </div>
                    <div className="mx-auto grid max-w-5xl gap-12 px-5 py-14 sm:px-8 sm:py-20 lg:grid-cols-[minmax(0,1fr)_250px]">
                        <div className="max-w-[68ch] font-serif text-[1.125rem] leading-[1.9] whitespace-pre-line text-ink selection:bg-brand-200">
                            {article.body}
                        </div>
                        <aside className="space-y-6">
                            {article.tags.length > 0 ? (
                                <div>
                                    <h2 className="text-xs font-black tracking-[0.16em] uppercase">
                                        Topics
                                    </h2>
                                    <div className="mt-3 flex flex-wrap gap-2">
                                        {article.tags.map((tag) => (
                                            <span
                                                key={tag.id}
                                                className="rounded-full bg-brand-100 px-3 py-1 text-xs font-bold text-brand-800"
                                            >
                                                {tag.name}
                                            </span>
                                        ))}
                                    </div>
                                </div>
                            ) : null}
                            {article.affiliate_url ? (
                                <div className="rounded-2xl border border-brand-200 bg-brand-50 p-5 text-ink shadow-sm">
                                    <h2 className="font-black text-navy">
                                        Interested?
                                    </h2>
                                    <p className="mt-2 text-sm leading-6 text-muted">
                                        We may earn a commission if you buy
                                        through this link, at no extra cost to
                                        you.
                                    </p>
                                    <a
                                        href={article.affiliate_url}
                                        target="_blank"
                                        rel="sponsored nofollow noopener noreferrer"
                                        className="mt-4 inline-flex min-h-11 w-full items-center justify-center rounded-full bg-brand-600 px-4 text-sm font-bold text-white transition hover:bg-navy focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-500"
                                    >
                                        Check latest price ↗
                                    </a>
                                </div>
                            ) : null}
                        </aside>
                    </div>
                </article>
            </main>
        </PublicLayout>
    );
}
