<script setup>
defineProps({ message: String });
</script>

<template>
    <p v-if="message" role="alert" class="mt-1.5 flex items-start gap-1.5 text-sm text-red-700">
        <span aria-hidden="true">●</span>
        <span>{{ message }}</span>
    </p>
</template>
