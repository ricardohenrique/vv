import { Head } from '@inertiajs/react';
import ArticleForm from '@/components/articles/article-form';
import AdminLayout from '@/layouts/admin-layout';
import { update } from '@/routes/admin/articles';
import type { Article, CategoryOption } from '@/types';

export default function EditArticle({
    article: resource,
    categories,
}: {
    article: { data: Article };
    categories: CategoryOption[];
}) {
    const article = resource.data;

    return (
        <AdminLayout
            title="Edit article"
            description={
                article.published_at
                    ? 'This article is live. Saved changes appear immediately.'
                    : 'This article is currently a draft.'
            }
        >
            <Head title={`Edit ${article.title}`} />
            <ArticleForm
                action={update(article)}
                article={article}
                categories={categories}
                submitLabel="Save changes"
            />
        </AdminLayout>
    );
}
