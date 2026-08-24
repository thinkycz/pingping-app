<script setup>
import { Link } from '@inertiajs/vue3';
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid';
defineProps({ pagination: { type: Object, required: true } });
</script>

<template>
    <nav v-if="pagination.last_page > 1" class="flex items-center justify-between gap-3 border-t border-line px-4 py-3 sm:px-5" :aria-label="$t('Pagination')">
        <Link
            :href="pagination.prev_page_url || '#'"
            class="inline-flex min-h-10 items-center gap-1 rounded-xl border border-line px-3 text-sm font-semibold"
            :class="pagination.prev_page_url ? 'text-ink hover:bg-gray-50' : 'pointer-events-none bg-gray-50 text-gray-500'"
            :aria-disabled="!pagination.prev_page_url"
            preserve-scroll
        ><ChevronLeftIcon class="h-4 w-4" />{{ $t('Previous') }}</Link>
        <span class="text-sm tabular-nums text-muted">{{ pagination.current_page }} / {{ pagination.last_page }}</span>
        <Link
            :href="pagination.next_page_url || '#'"
            dusk="pagination-next"
            class="inline-flex min-h-10 items-center gap-1 rounded-xl border border-line px-3 text-sm font-semibold"
            :class="pagination.next_page_url ? 'text-ink hover:bg-gray-50' : 'pointer-events-none bg-gray-50 text-gray-500'"
            :aria-disabled="!pagination.next_page_url"
            preserve-scroll
        >{{ $t('Next') }}<ChevronRightIcon class="h-4 w-4" /></Link>
    </nav>
</template>
