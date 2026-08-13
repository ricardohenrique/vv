import { Form, Link, usePage } from '@inertiajs/react';
import type { PropsWithChildren, ReactNode } from 'react';
import { home, logout } from '@/routes';
import { index } from '@/routes/admin/articles';

type AdminLayoutProps = PropsWithChildren<{
    title: string;
    description?: string;
    actions?: ReactNode;
}>;

type SharedProps = {
    flash: { status: string | null };
};

export default function AdminLayout({
    title,
    description,
    actions,
    children,
}: AdminLayoutProps) {
    const { flash } = usePage<SharedProps>().props;

    return (
        <div className="min-h-screen bg-stone-100 text-stone-950 dark:bg-stone-950 dark:text-stone-100">
            <header className="border-b border-stone-200 bg-white dark:border-stone-800 dark:bg-stone-900">
                <div className="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-5 py-4 sm:px-8">
                    <Link href={index()} className="font-black tracking-tight">
                        Viral Verdict{' '}
                        <span className="text-amber-500">Admin</span>
                    </Link>
                    <nav
                        aria-label="Admin navigation"
                        className="flex items-center gap-4 text-sm font-semibold"
                    >
                        <Link href={home()} className="hover:text-amber-600">
                            View site
                        </Link>
                        <Form action={logout()}>
                            <button
                                type="submit"
                                className="hover:text-amber-600"
                            >
                                Log out
                            </button>
                        </Form>
                    </nav>
                </div>
            </header>
            <main className="mx-auto max-w-7xl px-5 py-10 sm:px-8">
                <div className="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
                    <div>
                        <p className="text-xs font-bold tracking-[0.18em] text-amber-600 uppercase dark:text-amber-400">
                            Editorial
                        </p>
                        <h1 className="mt-2 text-3xl font-black tracking-tight sm:text-4xl">
                            {title}
                        </h1>
                        {description ? (
                            <p className="mt-2 text-stone-600 dark:text-stone-400">
                                {description}
                            </p>
                        ) : null}
                    </div>
                    {actions}
                </div>
                {flash.status ? (
                    <div
                        role="status"
                        className="mt-6 rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-200"
                    >
                        {flash.status}
                    </div>
                ) : null}
                <div className="mt-8">{children}</div>
            </main>
        </div>
    );
}
