import { Form, Head, Link } from '@inertiajs/react';
import Pagination from '@/components/ui/pagination';
import AdminLayout from '@/layouts/admin-layout';
import { create, edit, publish, unpublish } from '@/routes/admin/articles';
import type { Article, Paginated } from '@/types';

export default function AdminArticleIndex({
    articles,
}: {
    articles: Paginated<Article>;
}) {
    return (
        <AdminLayout
            title="Articles"
            description="Draft, publish, and update product verdicts."
            actions={
                <Link
                    href={create()}
                    className="inline-flex min-h-11 items-center justify-center rounded-full bg-amber-400 px-5 text-sm font-black text-stone-950 hover:bg-amber-300"
                >
                    New article
                </Link>
            }
        >
            <Head title="Manage articles" />
            {articles.data.length === 0 ? (
                <div className="rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-16 text-center dark:border-stone-700 dark:bg-stone-900">
                    <h2 className="text-xl font-bold">No articles yet</h2>
                    <p className="mt-2 text-stone-500 dark:text-stone-400">
                        Create your first product review to get started.
                    </p>
                </div>
            ) : (
                <div className="overflow-hidden rounded-2xl border border-stone-200 bg-white dark:border-stone-800 dark:bg-stone-900">
                    <div className="overflow-x-auto">
                        <table className="w-full min-w-3xl text-left text-sm">
                            <caption className="sr-only">
                                Current review articles
                            </caption>
                            <thead className="border-b border-stone-200 bg-stone-50 text-xs tracking-wide text-stone-500 uppercase dark:border-stone-800 dark:bg-stone-950 dark:text-stone-400">
                                <tr>
                                    <th scope="col" className="px-5 py-4">
                                        Article
                                    </th>
                                    <th scope="col" className="px-5 py-4">
                                        Rating
                                    </th>
                                    <th scope="col" className="px-5 py-4">
                                        Status
                                    </th>
                                    <th
                                        scope="col"
                                        className="px-5 py-4 text-right"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-stone-200 dark:divide-stone-800">
                                {articles.data.map((article) => (
                                    <tr key={article.id}>
                                        <td className="px-5 py-4">
                                            <p className="font-bold">
                                                {article.title}
                                            </p>
                                            <p className="mt-1 text-xs text-stone-500 dark:text-stone-400">
                                                {article.category.name} · by{' '}
                                                {article.author.name}
                                            </p>
                                        </td>
                                        <td className="px-5 py-4 font-bold">
                                            {article.rating.toFixed(1)}/10
                                        </td>
                                        <td className="px-5 py-4">
                                            <span
                                                className={`rounded-full px-2.5 py-1 text-xs font-bold ${article.published_at ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-stone-200 text-stone-700 dark:bg-stone-800 dark:text-stone-300'}`}
                                            >
                                                {article.published_at
                                                    ? 'Published'
                                                    : 'Draft'}
                                            </span>
                                        </td>
                                        <td className="px-5 py-4">
                                            <div className="flex justify-end gap-3">
                                                <Link
                                                    href={edit(article)}
                                                    className="font-bold underline underline-offset-4 hover:text-amber-600"
                                                >
                                                    Edit
                                                </Link>
                                                <Form
                                                    action={
                                                        article.published_at
                                                            ? unpublish(article)
                                                            : publish(article)
                                                    }
                                                >
                                                    <button
                                                        type="submit"
                                                        className="font-bold underline underline-offset-4 hover:text-amber-600"
                                                    >
                                                        {article.published_at
                                                            ? 'Unpublish'
                                                            : 'Publish'}
                                                    </button>
                                                </Form>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            )}
            <Pagination page={articles} />
        </AdminLayout>
    );
}
