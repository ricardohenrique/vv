import { Form, Head } from '@inertiajs/react';
import { home } from '@/routes';
import { store } from '@/routes/login';

export default function Login() {
    return (
        <>
            <Head title="Log in" />

            <main className="relative flex min-h-screen items-center justify-center overflow-hidden bg-canvas px-6 py-12 text-ink">
                <div
                    aria-hidden="true"
                    className="absolute -top-40 -right-40 h-[32rem] w-[32rem] rounded-full bg-brand-100 blur-3xl"
                />
                <section className="relative w-full max-w-md rounded-3xl border border-line bg-white p-8 shadow-[0_24px_70px_rgba(11,42,91,0.12)] sm:p-10">
                    <div className="mb-8">
                        <a
                            href={home.url()}
                            className="mb-8 block w-fit rounded-sm focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-500"
                        >
                            <img
                                src="/assets/viral-verdict-logo.png"
                                alt="Viral Verdict"
                                className="h-20 w-auto"
                            />
                        </a>
                        <p className="text-xs font-bold tracking-[0.18em] text-brand-700 uppercase">
                            Editorial access
                        </p>
                        <h1 className="mt-2 text-3xl font-black tracking-tight text-navy">
                            Welcome back
                        </h1>
                        <p className="mt-2 text-sm leading-6 text-muted">
                            Sign in to manage Viral Verdict articles.
                        </p>
                    </div>

                    <Form
                        action={store()}
                        resetOnSuccess={['password']}
                        disableWhileProcessing
                    >
                        {({ errors, processing }) => (
                            <div className="space-y-5">
                                <div>
                                    <label
                                        htmlFor="email"
                                        className="mb-2 block text-sm font-medium"
                                    >
                                        Email address
                                    </label>
                                    <input
                                        id="email"
                                        name="email"
                                        type="email"
                                        autoComplete="email"
                                        autoFocus
                                        required
                                        aria-invalid={Boolean(errors.email)}
                                        aria-describedby={
                                            errors.email
                                                ? 'email-error'
                                                : undefined
                                        }
                                        className="h-11 w-full rounded-xl border border-line bg-white px-3.5 text-sm transition outline-none placeholder:text-slate-400 focus:border-brand-500 focus:ring-3 focus:ring-brand-500/15"
                                        placeholder="you@example.com"
                                    />
                                    {errors.email && (
                                        <p
                                            id="email-error"
                                            role="alert"
                                            className="mt-2 text-sm text-red-600"
                                        >
                                            {errors.email}
                                        </p>
                                    )}
                                </div>

                                <div>
                                    <label
                                        htmlFor="password"
                                        className="mb-2 block text-sm font-medium"
                                    >
                                        Password
                                    </label>
                                    <input
                                        id="password"
                                        name="password"
                                        type="password"
                                        autoComplete="current-password"
                                        required
                                        aria-invalid={Boolean(errors.password)}
                                        aria-describedby={
                                            errors.password
                                                ? 'password-error'
                                                : undefined
                                        }
                                        className="h-11 w-full rounded-xl border border-line bg-white px-3.5 text-sm transition outline-none focus:border-brand-500 focus:ring-3 focus:ring-brand-500/15"
                                    />
                                    {errors.password && (
                                        <p
                                            id="password-error"
                                            role="alert"
                                            className="mt-2 text-sm text-red-600"
                                        >
                                            {errors.password}
                                        </p>
                                    )}
                                </div>

                                <label className="flex w-fit items-center gap-2.5 text-sm text-muted">
                                    <input
                                        name="remember"
                                        type="checkbox"
                                        value="1"
                                        className="h-4 w-4 rounded border-line accent-brand-600"
                                    />
                                    Remember me
                                </label>

                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="flex h-11 w-full items-center justify-center rounded-xl bg-brand-600 px-4 text-sm font-bold text-white transition hover:bg-navy focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-500 disabled:cursor-not-allowed disabled:opacity-60"
                                >
                                    {processing ? 'Logging in…' : 'Log in'}
                                </button>
                            </div>
                        )}
                    </Form>

                    <p className="mt-6 text-center text-sm text-muted">
                        Looking for reviews?{' '}
                        <a
                            href={home.url()}
                            className="font-bold text-brand-700 underline decoration-brand-200 underline-offset-4 transition hover:decoration-brand-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-500"
                        >
                            Visit the home page
                        </a>
                    </p>
                </section>
            </main>
        </>
    );
}
