<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import EmptyState from '@/Components/EmptyState.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowRightIcon, MagnifyingGlassIcon, PauseIcon, PlayIcon, PlusIcon } from '@heroicons/vue/20/solid';
import { onBeforeUnmount, onMounted, ref, watch } from 'vue';

const props = defineProps({ monitors: Object, stats: Object, filters: Object });
const search = ref(props.filters.search || '');
let searchTimer;
let refreshTimer;

const statusFilters = [
    { value: 'all', label: 'All', count: 'total' },
    { value: 'up', label: 'Up', count: 'up' },
    { value: 'down', label: 'Down', count: 'down' },
    { value: 'pending', label: 'Pending', count: 'pending' },
    { value: 'paused', label: 'Paused', count: 'paused' },
];

const visit = (status = props.filters.status, query = search.value) => {
    router.get(route('dashboard'), { status, search: query || undefined }, {
        preserveScroll: true,
        preserveState: true,
        replace: true,
        only: ['monitors', 'stats', 'filters'],
    });
};

watch(search, (value) => {
    window.clearTimeout(searchTimer);
    searchTimer = window.setTimeout(() => visit(props.filters.status, value), 350);
});

const formatDate = (value) => value
    ? new Intl.DateTimeFormat(document.documentElement.lang || 'en', { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(value))
    : '—';

onMounted(() => {
    refreshTimer = window.setInterval(() => {
        if (document.visibilityState === 'visible') router.reload({ only: ['monitors', 'stats'] });
    }, 60000);
});

onBeforeUnmount(() => {
    window.clearTimeout(searchTimer);
    window.clearInterval(refreshTimer);
});
</script>

<template>
    <Head :title="$t('Dashboard')" />
    <AuthenticatedLayout>
        <div class="page-shell py-8 sm:py-10">
            <PageHeader :title="$t('Your monitors')" :description="$t('A focused view of every public website you watch.')">
                <template #actions>
                    <Link :href="route('monitors.create')" dusk="new-monitor" class="inline-flex min-h-10 items-center gap-2 rounded-xl bg-primary-600 px-4 text-sm font-semibold text-white hover:bg-primary-700"><PlusIcon class="h-4 w-4" />{{ $t('New monitor') }}</Link>
                </template>
            </PageHeader>

            <section class="mt-7 grid grid-cols-2 gap-3 lg:grid-cols-5" :aria-label="$t('Monitor totals')">
                <button
                    v-for="filter in statusFilters"
                    :key="filter.value"
                        type="button"
                        :dusk="`filter-${filter.value}`"
                    class="rounded-2xl border p-4 text-left transition"
                    :class="filters.status === filter.value ? 'border-primary-500 bg-primary-50 ring-1 ring-primary-500' : 'border-line bg-white hover:border-gray-300'"
                    :aria-pressed="filters.status === filter.value"
                    @click="visit(filter.value)"
                >
                    <span class="text-xs font-semibold text-muted">{{ $t(filter.label) }}</span>
                    <span class="metric-number mt-1 block text-2xl">{{ stats[filter.count] }}</span>
                </button>
            </section>

            <section class="surface mt-6 overflow-hidden" :aria-label="$t('Monitor list')">
                <div class="border-b border-line p-4 sm:p-5">
                    <label for="monitor-search" class="sr-only">{{ $t('Search monitors') }}</label>
                    <div class="relative max-w-md">
                        <MagnifyingGlassIcon class="pointer-events-none absolute left-3.5 top-3 h-5 w-5 text-gray-400" />
                        <input id="monitor-search" v-model="search" dusk="monitor-search" type="search" class="min-h-11 w-full rounded-xl border-line pl-10 pr-3 text-sm focus:border-primary-500 focus:ring-primary-500" :placeholder="$t('Search by name or URL')" />
                    </div>
                </div>

                <EmptyState v-if="monitors.data.length === 0" :title="$t(filters.search || filters.status !== 'all' ? 'No matching monitors' : 'No monitors yet')" :description="$t(filters.search || filters.status !== 'all' ? 'Try a different search or status filter.' : 'Add a public website to queue its first check.')">
                    <Link v-if="!filters.search && filters.status === 'all'" :href="route('monitors.create')" class="inline-flex min-h-10 items-center rounded-xl bg-primary-600 px-4 text-sm font-semibold text-white">{{ $t('Create monitor') }}</Link>
                </EmptyState>

                <template v-else>
                    <div dusk="mobile-monitor-list" class="divide-y divide-line md:hidden">
                        <article v-for="monitor in monitors.data" :key="monitor.id" class="p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0"><Link :href="route('monitors.show', monitor.id)" class="block truncate font-semibold text-ink hover:text-primary-700">{{ monitor.alias || monitor.url }}</Link><p v-if="monitor.alias" class="mt-0.5 truncate text-xs text-muted">{{ monitor.url }}</p></div>
                                <StatusBadge :status="monitor.display_state" />
                            </div>
                            <dl class="mt-4 grid grid-cols-3 gap-3 text-sm">
                                <div><dt class="text-xs text-muted">{{ $t('30-day uptime') }}</dt><dd class="metric-number mt-1">{{ monitor.last_checked_at ? `${monitor.uptime_30d.toFixed(2)}%` : '—' }}</dd></div>
                                <div><dt class="text-xs text-muted">{{ $t('Response') }}</dt><dd class="metric-number mt-1">{{ monitor.response_time_ms === null ? '—' : `${monitor.response_time_ms} ms` }}</dd></div>
                                <div><dt class="text-xs text-muted">{{ $t('Last check') }}</dt><dd class="mt-1 truncate text-xs font-medium text-ink">{{ formatDate(monitor.last_checked_at) }}</dd></div>
                            </dl>
                            <div class="mt-4 flex items-center justify-between border-t border-line pt-3">
                                <Link :href="route('monitors.toggle', monitor.id)" method="patch" as="button" class="inline-flex min-h-9 items-center gap-1.5 rounded-lg px-2 text-xs font-semibold text-muted hover:bg-gray-50 hover:text-ink"><PlayIcon v-if="!monitor.is_active" class="h-4 w-4" /><PauseIcon v-else class="h-4 w-4" />{{ monitor.is_active ? $t('Pause') : $t('Resume') }}</Link>
                                <Link :href="route('monitors.show', monitor.id)" class="inline-flex min-h-9 items-center gap-1 rounded-lg px-2 text-xs font-semibold text-primary-700 hover:bg-primary-50">{{ $t('Details') }}<ArrowRightIcon class="h-4 w-4" /></Link>
                            </div>
                        </article>
                    </div>

                    <div class="hidden overflow-x-auto md:block">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b border-line bg-gray-50/70 text-xs font-semibold uppercase tracking-wide text-muted"><tr><th class="px-5 py-3">{{ $t('Monitor') }}</th><th class="px-4 py-3">{{ $t('Status') }}</th><th class="px-4 py-3">{{ $t('30-day uptime') }}</th><th class="px-4 py-3">{{ $t('Response') }}</th><th class="px-4 py-3">{{ $t('Last check') }}</th><th class="px-5 py-3 text-right"><span class="sr-only">{{ $t('Actions') }}</span></th></tr></thead>
                            <tbody class="divide-y divide-line">
                                <tr v-for="monitor in monitors.data" :key="monitor.id" class="hover:bg-gray-50/70">
                                    <td class="max-w-xs px-5 py-4"><Link :href="route('monitors.show', monitor.id)" class="block truncate font-semibold text-ink hover:text-primary-700">{{ monitor.alias || monitor.url }}</Link><p v-if="monitor.alias" class="mt-0.5 truncate text-xs text-muted">{{ monitor.url }}</p></td>
                                    <td class="px-4 py-4"><StatusBadge :status="monitor.display_state" /></td>
                                    <td class="metric-number px-4 py-4">{{ monitor.last_checked_at ? `${monitor.uptime_30d.toFixed(2)}%` : '—' }}</td>
                                    <td class="metric-number px-4 py-4">{{ monitor.response_time_ms === null ? '—' : `${monitor.response_time_ms} ms` }}</td>
                                    <td class="whitespace-nowrap px-4 py-4 text-muted">{{ formatDate(monitor.last_checked_at) }}</td>
                                    <td class="px-5 py-4 text-right"><Link :href="route('monitors.show', monitor.id)" class="inline-flex min-h-9 items-center gap-1 rounded-lg px-2 text-xs font-semibold text-primary-700 hover:bg-primary-50">{{ $t('Details') }}<ArrowRightIcon class="h-4 w-4" /></Link></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </template>
                <Pagination :pagination="monitors" />
            </section>
        </div>
    </AuthenticatedLayout>
</template>
