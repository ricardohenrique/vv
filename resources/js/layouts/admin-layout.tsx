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
        <div className="min-h-screen bg-canvas text-ink">
            <header className="border-b border-line bg-white shadow-[0_1px_12px_rgba(11,42,91,0.04)]">
                <div className="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-5 py-4 sm:px-8">
                    <Link
                        href={index()}
                        className="flex items-center gap-3 rounded-sm focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-500"
                    >
                        <img
                            src="/assets/viral-verdict-logo.png"
                            alt="Viral Verdict"
                            className="h-11 w-auto"
                        />
                        <span className="rounded-full bg-brand-100 px-2.5 py-1 text-[10px] font-black tracking-[0.15em] text-brand-800 uppercase">
                            Admin
                        </span>
                    </Link>
                    <nav
                        aria-label="Admin navigation"
                        className="flex items-center gap-4 text-sm font-semibold"
                    >
                        <Link
                            href={home()}
                            className="text-muted transition hover:text-brand-700"
                        >
                            View site
                        </Link>
                        <Form action={logout()}>
                            <button
                                type="submit"
                                className="text-muted transition hover:text-brand-700"
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
                        <p className="text-xs font-bold tracking-[0.18em] text-brand-700 uppercase">
                            Editorial
                        </p>
                        <h1 className="mt-2 text-3xl font-black tracking-tight text-navy sm:text-4xl">
                            {title}
                        </h1>
                        {description ? (
                            <p className="mt-2 text-muted">{description}</p>
                        ) : null}
                    </div>
                    {actions}
                </div>
                {flash.status ? (
                    <div
                        role="status"
                        className="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900"
                    >
                        {flash.status}
                    </div>
                ) : null}
                <div className="mt-8">{children}</div>
            </main>
        </div>
    );
}
