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
                    <header className="mx-auto max-w-5xl px-5 pt-12 pb-8 sm:px-8 sm:pt-20">
                        <Link
                            href={home()}
                            className="text-sm font-bold text-stone-500 underline underline-offset-4 hover:text-amber-600 dark:text-stone-400"
                        >
                            ← All reviews
                        </Link>
                        <div className="mt-8 flex flex-wrap items-center gap-3">
                            <Rating value={article.rating} />
                            <span className="text-xs font-bold tracking-[0.16em] text-stone-500 uppercase dark:text-stone-400">
                                {article.category.name}
                            </span>
                        </div>
                        <h1 className="mt-6 text-4xl leading-tight font-black tracking-[-0.04em] sm:text-6xl">
                            {article.title}
                        </h1>
                        <p className="mt-6 max-w-3xl text-xl leading-8 text-stone-600 dark:text-stone-300">
                            {article.summary}
                        </p>
                        <div className="mt-8 flex flex-wrap gap-x-5 gap-y-2 text-sm text-stone-500 dark:text-stone-400">
                            <span className="font-bold text-stone-900 dark:text-stone-100">
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
                            className="aspect-[16/8] w-full rounded-3xl bg-stone-200 object-cover dark:bg-stone-800"
                        />
                    </div>
                    <div className="mx-auto grid max-w-5xl gap-10 px-5 py-12 sm:px-8 sm:py-16 lg:grid-cols-[1fr_240px]">
                        <div className="text-lg leading-8 whitespace-pre-line text-stone-700 dark:text-stone-300">
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
                                                className="rounded-full bg-stone-200 px-3 py-1 text-xs font-bold dark:bg-stone-800"
                                            >
                                                {tag.name}
                                            </span>
                                        ))}
                                    </div>
                                </div>
                            ) : null}
                            {article.affiliate_url ? (
                                <div className="rounded-2xl bg-amber-400 p-5 text-stone-950">
                                    <h2 className="font-black">Interested?</h2>
                                    <p className="mt-2 text-sm leading-6">
                                        We may earn a commission if you buy
                                        through this link, at no extra cost to
                                        you.
                                    </p>
                                    <a
                                        href={article.affiliate_url}
                                        target="_blank"
                                        rel="sponsored nofollow noopener noreferrer"
                                        className="mt-4 inline-flex min-h-11 w-full items-center justify-center rounded-full bg-stone-950 px-4 text-sm font-bold text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-stone-950"
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
