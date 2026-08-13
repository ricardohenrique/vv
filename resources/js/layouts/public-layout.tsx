import { Link, usePage } from '@inertiajs/react';
import type { PropsWithChildren } from 'react';
import { home, login } from '@/routes';
import { index as adminArticles } from '@/routes/admin/articles';
import type { Auth } from '@/types';

type SharedProps = {
    auth: Auth;
};

export default function PublicLayout({ children }: PropsWithChildren) {
    const { auth } = usePage<SharedProps>().props;

    return (
        <div className="min-h-screen bg-stone-50 text-stone-950 dark:bg-stone-950 dark:text-stone-100">
            <header className="border-b border-stone-200 bg-stone-50/95 dark:border-stone-800 dark:bg-stone-950/95">
                <div className="mx-auto flex max-w-7xl items-center justify-between gap-6 px-5 py-5 sm:px-8">
                    <Link
                        href={home()}
                        className="group flex items-center gap-3 focus-visible:rounded-sm focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-amber-500"
                    >
                        <span className="flex h-10 w-10 items-center justify-center rounded-full bg-amber-400 font-black text-stone-950 transition group-hover:rotate-6">
                            VV
                        </span>
                        <span>
                            <span className="block text-lg font-black tracking-tight">
                                Viral Verdict
                            </span>
                            <span className="block text-[10px] font-bold tracking-[0.2em] text-stone-500 uppercase dark:text-stone-400">
                                Reviews worth sharing
                            </span>
                        </span>
                    </Link>
                    {auth.user?.is_admin ? (
                        <Link
                            href={adminArticles()}
                            className="rounded-full border border-stone-300 px-4 py-2 text-sm font-bold transition hover:border-stone-950 hover:bg-stone-950 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-500 dark:border-stone-700 dark:hover:border-white dark:hover:bg-white dark:hover:text-stone-950"
                        >
                            Admin
                        </Link>
                    ) : (
                        <Link
                            href={login()}
                            className="text-sm font-semibold text-stone-600 underline decoration-stone-300 underline-offset-4 hover:text-stone-950 dark:text-stone-400 dark:hover:text-white"
                        >
                            Editor login
                        </Link>
                    )}
                </div>
            </header>
            {children}
            <footer className="border-t border-stone-200 px-5 py-10 text-center text-sm text-stone-500 dark:border-stone-800 dark:text-stone-400">
                Independent reviews. Some links may earn us a commission at no
                extra cost to you.
            </footer>
        </div>
    );
}
