<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import EmptyState from '@/Components/EmptyState.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PageHeader from '@/Components/PageHeader.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ArrowLeftIcon, PauseIcon, PlayIcon, TrashIcon } from '@heroicons/vue/20/solid';
import { CategoryScale, Chart as ChartJS, Filler, Legend, LineElement, LinearScale, PointElement, Tooltip } from 'chart.js';
import { Line } from 'vue-chartjs';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Filler, Tooltip, Legend);

const props = defineProps({ monitor: Object, history: Array, recentChecks: Array });
const form = useForm({ url: props.monitor.url, alias: props.monitor.alias || '', interval: props.monitor.interval });
const confirmingDelete = ref(false);
let refreshTimer;

const submit = () => form.put(route('monitors.update', props.monitor.id), { preserveScroll: true });
const remove = () => form.delete(route('monitors.destroy', props.monitor.id));
const formatDate = (value) => value ? new Intl.DateTimeFormat(document.documentElement.lang || 'en', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(value)) : '—';

const chartData = computed(() => ({
    labels: props.history.map((item) => formatDate(item.checked_at)),
    datasets: [{
        label: 'ms',
        data: props.history.map((item) => item.response_time_ms),
        borderColor: '#168d82',
        backgroundColor: 'rgba(22, 141, 130, 0.10)',
        pointRadius: props.history.length < 20 ? 2 : 0,
        borderWidth: 2,
        fill: true,
        tension: 0.25,
    }],
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    animation: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? false : { duration: 250 },
    plugins: { legend: { display: false } },
    interaction: { intersect: false, mode: 'index' },
    scales: {
        x: { display: false, grid: { display: false } },
        y: { beginAtZero: true, ticks: { callback: (value) => `${value} ms`, color: '#5f6f75' }, grid: { color: '#edf1f0' } },
    },
};

onMounted(() => {
    refreshTimer = window.setInterval(() => {
        if (document.visibilityState === 'visible' && !form.isDirty) router.reload({ only: ['monitor', 'history', 'recentChecks'] });
    }, 60000);
});
onBeforeUnmount(() => window.clearInterval(refreshTimer));
</script>

<template>
    <Head :title="monitor.alias || monitor.url" />
    <AuthenticatedLayout>
        <div class="page-shell py-8 sm:py-10">
            <Link :href="route('dashboard')" class="mb-5 inline-flex items-center gap-1.5 rounded-lg text-sm font-semibold text-muted hover:text-ink"><ArrowLeftIcon class="h-4 w-4" />{{ $t('Back to dashboard') }}</Link>
            <PageHeader :title="monitor.alias || monitor.url" :description="monitor.alias ? monitor.url : $t('Monitor detail and recent check history.')">
                <template #actions>
                    <Link :href="route('monitors.toggle', monitor.id)" method="patch" as="button" dusk="toggle-monitor" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-line bg-white px-4 text-sm font-semibold text-ink shadow-sm hover:bg-gray-50"><PlayIcon v-if="!monitor.is_active" class="h-4 w-4" /><PauseIcon v-else class="h-4 w-4" />{{ monitor.is_active ? $t('Pause') : $t('Resume') }}</Link>
                </template>
            </PageHeader>

            <section class="mt-7 grid grid-cols-2 gap-3 lg:grid-cols-4" :aria-label="$t('Current monitor summary')">
                <div class="surface p-4 sm:p-5"><p class="text-xs font-semibold text-muted">{{ $t('Current status') }}</p><div class="mt-2"><StatusBadge :status="monitor.display_state" /></div></div>
                <div class="surface p-4 sm:p-5"><p class="text-xs font-semibold text-muted">{{ $t('30-day uptime') }}</p><p class="metric-number mt-2 text-2xl">{{ monitor.last_checked_at ? `${monitor.uptime_30d.toFixed(2)}%` : '—' }}</p></div>
                <div class="surface p-4 sm:p-5"><p class="text-xs font-semibold text-muted">{{ $t('Latest response') }}</p><p class="metric-number mt-2 text-2xl">{{ monitor.response_time_ms === null ? '—' : `${monitor.response_time_ms} ms` }}</p></div>
                <div class="surface p-4 sm:p-5"><p class="text-xs font-semibold text-muted">{{ $t('HTTP / TLS') }}</p><p class="metric-number mt-2 text-lg">{{ monitor.http_status || '—' }} <span class="text-sm font-medium text-muted">/ {{ $t(`tls.${monitor.ssl.status}`) }}</span></p></div>
            </section>

            <div v-if="monitor.failure_message" class="mt-5 rounded-2xl border border-red-200 bg-red-50 p-4" role="alert">
                <p class="text-sm font-semibold text-red-800">{{ $t('Latest check failed') }}</p>
                <p class="mt-1 text-sm leading-6 text-red-700">{{ monitor.failure_message }}<span v-if="monitor.http_status"> (HTTP {{ monitor.http_status }})</span></p>
            </div>

            <section class="surface mt-6 overflow-hidden" :aria-labelledby="history-heading">
                <div class="border-b border-line px-5 py-4"><h2 id="history-heading" class="font-semibold text-ink">{{ $t('Response time · last 30 days') }}</h2><p class="mt-0.5 text-xs text-muted">{{ $t('A visual trend; exact values are listed in recent checks below.') }}</p></div>
                <div v-if="history.length" class="h-56 p-4 sm:h-64 sm:p-5"><Line :data="chartData" :options="chartOptions" aria-label="Response time history chart" /></div>
                <EmptyState v-else :title="$t('Waiting for the first check')" :description="$t('This monitor is pending. The chart will appear after checks are recorded.')" />
                <table class="sr-only"><caption>{{ $t('Response time history') }}</caption><thead><tr><th>{{ $t('Time') }}</th><th>{{ $t('Status') }}</th><th>{{ $t('Response') }}</th></tr></thead><tbody><tr v-for="item in history" :key="item.checked_at"><td>{{ formatDate(item.checked_at) }}</td><td>{{ item.status }}</td><td>{{ item.response_time_ms }} ms</td></tr></tbody></table>
            </section>

            <section class="surface mt-6 overflow-hidden" :aria-labelledby="recent-heading">
                <div class="border-b border-line px-5 py-4"><h2 id="recent-heading" class="font-semibold text-ink">{{ $t('Recent checks') }}</h2><p class="mt-0.5 text-xs text-muted">{{ $t('The latest 50 recorded checks.') }}</p></div>
                <EmptyState v-if="recentChecks.length === 0" :title="$t('No checks recorded yet')" :description="$t('The first check has been queued.')" />
                <template v-else>
                    <div class="divide-y divide-line sm:hidden"><article v-for="check in recentChecks" :key="check.id" class="p-4"><div class="flex items-center justify-between gap-3"><StatusBadge :status="check.status" /><span class="text-xs text-muted">{{ formatDate(check.checked_at) }}</span></div><dl class="mt-3 grid grid-cols-3 gap-2 text-sm"><div><dt class="text-xs text-muted">{{ $t('Response') }}</dt><dd class="metric-number mt-1">{{ check.response_time_ms }} ms</dd></div><div><dt class="text-xs text-muted">HTTP</dt><dd class="metric-number mt-1">{{ check.http_status || '—' }}</dd></div><div><dt class="text-xs text-muted">TLS</dt><dd class="mt-1 font-medium text-ink">{{ $t(`tls.${check.ssl_status}`) }}</dd></div></dl><p v-if="check.failure_message" class="mt-3 text-xs leading-5 text-red-700">{{ check.failure_message }}</p></article></div>
                    <div class="hidden overflow-x-auto sm:block"><table class="w-full text-left text-sm"><thead class="border-b border-line bg-gray-50/70 text-xs font-semibold uppercase tracking-wide text-muted"><tr><th class="px-5 py-3">{{ $t('Time') }}</th><th class="px-4 py-3">{{ $t('Status') }}</th><th class="px-4 py-3">{{ $t('Response') }}</th><th class="px-4 py-3">HTTP</th><th class="px-5 py-3">TLS</th></tr></thead><tbody class="divide-y divide-line"><tr v-for="check in recentChecks" :key="check.id"><td class="whitespace-nowrap px-5 py-3.5 text-muted">{{ formatDate(check.checked_at) }}</td><td class="px-4 py-3.5"><StatusBadge :status="check.status" /></td><td class="metric-number px-4 py-3.5">{{ check.response_time_ms }} ms</td><td class="metric-number px-4 py-3.5">{{ check.http_status || '—' }}</td><td class="px-5 py-3.5"><span class="font-medium text-ink">{{ $t(`tls.${check.ssl_status}`) }}</span><p v-if="check.failure_message" class="mt-0.5 max-w-xs text-xs text-red-700">{{ check.failure_message }}</p></td></tr></tbody></table></div>
                </template>
            </section>

            <section class="surface mt-6 p-5 sm:p-7" :aria-labelledby="settings-heading">
                <div class="max-w-2xl"><h2 id="settings-heading" class="font-semibold text-ink">{{ $t('Monitor settings') }}</h2><p class="mt-1 text-sm text-muted">{{ $t('Changing the URL resets the current summary and queues a new first check. Existing logs stay intact.') }}</p>
                    <form class="mt-6 space-y-5" novalidate @submit.prevent="submit">
                        <div><InputLabel for="url" :value="$t('Public website URL')" /><TextInput id="url" v-model="form.url" type="url" class="mt-2" required :aria-invalid="Boolean(form.errors.url)" /><InputError :message="form.errors.url" /></div>
                        <div><InputLabel for="alias" :value="$t('Friendly name')" /><TextInput id="alias" v-model="form.alias" dusk="settings-alias" type="text" class="mt-2" :aria-invalid="Boolean(form.errors.alias)" /><InputError :message="form.errors.alias" /></div>
                        <div><InputLabel for="interval" :value="$t('Check interval')" /><select id="interval" v-model="form.interval" class="mt-2 min-h-11 w-full rounded-xl border-line bg-white text-sm focus:border-primary-500 focus:ring-primary-500"><option v-for="minutes in [5,15,30,60]" :key="minutes" :value="minutes">{{ $t(':minutes minutes', { minutes }) }}</option></select><InputError :message="form.errors.interval" /></div>
                        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-line pt-5"><button type="button" dusk="delete-monitor" class="inline-flex min-h-10 items-center gap-2 rounded-xl px-3 text-sm font-semibold text-red-700 hover:bg-red-50" @click="confirmingDelete = true"><TrashIcon class="h-4 w-4" />{{ $t('Delete monitor') }}</button><div class="flex gap-2"><SecondaryButton v-if="form.isDirty" @click="form.reset()">{{ $t('Reset') }}</SecondaryButton><PrimaryButton dusk="save-monitor" :disabled="form.processing || !form.isDirty">{{ form.processing ? $t('Saving…') : $t('Save changes') }}</PrimaryButton></div></div>
                    </form>
                </div>
            </section>
        </div>

        <ConfirmDialog :show="confirmingDelete" :processing="form.processing" :title="$t('Delete this monitor?')" :description="$t('The monitor and all of its recorded checks will be permanently deleted. This cannot be undone.')" :confirm-label="$t('Delete monitor')" @close="confirmingDelete = false" @confirm="remove" />
    </AuthenticatedLayout>
</template>
