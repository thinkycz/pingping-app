<script setup>
defineProps({ title: { type: String, required: true }, description: String, eyebrow: String });
</script>

<template>
    <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div class="min-w-0">
            <p v-if="eyebrow" class="mb-1 text-xs font-bold uppercase tracking-[0.16em] text-primary-700">{{ eyebrow }}</p>
            <h1 class="text-2xl font-bold tracking-tight text-ink sm:text-3xl">{{ title }}</h1>
            <p v-if="description" class="mt-1.5 max-w-2xl text-sm leading-6 text-muted">{{ description }}</p>
        </div>
        <div v-if="$slots.actions" class="flex shrink-0 flex-wrap gap-2"><slot name="actions" /></div>
    </header>
</template>
