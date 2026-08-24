<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { BellAlertIcon, ChartBarIcon, ShieldCheckIcon } from '@heroicons/vue/24/outline';

defineProps({ canLogin: Boolean, canRegister: Boolean });
const page = usePage();
</script>

<template>
    <Head :title="$t('Website uptime monitoring')" />
    <div class="min-h-screen overflow-hidden bg-canvas">
        <header class="page-shell flex h-20 items-center justify-between gap-4">
            <Link href="/" class="flex items-center gap-2 rounded-lg">
                <ApplicationLogo class="h-10 w-10" />
                <span class="text-xl font-bold tracking-tight text-ink">PingPing</span>
            </Link>
            <div class="flex items-center gap-2 sm:gap-3">
                <LanguageSwitcher />
                <Link v-if="page.props.auth.user" :href="route('dashboard')" class="inline-flex min-h-10 items-center rounded-xl bg-primary-600 px-3.5 text-sm font-semibold text-white hover:bg-primary-700">{{ $t('Dashboard') }}</Link>
                <template v-else-if="canLogin">
                    <Link dusk="landing-login" :href="route('login')" class="inline-flex min-h-10 items-center rounded-xl bg-primary-600 px-3.5 text-sm font-semibold text-white hover:bg-primary-700 sm:bg-transparent sm:text-ink sm:hover:bg-white">{{ $t('Log in') }}</Link>
                    <Link v-if="canRegister" :href="route('register')" class="hidden min-h-10 items-center rounded-xl bg-primary-600 px-3.5 text-sm font-semibold text-white hover:bg-primary-700 sm:inline-flex">{{ $t('Get started') }}</Link>
                </template>
            </div>
        </header>

        <main>
            <section class="page-shell grid items-center gap-12 pb-20 pt-12 lg:grid-cols-[1.05fr_.95fr] lg:pb-28 lg:pt-20">
                <div>
                    <p class="inline-flex rounded-full border border-primary-100 bg-primary-50 px-3 py-1.5 text-xs font-bold uppercase tracking-[0.14em] text-primary-700">{{ $t('Calm monitoring for small teams') }}</p>
                    <h1 class="mt-6 max-w-3xl text-4xl font-bold tracking-[-0.035em] text-ink sm:text-5xl lg:text-6xl">{{ $t('Know when your website needs attention.') }}</h1>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-muted">{{ $t('PingPing checks your public website on a schedule, keeps a clear 30-day history, and emails you when its status changes.') }}</p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <Link :href="page.props.auth.user ? route('dashboard') : route('register')" class="inline-flex min-h-12 items-center rounded-xl bg-primary-600 px-5 text-sm font-semibold text-white shadow-sm hover:bg-primary-700">{{ page.props.auth.user ? $t('Open dashboard') : $t('Create your first monitor') }}</Link>
                        <a href="#how-it-works" class="inline-flex min-h-12 items-center rounded-xl border border-line bg-white px-5 text-sm font-semibold text-ink shadow-sm hover:bg-gray-50">{{ $t('How it works') }}</a>
                    </div>
                    <p class="mt-4 text-sm text-muted">{{ $t('Public HTTP and HTTPS sites · Email alerts · English and Czech') }}</p>
                </div>

                <div class="relative">
                    <div class="absolute -inset-8 -z-10 rounded-full bg-primary-100/60 blur-3xl" aria-hidden="true" />
                    <div class="surface overflow-hidden">
                        <div class="flex items-center justify-between border-b border-line px-5 py-4">
                            <div>
                                <p class="font-semibold text-ink">{{ $t('Your monitors') }}</p>
                                <p class="text-xs text-muted">{{ $t('A quiet overview, updated every minute') }}</p>
                            </div>
                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">↑ {{ $t('All clear') }}</span>
                        </div>
                        <div class="grid grid-cols-3 gap-px bg-line">
                            <div class="bg-white p-4"><p class="text-xs text-muted">{{ $t('Up') }}</p><p class="metric-number mt-1 text-2xl">3</p></div>
                            <div class="bg-white p-4"><p class="text-xs text-muted">{{ $t('30-day uptime') }}</p><p class="metric-number mt-1 text-2xl">99.98%</p></div>
                            <div class="bg-white p-4"><p class="text-xs text-muted">{{ $t('Response') }}</p><p class="metric-number mt-1 text-2xl">184 ms</p></div>
                        </div>
                        <div class="space-y-2 p-4">
                            <div v-for="site in ['Storefront', 'Product docs', 'Company site']" :key="site" class="flex items-center justify-between rounded-xl border border-line p-3.5">
                                <div class="flex min-w-0 items-center gap-3"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-50" /><div><p class="text-sm font-semibold text-ink">{{ site }}</p><p class="text-xs text-muted">{{ $t('Checked recently') }}</p></div></div>
                                <span class="text-sm font-semibold tabular-nums text-ink">99.9%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="how-it-works" class="border-y border-line bg-white py-20 sm:py-24">
                <div class="page-shell">
                    <div class="max-w-2xl">
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-primary-700">{{ $t('Focused by design') }}</p>
                        <h2 class="mt-3 text-3xl font-bold tracking-tight text-ink sm:text-4xl">{{ $t('The essentials for reliable website checks.') }}</h2>
                        <p class="mt-4 text-base leading-7 text-muted">{{ $t('No crowded incident suite—just clear checks, useful history, and email when something changes.') }}</p>
                    </div>
                    <div class="mt-12 grid gap-5 md:grid-cols-3">
                        <article v-for="feature in [
                            { icon: ShieldCheckIcon, title: $t('Safe public checks'), copy: $t('Monitor public HTTP or HTTPS targets with verified TLS and protected redirect handling.') },
                            { icon: BellAlertIcon, title: $t('Meaningful email alerts'), copy: $t('Hear about the first failure and later status changes without noisy success mail.') },
                            { icon: ChartBarIcon, title: $t('Thirty-day context'), copy: $t('Review uptime, response time, HTTP status, TLS state, and recent checks together.') },
                        ]" :key="feature.title" class="rounded-2xl border border-line bg-canvas p-6">
                            <div class="grid h-11 w-11 place-items-center rounded-xl bg-primary-50 text-primary-700"><component :is="feature.icon" class="h-5 w-5" /></div>
                            <h3 class="mt-5 text-lg font-semibold text-ink">{{ feature.title }}</h3>
                            <p class="mt-2 text-sm leading-6 text-muted">{{ feature.copy }}</p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="page-shell py-20 text-center sm:py-24">
                <h2 class="text-3xl font-bold tracking-tight text-ink">{{ $t('A clearer way to watch your websites.') }}</h2>
                <p class="mx-auto mt-3 max-w-xl text-muted">{{ $t('Create an account, verify your email, and add a public website when you are ready.') }}</p>
                <Link :href="route('register')" class="mt-7 inline-flex min-h-12 items-center rounded-xl bg-primary-600 px-5 text-sm font-semibold text-white hover:bg-primary-700">{{ $t('Get started') }}</Link>
            </section>
        </main>

        <footer class="border-t border-line bg-white">
            <div class="page-shell flex flex-col items-center justify-between gap-3 py-8 text-sm text-muted sm:flex-row"><div class="flex items-center gap-2"><ApplicationLogo class="h-7 w-7" /><span class="font-semibold text-ink">PingPing</span></div><p>© 2026 PingPing. {{ $t('Straightforward uptime monitoring.') }}</p></div>
        </footer>
    </div>
</template>
