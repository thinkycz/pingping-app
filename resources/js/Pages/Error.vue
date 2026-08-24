<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({ status: Number });
const content = computed(() => ({
    403: { title: 'Access denied', description: 'You do not have permission to open this monitor or page.' },
    404: { title: 'Page not found', description: 'The page may have moved, or the address may be incorrect.' },
    419: { title: 'Session expired', description: 'Your session token expired. Return to the app and try the action again.' },
    500: { title: 'Something went wrong', description: 'PingPing could not complete this request. Please try again shortly.' },
    503: { title: 'Temporarily unavailable', description: 'PingPing is briefly unavailable while maintenance is completed.' },
})[props.status] || { title: 'Unexpected response', description: 'PingPing could not complete this request.' });
</script>

<template><Head :title="$t(content.title)" /><div class="min-h-screen bg-canvas"><header class="page-shell flex h-20 items-center justify-between"><Link href="/" class="flex items-center gap-2"><ApplicationLogo class="h-9 w-9" /><span class="text-lg font-bold text-ink">PingPing</span></Link><LanguageSwitcher /></header><main class="page-shell grid min-h-[calc(100vh-8rem)] place-items-center pb-16 text-center"><div class="max-w-lg"><p class="text-sm font-bold tabular-nums text-primary-700">{{ status }}</p><h1 class="mt-3 text-3xl font-bold tracking-tight text-ink sm:text-4xl">{{ $t(content.title) }}</h1><p class="mt-4 text-base leading-7 text-muted">{{ $t(content.description) }}</p><div class="mt-7 flex justify-center gap-3"><Link href="/" class="inline-flex min-h-11 items-center rounded-xl border border-line bg-white px-4 text-sm font-semibold text-ink">{{ $t('Home') }}</Link><Link :href="route('dashboard')" class="inline-flex min-h-11 items-center rounded-xl bg-primary-600 px-4 text-sm font-semibold text-white">{{ $t('Dashboard') }}</Link></div></div></main></div></template>
