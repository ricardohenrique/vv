import { Form, Head, Link, usePage } from '@inertiajs/react';
import { home, logout } from '@/routes';
import { index as adminArticles } from '@/routes/admin/articles';
import type { Auth } from '@/types';

type SharedProps = {
    auth: Auth;
};

export default function Welcome() {
    const { auth } = usePage<SharedProps>().props;

    return (
        <main className="relative flex min-h-screen items-center justify-center overflow-hidden bg-canvas px-6 py-12 text-ink">
            <Head title="Welcome" />
            <div
                aria-hidden="true"
                className="absolute -top-48 -right-48 h-[36rem] w-[36rem] rounded-full bg-brand-100 blur-3xl"
            />
            <section className="relative w-full max-w-2xl rounded-3xl border border-line bg-white p-8 text-center shadow-[0_24px_70px_rgba(11,42,91,0.12)] sm:p-12">
                <img
                    src="/assets/viral-verdict-logo.png"
                    alt="Viral Verdict"
                    className="mx-auto h-32 w-auto sm:h-40"
                />
                <p className="mt-8 text-xs font-bold tracking-[0.2em] text-brand-700 uppercase">
                    Reviews worth your time
                </p>
                <h1 className="mt-3 text-3xl font-black tracking-tight text-navy sm:text-4xl">
                    Welcome back, {auth.user?.name}.
                </h1>
                <p className="mx-auto mt-4 max-w-lg leading-7 text-muted">
                    Read the latest independent verdicts or head to the
                    editorial workspace to manage articles.
                </p>
                <div className="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                    <Link
                        href={home()}
                        className="inline-flex min-h-11 items-center justify-center rounded-full bg-brand-600 px-6 text-sm font-bold text-white transition hover:bg-navy focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-500"
                    >
                        Browse reviews
                    </Link>
                    {auth.user?.is_admin ? (
                        <Link
                            href={adminArticles()}
                            className="inline-flex min-h-11 items-center justify-center rounded-full border border-brand-200 bg-brand-50 px-6 text-sm font-bold text-brand-800 transition hover:border-brand-600"
                        >
                            Manage articles
                        </Link>
                    ) : null}
                </div>
                <Form action={logout()} className="mt-8">
                    <button
                        type="submit"
                        className="text-sm font-semibold text-muted underline decoration-brand-200 underline-offset-4 hover:text-brand-700"
                    >
                        Log out
                    </button>
                </Form>
            </section>
        </main>
    );
}
