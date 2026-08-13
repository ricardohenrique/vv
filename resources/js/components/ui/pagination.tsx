import { Link } from '@inertiajs/react';
import type { Paginated } from '@/types';

export default function Pagination<T>({ page }: { page: Paginated<T> }) {
    if (page.last_page <= 1) return null;

    return (
        <nav aria-label="Article result pages" className="mt-10 flex items-center justify-between gap-4 border-t border-stone-200 pt-6 dark:border-stone-800">
            {page.prev_page_url ? (
                <Link href={page.prev_page_url} preserveScroll className="rounded-full border border-stone-300 px-4 py-2 text-sm font-bold hover:bg-stone-900 hover:text-white dark:border-stone-700 dark:hover:bg-white dark:hover:text-stone-950">Previous</Link>
            ) : <span />}
            <span className="text-sm text-stone-500 dark:text-stone-400">Page {page.current_page} of {page.last_page}</span>
            {page.next_page_url ? (
                <Link href={page.next_page_url} preserveScroll className="rounded-full border border-stone-300 px-4 py-2 text-sm font-bold hover:bg-stone-900 hover:text-white dark:border-stone-700 dark:hover:bg-white dark:hover:text-stone-950">Next</Link>
            ) : <span />}
        </nav>
    );
}
