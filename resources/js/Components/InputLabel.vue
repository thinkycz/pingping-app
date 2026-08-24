<script setup>
defineProps({ value: String });
</script>

<template>
    <label class="block text-sm font-semibold text-ink">
        <span v-if="value">{{ value }}</span>
        <slot v-else />
    </label>
</template>
