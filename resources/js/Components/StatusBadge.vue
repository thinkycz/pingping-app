<script setup>
import { computed } from 'vue';

const props = defineProps({ status: { type: String, required: true } });
const styles = computed(() => ({
    up: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    down: 'bg-red-50 text-red-700 ring-red-200',
    paused: 'bg-amber-50 text-amber-800 ring-amber-200',
    pending: 'bg-blue-50 text-blue-700 ring-blue-200',
})[props.status] || 'bg-gray-50 text-gray-700 ring-gray-200');
const glyph = computed(() => ({ up: '↑', down: '↓', paused: 'Ⅱ', pending: '…' })[props.status] || '•');
</script>

<template>
    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset" :class="styles">
        <span aria-hidden="true">{{ glyph }}</span>
        {{ $t(`status.${status}`) }}
    </span>
</template>
