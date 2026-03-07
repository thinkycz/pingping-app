<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Line } from 'vue-chartjs';
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend } from 'chart.js';
import { ArrowLeftIcon, TrashIcon } from '@heroicons/vue/20/solid';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend);

const props = defineProps({
    monitor: Object,
    uptime30d: Number,
    chartData: Array,
    recentLogs: Array,
});

const form = useForm({
    url: props.monitor.url,
    alias: props.monitor.alias,
    interval: props.monitor.interval,
});

const updateMonitor = () => {
    form.put(route('monitors.update', props.monitor.id));
};

const deleteMonitor = () => {
    if (confirm('Are you sure you want to delete this monitor? All history will be lost.')) {
        router.delete(route('monitors.destroy', props.monitor.id));
    }
};

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        y: {
            beginAtZero: true,
            title: {
                display: true,
                text: 'Response Time (ms)'
            }
        },
        x: {
            display: false // Hide x-axis labels to avoid clutter
        }
    },
    plugins: {
        legend: {
            display: false
        },
        tooltip: {
            callbacks: {
                label: function(context) {
                    return `Response Time: ${context.parsed.y} ms`;
                }
            }
        }
    }
};

const chartDataObj = {
    labels: props.chartData.map(d => new Date(d.x).toLocaleString()),
    datasets: [
        {
            label: 'Response Time',
            backgroundColor: '#3B82F6',
            borderColor: '#3B82F6',
            data: props.chartData.map(d => d.y),
            tension: 0.1,
            pointRadius: 2,
        }
    ]
};
</script>

<template>
    <Head :title="monitor.alias || monitor.url" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-4">
                <button @click="router.visit(route('dashboard'))" class="text-gray-500 hover:text-gray-700 transition">
                    <ArrowLeftIcon class="h-6 w-6" />
                </button>
                <h2 class="text-xl font-bold text-gray-800">
                    {{ monitor.alias || monitor.url }}
                </h2>
                <span
                    class="ml-4 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                    :class="{
                        'bg-gray-100 text-gray-800': monitor.status === 'disabled' || !monitor.is_active,
                        'bg-red-100 text-red-800': monitor.status === 'Down' && monitor.is_active,
                        'bg-green-100 text-green-800': monitor.status === 'Up' && monitor.is_active
                    }"
                >
                    {{ !monitor.is_active ? 'Paused' : monitor.status }}
                </span>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">

                <!-- Stats Overview -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-4">
                    <div class="overflow-hidden rounded-lg bg-white shadow ring-1 ring-gray-200 p-5">
                        <dt class="truncate text-sm font-medium text-gray-500">Total Uptime</dt>
                        <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ monitor.uptime_percentage }}%</dd>
                    </div>
                    <div class="overflow-hidden rounded-lg bg-white shadow ring-1 ring-gray-200 p-5">
                        <dt class="truncate text-sm font-medium text-gray-500">30-Day Uptime</dt>
                        <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ uptime30d }}%</dd>
                    </div>
                    <div class="overflow-hidden rounded-lg bg-white shadow ring-1 ring-gray-200 p-5">
                        <dt class="truncate text-sm font-medium text-gray-500">Latest Response Time</dt>
                        <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ monitor.response_time ? monitor.response_time + 'ms' : '-' }}</dd>
                    </div>
                    <div class="overflow-hidden rounded-lg bg-white shadow ring-1 ring-gray-200 p-5">
                        <dt class="truncate text-sm font-medium text-gray-500">SSL Certificate</dt>
                        <dd class="mt-1 flex items-baseline gap-2">
                            <span class="text-xl font-semibold tracking-tight text-gray-900">{{ monitor.ssl_status }}</span>
                            <span v-if="monitor.ssl_expiration_date" class="text-sm text-gray-500 block mt-1">
                                Exp: {{ new Date(monitor.ssl_expiration_date).toLocaleDateString() }}
                            </span>
                        </dd>
                    </div>
                </div>

                <!-- Chart -->
                <div class="bg-white shadow ring-1 ring-gray-200 sm:rounded-lg p-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">Response Time History (Last 30 Days)</h3>
                    <div class="h-64">
                        <Line v-if="chartData.length > 0" :data="chartDataObj" :options="chartOptions" />
                        <div v-else class="h-full flex items-center justify-center text-gray-500">
                            No data available yet.
                        </div>
                    </div>
                </div>

                <!-- Settings & Recent Logs Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- Settings Form -->
                    <div class="bg-white shadow ring-1 ring-gray-200 sm:rounded-lg p-6 lg:col-span-1">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-base font-semibold leading-6 text-gray-900">Monitor Settings</h3>
                            <button @click="deleteMonitor" class="text-red-600 hover:text-red-900 focus:outline-none" title="Delete Monitor">
                                <TrashIcon class="h-5 w-5" />
                            </button>
                        </div>
                        <form @submit.prevent="updateMonitor" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium leading-6 text-gray-900">URL</label>
                                <input v-model="form.url" type="url" required class="mt-1 block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium leading-6 text-gray-900">Alias</label>
                                <input v-model="form.alias" type="text" class="mt-1 block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium leading-6 text-gray-900">Check Interval</label>
                                <select v-model="form.interval" class="mt-1 block w-full rounded-md border-0 py-2 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm sm:leading-6">
                                    <option :value="5">5 minutes</option>
                                    <option :value="15">15 minutes</option>
                                    <option :value="30">30 minutes</option>
                                    <option :value="60">60 minutes</option>
                                </select>
                            </div>
                            <div class="pt-2 flex justify-end">
                                <button type="submit" :disabled="form.processing" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Recent Logs -->
                    <div class="bg-white shadow ring-1 ring-gray-200 sm:rounded-lg lg:col-span-2 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-base font-semibold leading-6 text-gray-900">Recent Checks</h3>
                        </div>
                        <div class="overflow-y-auto max-h-96">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Response</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="log in recentLogs" :key="log.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" :title="log.date">
                                            {{ log.created_at }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                                :class="{
                                                    'bg-red-100 text-red-800': log.status === 'Down',
                                                    'bg-green-100 text-green-800': log.status === 'Up'
                                                }"
                                            >
                                                {{ log.status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ log.response_time ? log.response_time + 'ms' : '-' }}
                                        </td>
                                    </tr>
                                    <tr v-if="recentLogs.length === 0">
                                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">No logs found.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
