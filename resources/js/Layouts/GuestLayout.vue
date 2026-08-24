<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-canvas">
        <header class="page-shell flex h-20 items-center justify-between">
            <Link href="/" class="flex items-center gap-2 rounded-lg">
                <ApplicationLogo class="h-9 w-9" />
                <span class="text-lg font-bold tracking-tight text-ink">PingPing</span>
            </Link>
            <LanguageSwitcher />
        </header>
        <main class="page-shell flex min-h-[calc(100vh-8rem)] items-start justify-center pb-12 pt-6 sm:items-center sm:pt-0">
            <div class="surface w-full max-w-md p-6 sm:p-8">
                <slot />
            </div>
        </main>
    </div>
</template>
