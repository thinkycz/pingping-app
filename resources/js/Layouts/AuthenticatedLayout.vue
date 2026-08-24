<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import FlashMessages from '@/Components/FlashMessages.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronDownIcon } from '@heroicons/vue/20/solid';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const initial = computed(() => user.value?.name?.trim()?.charAt(0)?.toUpperCase() || '?');
</script>

<template>
    <div class="min-h-screen bg-canvas">
        <a href="#main-content" class="sr-only z-[100] rounded-lg bg-white px-4 py-2 text-ink focus:not-sr-only focus:fixed focus:left-4 focus:top-4">{{ $t('Skip to content') }}</a>
        <nav class="sticky top-0 z-50 border-b border-line/90 bg-white/95 backdrop-blur" :aria-label="$t('Main navigation')">
            <div class="page-shell flex h-16 items-center justify-between gap-3">
                <div class="flex min-w-0 items-center gap-5">
                    <Link :href="route('dashboard')" class="flex shrink-0 items-center gap-2 rounded-lg" :aria-label="$t('PingPing dashboard')">
                        <ApplicationLogo class="h-9 w-9" />
                        <span class="text-lg font-bold tracking-tight text-ink">PingPing</span>
                    </Link>
                    <Link :href="route('dashboard')" class="hidden rounded-lg px-3 py-2 text-sm font-semibold text-muted hover:bg-gray-50 hover:text-ink sm:block" :class="{ 'bg-primary-50 text-primary-700': route().current('dashboard') }">{{ $t('Dashboard') }}</Link>
                </div>

                <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                    <LanguageSwitcher />
                    <details class="group relative">
                        <summary class="flex min-h-10 cursor-pointer list-none items-center gap-2 rounded-xl border border-line bg-white p-1.5 pr-2 text-sm font-semibold text-ink hover:bg-gray-50">
                            <span class="grid h-7 w-7 place-items-center rounded-lg bg-primary-100 text-xs font-bold text-primary-700">{{ initial }}</span>
                            <span class="hidden max-w-32 truncate sm:block">{{ user.name }}</span>
                            <ChevronDownIcon class="hidden h-4 w-4 text-muted transition group-open:rotate-180 sm:block" />
                        </summary>
                        <div class="absolute right-0 mt-2 w-52 overflow-hidden rounded-xl border border-line bg-white p-1.5 shadow-lg">
                            <Link :href="route('profile.edit')" class="block rounded-lg px-3 py-2 text-sm font-medium text-ink hover:bg-gray-50">{{ $t('Account settings') }}</Link>
                            <Link :href="route('logout')" method="post" as="button" class="block w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-red-700 hover:bg-red-50">{{ $t('Log out') }}</Link>
                        </div>
                    </details>
                </div>
            </div>
        </nav>

        <FlashMessages />
        <main id="main-content">
            <div v-if="$slots.header" class="border-b border-line bg-white">
                <div class="page-shell py-6"><slot name="header" /></div>
            </div>
            <slot />
        </main>
        <footer class="page-shell py-8 text-center text-xs text-muted">{{ $t('PingPing monitors public websites from one reliable location.') }}</footer>
    </div>
</template>
