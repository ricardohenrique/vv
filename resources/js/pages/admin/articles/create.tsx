import { Head } from '@inertiajs/react';
import ArticleForm from '@/components/articles/article-form';
import AdminLayout from '@/layouts/admin-layout';
import { store } from '@/routes/admin/articles';
import type { CategoryOption } from '@/types';

export default function CreateArticle({
    categories,
}: {
    categories: CategoryOption[];
}) {
    return (
        <AdminLayout
            title="New article"
            description="Create a complete review as a draft. You can publish it from the article list."
        >
            <Head title="New article" />
            <ArticleForm
                action={store()}
                categories={categories}
                submitLabel="Save draft"
            />
        </AdminLayout>
    );
}
