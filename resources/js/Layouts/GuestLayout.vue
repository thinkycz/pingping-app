<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gray-50 pt-6 sm:justify-center sm:pt-0"
    >
        <div class="absolute top-0 right-0 p-4">
            <LanguageSwitcher />
        </div>
        <div class="mb-8">
            <Link href="/" class="flex flex-col items-center space-y-4">
                <ApplicationLogo class="h-16 w-16 text-indigo-600" />
                <span class="text-2xl font-bold tracking-tight text-gray-900">UptimePro</span>
            </Link>
        </div>

        <div
            class="mt-2 w-full overflow-hidden bg-white px-8 py-10 shadow-xl shadow-gray-200/50 sm:max-w-md sm:rounded-2xl border border-gray-100"
        >
            <slot />
        </div>
    </div>
</template>
