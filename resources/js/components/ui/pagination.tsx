import { Link } from '@inertiajs/react';
import type { Paginated } from '@/types';

export default function Pagination<T>({ page }: { page: Paginated<T> }) {
    if (page.meta.last_page <= 1) return null;

    return (
        <nav aria-label="Article result pages" className="mt-12 flex items-center justify-between gap-4 border-t border-line pt-6">
            {page.links.prev ? (
                <Link href={page.links.prev} preserveScroll className="rounded-full border border-brand-200 bg-white px-4 py-2 text-sm font-bold text-brand-800 transition hover:border-brand-600 hover:bg-brand-600 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-500">Previous</Link>
            ) : <span />}
            <span className="text-sm text-muted">Page {page.meta.current_page} of {page.meta.last_page}</span>
            {page.links.next ? (
                <Link href={page.links.next} preserveScroll className="rounded-full border border-brand-200 bg-white px-4 py-2 text-sm font-bold text-brand-800 transition hover:border-brand-600 hover:bg-brand-600 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-500">Next</Link>
            ) : <span />}
        </nav>
    );
}
