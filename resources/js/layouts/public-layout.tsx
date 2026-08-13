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
        <div className="min-h-screen bg-canvas text-ink">
            <header className="sticky top-0 z-40 border-b border-line bg-white/95 shadow-[0_1px_12px_rgba(11,42,91,0.04)] backdrop-blur">
                <div className="mx-auto flex max-w-7xl items-center justify-between gap-6 px-5 py-3 sm:px-8">
                    <Link
                        href={home()}
                        className="group rounded-sm focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-500"
                    >
                        <img
                            src="/assets/viral-verdict-logo.png"
                            alt="Viral Verdict"
                            className="h-14 w-auto transition duration-300 group-hover:opacity-80 sm:h-16"
                        />
                    </Link>
                    {auth.user?.is_admin ? (
                        <Link
                            href={adminArticles()}
                            className="rounded-full border border-brand-200 bg-brand-50 px-4 py-2 text-sm font-bold text-brand-800 transition hover:border-brand-600 hover:bg-brand-600 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-500"
                        >
                            Admin
                        </Link>
                    ) : (
                        <Link
                            href={login()}
                            className="text-sm font-semibold text-muted underline decoration-brand-200 underline-offset-4 transition hover:text-brand-700 hover:decoration-brand-500 focus-visible:rounded-sm focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-500"
                        >
                            Editor login
                        </Link>
                    )}
                </div>
            </header>
            {children}
            <footer className="border-t border-line bg-white px-5 py-10 text-center text-sm text-muted">
                <img
                    src="/assets/viral-verdict-logo.png"
                    alt=""
                    className="mx-auto mb-5 h-12 w-auto opacity-90"
                />
                Independent reviews. Some links may earn us a commission at no
                extra cost to you.
            </footer>
        </div>
    );
}
