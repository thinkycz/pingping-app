<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({ type: String, required: true });
const input = ref(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) input.value.focus();
});

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <input
        ref="input"
        v-model="model"
        class="min-h-11 w-full rounded-xl border-line bg-white px-3.5 py-2.5 text-sm text-ink shadow-sm placeholder:text-gray-400 hover:border-gray-300 focus:border-primary-500 focus:ring-primary-500 disabled:bg-gray-100"
    />
</template>
