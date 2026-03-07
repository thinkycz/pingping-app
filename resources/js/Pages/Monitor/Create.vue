<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { PlusIcon, ArrowLeftIcon } from '@heroicons/vue/20/solid';

const form = useForm({
    url: '',
    alias: '',
    interval: 5,
});

const submitMonitor = () => {
    form.post(route('monitors.store'));
};
</script>

<template>
    <Head title="Add Monitor" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link
                    :href="route('dashboard')"
                    class="rounded-full p-2 bg-white text-gray-400 hover:text-gray-500 hover:bg-gray-50 ring-1 ring-inset ring-gray-300 shadow-sm transition"
                >
                    <ArrowLeftIcon class="h-5 w-5" aria-hidden="true" />
                </Link>
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                    Add New Monitor
                </h2>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">
                <div class="bg-white shadow-sm ring-1 ring-gray-200 sm:rounded-xl p-6 lg:p-8">
                    <div class="md:flex md:items-center md:justify-between mb-6">
                        <div class="min-w-0 flex-1">
                            <h3 class="text-lg font-bold leading-7 text-gray-900 sm:truncate sm:text-xl sm:tracking-tight">Create Monitor</h3>
                            <p class="mt-1 text-sm text-gray-500">Create a new check to start monitoring uptime and response times instantly.</p>
                        </div>
                    </div>

                    <form @submit.prevent="submitMonitor" class="mt-4">
                        <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6 items-end">
                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium leading-6 text-gray-900">Target URL</label>
                                <div class="mt-2 rounded-md shadow-sm">
                                    <input
                                        v-model="form.url"
                                        type="url"
                                        required
                                        placeholder="https://example.com"
                                        class="block w-full rounded-md border-0 py-2.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition"
                                    />
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium leading-6 text-gray-900">Friendly Name (Optional)</label>
                                <div class="mt-2">
                                    <input
                                        v-model="form.alias"
                                        type="text"
                                        placeholder="My App Production"
                                        class="block w-full rounded-md border-0 py-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition"
                                    />
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium leading-6 text-gray-900">Check Interval</label>
                                <div class="mt-2">
                                    <select
                                        v-model="form.interval"
                                        class="block w-full rounded-md border-0 py-2.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6 transition cursor-pointer"
                                    >
                                        <option :value="5">5 min</option>
                                        <option :value="15">15 min</option>
                                        <option :value="30">30 min</option>
                                        <option :value="60">60 min</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-x-6 border-t border-gray-100 pt-6">
                            <button
                                type="submit"
                                class="rounded-md bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition flex items-center gap-x-2"
                                :disabled="form.processing"
                                :class="{'opacity-75 cursor-not-allowed': form.processing}"
                            >
                                <PlusIcon v-if="!form.processing" class="h-4 w-4" />
                                <svg v-else class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Create Monitor</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
