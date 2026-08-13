import { Form, Head } from '@inertiajs/react';
import { home } from '@/routes';
import { store } from '@/routes/login';

export default function Login() {
    return (
        <>
            <Head title="Log in" />

            <main className="flex min-h-screen items-center justify-center bg-[#f7f7f5] px-6 py-12 text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#ededec]">
                <section className="w-full max-w-md rounded-2xl border border-black/10 bg-white p-8 shadow-xl shadow-black/5 sm:p-10 dark:border-white/10 dark:bg-[#161615] dark:shadow-black/30">
                    <div className="mb-8">
                        <div className="mb-6 flex h-11 w-11 items-center justify-center rounded-xl bg-[#f53003] text-sm font-semibold text-white shadow-sm">
                            LN
                        </div>
                        <h1 className="text-2xl font-semibold tracking-tight">
                            Welcome back
                        </h1>
                        <p className="mt-2 text-sm leading-6 text-[#706f6c] dark:text-[#a1a09a]">
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
                                        className="h-11 w-full rounded-lg border border-black/15 bg-white px-3.5 text-sm transition outline-none placeholder:text-[#a1a09a] focus:border-[#f53003] focus:ring-3 focus:ring-[#f53003]/15 dark:border-white/15 dark:bg-[#0f0f0e] dark:focus:border-[#ff4433]"
                                        placeholder="you@example.com"
                                    />
                                    {errors.email && (
                                        <p
                                            id="email-error"
                                            role="alert"
                                            className="mt-2 text-sm text-red-600 dark:text-red-400"
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
                                        className="h-11 w-full rounded-lg border border-black/15 bg-white px-3.5 text-sm transition outline-none focus:border-[#f53003] focus:ring-3 focus:ring-[#f53003]/15 dark:border-white/15 dark:bg-[#0f0f0e] dark:focus:border-[#ff4433]"
                                    />
                                    {errors.password && (
                                        <p
                                            id="password-error"
                                            role="alert"
                                            className="mt-2 text-sm text-red-600 dark:text-red-400"
                                        >
                                            {errors.password}
                                        </p>
                                    )}
                                </div>

                                <label className="flex w-fit items-center gap-2.5 text-sm text-[#565652] dark:text-[#b5b5ae]">
                                    <input
                                        name="remember"
                                        type="checkbox"
                                        value="1"
                                        className="h-4 w-4 rounded border-black/20 accent-[#f53003] dark:border-white/20"
                                    />
                                    Remember me
                                </label>

                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="flex h-11 w-full items-center justify-center rounded-lg bg-[#1b1b18] px-4 text-sm font-medium text-white transition hover:bg-black focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#f53003] disabled:cursor-not-allowed disabled:opacity-60 dark:bg-[#ededec] dark:text-[#1b1b18] dark:hover:bg-white"
                                >
                                    {processing ? 'Logging in…' : 'Log in'}
                                </button>
                            </div>
                        )}
                    </Form>

                    <p className="mt-6 text-center text-sm text-[#706f6c] dark:text-[#a1a09a]">
                        Looking for reviews?{' '}
                        <a
                            href={home.url()}
                            className="font-medium text-[#f53003] underline decoration-[#f53003]/30 underline-offset-4 transition hover:decoration-[#f53003] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#f53003] dark:text-[#ff4433]"
                        >
                            Visit the home page
                        </a>
                    </p>
                </section>
            </main>
        </>
    );
}
