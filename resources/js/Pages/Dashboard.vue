<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    MagnifyingGlassIcon, 
    XMarkIcon, 
    PauseIcon, 
    PlayIcon, 
    ChartBarIcon, 
    Cog8ToothIcon,
    ArrowUpCircleIcon,
    ArrowDownCircleIcon,
    PlusIcon
} from '@heroicons/vue/20/solid';

const props = defineProps({
    monitors: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');

// Derived stats for the new overview section
// To calculate accurate stats over all data without modifying the backend immediately,
// we'd typically need backend props. For now, since we only have paginated data,
// we'll display stats for the current page to avoid inaccurate "total vs page" math,
// or we will just calculate based on the current page's visible data.
const totalMonitors = computed(() => props.monitors.total || 0);

// Since we don't have global stats from the backend, we calculate up/down/paused
// based ONLY on the current page's data to prevent mathematical impossibilities
// (like total - page active = massive paused number).
const upMonitors = computed(() => props.monitors.data.filter(m => m.is_active && m.status === 'Up').length);
const downMonitors = computed(() => props.monitors.data.filter(m => m.is_active && m.status === 'Down').length);
const pausedMonitors = computed(() => props.monitors.data.filter(m => !m.is_active).length);


const form = useForm({
    url: '',
    alias: '',
    interval: 5,
});

const submitMonitors = () => {
    form.post(route('monitors.store'), {
        onSuccess: () => form.reset(),
    });
};

const handleSearch = () => {
    router.get(route('dashboard'), { search: search.value }, { preserveState: true, replace: true });
};

const clearSearch = () => {
    search.value = '';
    handleSearch();
};

const toggleMonitor = (monitor) => {
    router.patch(route('monitors.toggle', monitor.id), {}, { preserveScroll: true });
};
</script>

<template>
    <Head title="Monitoring" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Dashboard Overview</h2>
                <div class="mt-4 flex md:ml-4 md:mt-0">
                    <button type="button" @click="() => document.getElementById('add-monitor-form').scrollIntoView({ behavior: 'smooth' })" class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition">
                        <PlusIcon class="-ml-0.5 mr-1.5 h-5 w-5" aria-hidden="true" />
                        New Monitor
                    </button>
                </div>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">

                <!-- Stats Overview -->
                <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="relative overflow-hidden rounded-xl bg-white px-4 pb-12 pt-5 shadow-sm ring-1 ring-gray-200 sm:px-6 sm:pt-6 hover:shadow-md transition">
                        <dt>
                            <div class="absolute rounded-md bg-indigo-50 p-3">
                                <ChartBarIcon class="h-6 w-6 text-indigo-600" aria-hidden="true" />
                            </div>
                            <p class="ml-16 truncate text-sm font-medium text-gray-500">Total Monitors</p>
                        </dt>
                        <dd class="ml-16 flex items-baseline pb-6 sm:pb-7">
                            <p class="text-2xl font-semibold text-gray-900">{{ totalMonitors }}</p>
                        </dd>
                    </div>

                    <div class="relative overflow-hidden rounded-xl bg-white px-4 pb-12 pt-5 shadow-sm ring-1 ring-gray-200 sm:px-6 sm:pt-6 hover:shadow-md transition">
                        <dt>
                            <div class="absolute rounded-md bg-green-50 p-3">
                                <ArrowUpCircleIcon class="h-6 w-6 text-green-600" aria-hidden="true" />
                            </div>
                            <p class="ml-16 truncate text-sm font-medium text-gray-500">Page Up</p>
                        </dt>
                        <dd class="ml-16 flex items-baseline pb-6 sm:pb-7">
                            <p class="text-2xl font-semibold text-gray-900">{{ upMonitors }}</p>
                        </dd>
                    </div>

                    <div class="relative overflow-hidden rounded-xl bg-white px-4 pb-12 pt-5 shadow-sm ring-1 ring-gray-200 sm:px-6 sm:pt-6 hover:shadow-md transition">
                        <dt>
                            <div class="absolute rounded-md bg-red-50 p-3">
                                <ArrowDownCircleIcon class="h-6 w-6 text-red-600" aria-hidden="true" />
                            </div>
                            <p class="ml-16 truncate text-sm font-medium text-gray-500">Page Down</p>
                        </dt>
                        <dd class="ml-16 flex items-baseline pb-6 sm:pb-7">
                            <p class="text-2xl font-semibold text-gray-900">{{ downMonitors }}</p>
                        </dd>
                    </div>

                    <div class="relative overflow-hidden rounded-xl bg-white px-4 pb-12 pt-5 shadow-sm ring-1 ring-gray-200 sm:px-6 sm:pt-6 hover:shadow-md transition">
                        <dt>
                            <div class="absolute rounded-md bg-gray-50 p-3">
                                <PauseIcon class="h-6 w-6 text-gray-600" aria-hidden="true" />
                            </div>
                            <p class="ml-16 truncate text-sm font-medium text-gray-500">Page Paused</p>
                        </dt>
                        <dd class="ml-16 flex items-baseline pb-6 sm:pb-7">
                            <p class="text-2xl font-semibold text-gray-900">{{ pausedMonitors }}</p>
                        </dd>
                    </div>
                </dl>
                
                <!-- Search Bar -->
                <div class="relative max-w-xl">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                    </div>
                    <input 
                        v-model="search"
                        @keyup.enter="handleSearch"
                        type="text" 
                        class="block w-full rounded-full border-0 py-3 pl-10 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition hover:ring-gray-300"
                        placeholder="Search monitors by alias or URL..."
                    />
                    <div v-if="search" class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <button @click="clearSearch" class="text-gray-400 hover:text-gray-500 rounded-full p-1 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <XMarkIcon class="h-5 w-5" aria-hidden="true" />
                        </button>
                    </div>
                </div>

                <!-- Monitors Table Card -->
                <div class="bg-white shadow-sm ring-1 ring-gray-200 sm:rounded-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider sm:pl-6">Name</th>
                                    <th scope="col" class="px-3 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Interval</th>
                                    <th scope="col" class="px-3 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">SSL/TLS</th>
                                    <th scope="col" class="px-3 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-3 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Uptime</th>
                                    <th scope="col" class="px-3 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Response Time</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Last Check</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="monitor in monitors.data" :key="monitor.id" :class="{'opacity-50': !monitor.is_active, 'hover:bg-gray-50': true}">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                        <div class="flex items-center gap-x-3">
                                            <div :class="[monitor.is_active && monitor.status === 'Up' ? 'bg-green-500' : (monitor.is_active && monitor.status === 'Down' ? 'bg-red-500' : 'bg-gray-400'), 'h-2.5 w-2.5 rounded-full ring-2 ring-white']"></div>
                                            <Link :href="route('monitors.show', monitor.id)" class="text-indigo-600 hover:text-indigo-900 font-semibold truncate max-w-[200px] inline-block" :title="monitor.alias || monitor.url">
                                                {{ monitor.alias || monitor.url }}
                                            </Link>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-center text-gray-500">
                                        {{ monitor.interval }}m
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-center">
                                        <span 
                                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                            :class="{
                                                'bg-gray-100 text-gray-800': monitor.ssl_status === 'None' || monitor.ssl_status === 'disabled',
                                                'bg-red-100 text-red-800': monitor.ssl_status === 'Invalid',
                                                'bg-green-100 text-green-800': monitor.ssl_status === 'Valid'
                                            }"
                                        >
                                            {{ monitor.ssl_status }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-center">
                                        <span 
                                            class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset"
                                            :class="{
                                                'bg-gray-50 text-gray-600 ring-gray-500/10': monitor.status === 'disabled' || !monitor.is_active,
                                                'bg-red-50 text-red-700 ring-red-600/10': monitor.status === 'Down' && monitor.is_active,
                                                'bg-green-50 text-green-700 ring-green-600/20': monitor.status === 'Up' && monitor.is_active
                                            }"
                                        >
                                            {{ !monitor.is_active ? 'Paused' : monitor.status }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-center">
                                        <div class="flex items-center justify-center">
                                            <span
                                                class="font-mono text-sm"
                                                :class="{
                                                    'text-green-600 font-semibold': monitor.uptime_percentage >= 99,
                                                    'text-red-600 font-semibold': monitor.uptime_percentage < 99 && monitor.uptime_percentage >= 0,
                                                    'text-gray-500': !monitor.is_active
                                                }"
                                            >
                                                {{ monitor.uptime_percentage }}%
                                            </span>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 text-right">
                                        {{ monitor.response_time !== null ? monitor.response_time + 'ms' : '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 text-left">
                                        {{ monitor.last_checked_at ? new Date(monitor.last_checked_at).toLocaleString() : 'Never' }}
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <div class="flex items-center justify-end space-x-4 text-gray-400">
                                            <button @click="toggleMonitor(monitor)" class="hover:text-indigo-600 transition focus:outline-none" :title="monitor.is_active ? 'Pause' : 'Resume'">
                                                <PlayIcon v-if="!monitor.is_active" class="h-5 w-5" />
                                                <PauseIcon v-else class="h-5 w-5" />
                                            </button>
                                            <Link :href="route('monitors.show', monitor.id)" class="hover:text-indigo-600 transition focus:outline-none" title="View Details">
                                                <span class="sr-only">View Details</span>
                                                <ChartBarIcon class="h-5 w-5" />
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="monitors.data.length === 0">
                                    <td colspan="8" class="py-4 text-center text-sm text-gray-500">No monitors found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination Footer -->
                    <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing <span class="font-medium">{{ monitors.from || 0 }}</span> to <span class="font-medium">{{ monitors.to || 0 }}</span> of <span class="font-medium">{{ monitors.total }}</span> results
                                </p>
                            </div>
                            <div>
                                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                    <button
                                        :disabled="!monitors.prev_page_url"
                                        @click="router.get(monitors.prev_page_url)"
                                        class="relative inline-flex items-center rounded-l-md px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                                        :class="!monitors.prev_page_url ? 'text-gray-300' : 'text-gray-900'"
                                    >
                                        Previous
                                    </button>
                                    <button
                                        :disabled="!monitors.next_page_url"
                                        @click="router.get(monitors.next_page_url)"
                                        class="relative inline-flex items-center rounded-r-md px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                                        :class="!monitors.next_page_url ? 'text-gray-300' : 'text-gray-900'"
                                    >
                                        Next
                                    </button>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Monitor Form -->
                <div id="add-monitor-form" class="bg-white shadow-sm ring-1 ring-gray-200 sm:rounded-xl p-6 lg:p-8">
                    <div class="md:flex md:items-center md:justify-between mb-6">
                        <div class="min-w-0 flex-1">
                            <h3 class="text-lg font-bold leading-7 text-gray-900 sm:truncate sm:text-xl sm:tracking-tight">Add New Monitor</h3>
                            <p class="mt-1 text-sm text-gray-500">Create a new check to start monitoring uptime and response times instantly.</p>
                        </div>
                    </div>

                    <form @submit.prevent="submitMonitors" class="mt-4">
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

                            <div class="sm:col-span-2">
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

                            <div class="sm:col-span-1">
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
