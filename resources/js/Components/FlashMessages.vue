<script setup>
import { CheckCircleIcon, ExclamationCircleIcon, XMarkIcon } from '@heroicons/vue/20/solid';
import { usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const page = usePage();
const dismissed = ref(false);
const message = computed(() => page.props.flash?.success || page.props.flash?.error);
const isError = computed(() => Boolean(page.props.flash?.error));
watch(message, () => { dismissed.value = false; });
</script>

<template>
    <div v-if="message && !dismissed" class="fixed inset-x-4 top-20 z-[70] mx-auto max-w-md" role="status" aria-live="polite">
        <div class="flex items-start gap-3 rounded-xl border bg-white p-3.5 shadow-lg" :class="isError ? 'border-red-200' : 'border-emerald-200'">
            <ExclamationCircleIcon v-if="isError" class="mt-0.5 h-5 w-5 shrink-0 text-red-600" />
            <CheckCircleIcon v-else class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" />
            <p class="flex-1 text-sm font-medium text-ink">{{ message }}</p>
            <button type="button" class="rounded-md text-muted hover:text-ink" :aria-label="$t('Dismiss')" @click="dismissed = true"><XMarkIcon class="h-5 w-5" /></button>
        </div>
    </div>
</template>
