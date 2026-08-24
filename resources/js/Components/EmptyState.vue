<script setup>
import { SignalSlashIcon } from '@heroicons/vue/24/outline';
defineProps({ title: { type: String, required: true }, description: String });
</script>

<template>
    <div class="flex flex-col items-center px-5 py-12 text-center">
        <div class="grid h-11 w-11 place-items-center rounded-xl bg-primary-50 text-primary-700"><SignalSlashIcon class="h-5 w-5" /></div>
        <h2 class="mt-4 text-base font-semibold text-ink">{{ title }}</h2>
        <p v-if="description" class="mt-1 max-w-md text-sm leading-6 text-muted">{{ description }}</p>
        <div v-if="$slots.default" class="mt-5"><slot /></div>
    </div>
</template>
