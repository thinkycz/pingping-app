<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
    MagnifyingGlassIcon, 
    XMarkIcon, 
    PauseIcon, 
    PlayIcon, 
    ChartBarIcon, 
    Cog8ToothIcon 
} from '@heroicons/vue/20/solid';

const props = defineProps({
    monitors: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');

const form = useForm({
    url: '',
    alias: '',
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
            <h2 class="text-xl font-bold text-gray-800">Monitoring</h2>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                
                <!-- Search Bar -->
                <div class="mb-6 relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                    </div>
                    <input 
                        v-model="search"
                        @keyup.enter="handleSearch"
                        type="text" 
                        class="block w-full rounded-md border-0 py-3 pl-10 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6" 
                        placeholder="Searching monitors by alias or URL..." 
                    />
                    <div v-if="search" class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <button @click="clearSearch" class="text-gray-400 hover:text-gray-500">
                            <XMarkIcon class="h-5 w-5" aria-hidden="true" />
                        </button>
                    </div>
                </div>

                <!-- Monitors Table Card -->
                <div class="bg-white shadow-sm ring-1 ring-gray-200 sm:rounded-lg mb-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider sm:pl-6">Name</th>
                                    <th scope="col" class="px-3 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">SSL/TLS</th>
                                    <th scope="col" class="px-3 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-3 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Uptime</th>
                                    <th scope="col" class="px-3 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Response</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Last Check</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="monitor in monitors.data" :key="monitor.id" :class="{'opacity-50': !monitor.is_active}">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                        {{ monitor.alias || monitor.url }}
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
                                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                            :class="{
                                                'bg-gray-100 text-gray-800': monitor.status === 'disabled',
                                                'bg-red-100 text-red-800': monitor.status === 'Down',
                                                'bg-green-100 text-green-800': monitor.status === 'Up'
                                            }"
                                        >
                                            {{ monitor.status }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-center">
                                        <span 
                                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                            :class="{
                                                'bg-green-100 text-green-800': monitor.uptime_percentage >= 99,
                                                'bg-red-100 text-red-800': monitor.uptime_percentage < 99 && monitor.uptime_percentage >= 0,
                                                'bg-gray-100 text-gray-800': monitor.status === 'disabled'
                                            }"
                                        >
                                            <span v-if="monitor.status !== 'disabled'">{{ monitor.uptime_percentage }}%</span>
                                            <span v-else>100%</span>
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 text-right">
                                        {{ monitor.response_time }}s
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 text-left">
                                        <!-- Real app might use moment/dayjs, fake strings for now based on mockup -->
                                        a few seconds ago
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <div class="flex items-center justify-end space-x-3 text-gray-400">
                                            <button @click="toggleMonitor(monitor)" class="hover:text-gray-600 focus:outline-none">
                                                <PlayIcon v-if="!monitor.is_active" class="h-5 w-5" />
                                                <PauseIcon v-else class="h-5 w-5 stroke-2" />
                                            </button>
                                            <button class="hover:text-gray-600 focus:outline-none">
                                                <ChartBarIcon class="h-5 w-5" />
                                            </button>
                                            <button class="hover:text-gray-600 focus:outline-none">
                                                <Cog8ToothIcon class="h-5 w-5" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="monitors.data.length === 0">
                                    <td colspan="7" class="py-4 text-center text-sm text-gray-500">No monitors found.</td>
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
                <form @submit.prevent="submitMonitors" class="flex gap-4 items-center">
                    <div class="flex-1">
                        <input 
                            v-model="form.url"
                            type="text" 
                            required
                            placeholder="https://example.com"
                            class="block w-full rounded-md border-0 py-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                        />
                    </div>
                    <div class="flex-1">
                        <input 
                            v-model="form.alias"
                            type="text" 
                            placeholder="My Website (optional)"
                            class="block w-full rounded-md border-0 py-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                        />
                    </div>
                    <button 
                        type="submit" 
                        class="rounded-md bg-[#3B82F6] px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                        :disabled="form.processing"
                    >
                        Add monitor
                    </button>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
