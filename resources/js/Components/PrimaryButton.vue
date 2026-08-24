<script setup>
defineProps({ type: { type: String, default: 'submit' } });
</script>

<template>
    <button
        :type="type"
        class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700 disabled:cursor-not-allowed disabled:opacity-50"
    >
        <slot />
    </button>
</template>
