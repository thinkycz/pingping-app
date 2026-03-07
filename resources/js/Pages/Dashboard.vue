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
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-900">{{ $t('Dashboard Overview') }}</h2>
                
                <div class="flex flex-col sm:flex-row items-center gap-4 w-full md:w-auto">
                    <!-- Search Bar -->
                    <div class="relative w-full sm:w-80">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <MagnifyingGlassIcon class="h-4 w-4 text-gray-400" aria-hidden="true" />
                        </div>
                        <input 
                            v-model="search"
                            @keyup.enter="handleSearch"
                            type="text" 
                            class="block w-full rounded-md border-0 py-2 pl-9 pr-9 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 transition hover:ring-gray-400"
                            placeholder="Search monitors..."
                        />
                        <div v-if="search" class="absolute inset-y-0 right-0 flex items-center pr-2">
                            <button @click="clearSearch" class="text-gray-400 hover:text-gray-500 rounded-full p-1 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                <XMarkIcon class="h-4 w-4" aria-hidden="true" />
                            </button>
                        </div>
                    </div>

                    <Link :href="route('monitors.create')" class="whitespace-nowrap inline-flex w-full justify-center sm:w-auto items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition">
                        <PlusIcon class="-ml-0.5 mr-1.5 h-5 w-5" aria-hidden="true" />
                        New Monitor
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">

                <!-- Stats Overview -->
                <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="overflow-hidden rounded-xl bg-white px-4 py-5 shadow-sm ring-1 ring-gray-200 sm:p-6 hover:shadow-md transition flex items-center">
                        <div class="rounded-md bg-indigo-50 p-3 mr-4">
                            <ChartBarIcon class="h-6 w-6 text-indigo-600" aria-hidden="true" />
                        </div>
                        <div>
                            <dt class="truncate text-sm font-medium text-gray-500">{{ $t('Total Monitors') }}</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ totalMonitors }}</dd>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-xl bg-white px-4 py-5 shadow-sm ring-1 ring-gray-200 sm:p-6 hover:shadow-md transition flex items-center">
                        <div class="rounded-md bg-green-50 p-3 mr-4">
                            <ArrowUpCircleIcon class="h-6 w-6 text-green-600" aria-hidden="true" />
                        </div>
                        <div>
                            <dt class="truncate text-sm font-medium text-gray-500">{{ $t('Page Up') }}</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ upMonitors }}</dd>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-xl bg-white px-4 py-5 shadow-sm ring-1 ring-gray-200 sm:p-6 hover:shadow-md transition flex items-center">
                        <div class="rounded-md bg-red-50 p-3 mr-4">
                            <ArrowDownCircleIcon class="h-6 w-6 text-red-600" aria-hidden="true" />
                        </div>
                        <div>
                            <dt class="truncate text-sm font-medium text-gray-500">{{ $t('Page Down') }}</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ downMonitors }}</dd>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-xl bg-white px-4 py-5 shadow-sm ring-1 ring-gray-200 sm:p-6 hover:shadow-md transition flex items-center">
                        <div class="rounded-md bg-gray-50 p-3 mr-4">
                            <PauseIcon class="h-6 w-6 text-gray-600" aria-hidden="true" />
                        </div>
                        <div>
                            <dt class="truncate text-sm font-medium text-gray-500">{{ $t('Page Paused') }}</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ pausedMonitors }}</dd>
                        </div>
                    </div>
                </dl>

                <!-- Monitors Table Card -->
                <div class="bg-white shadow-sm ring-1 ring-gray-200 sm:rounded-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider sm:pl-6">{{ $t('Name') }}</th>
                                    <th scope="col" class="px-3 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $t('Interval') }}</th>
                                    <th scope="col" class="px-3 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $t('SSL/TLS') }}</th>
                                    <th scope="col" class="px-3 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $t('Status') }}</th>
                                    <th scope="col" class="px-3 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $t('Uptime') }}</th>
                                    <th scope="col" class="px-3 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $t('Response Time') }}</th>
                                    <th scope="col" class="px-3 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $t('Last Check') }}</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                        <span class="sr-only">{{ $t('Actions') }}</span>
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
                                    <td colspan="8" class="py-4 text-center text-sm text-gray-500">{{ $t('No monitors found.') }}</td>
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

            </div>
        </div>
    </AuthenticatedLayout>
</template>
