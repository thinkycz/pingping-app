<script setup>
import { computed, nextTick, ref, watch } from 'vue';

const props = defineProps({
    show: { type: Boolean, default: false },
    maxWidth: { type: String, default: 'lg' },
    closeable: { type: Boolean, default: true },
    ariaLabelledBy: { type: String, default: undefined },
    ariaDescribedBy: { type: String, default: undefined },
});
const emit = defineEmits(['close']);
const dialog = ref(null);
const maxWidthClass = computed(() => ({ sm: 'max-w-sm', md: 'max-w-md', lg: 'max-w-lg', xl: 'max-w-xl' })[props.maxWidth]);

watch(() => props.show, async (show) => {
    await nextTick();
    if (show && !dialog.value?.open) dialog.value?.showModal();
    if (!show && dialog.value?.open) dialog.value?.close();
});

const requestClose = () => {
    if (props.closeable) emit('close');
};
</script>

<template>
    <dialog
        ref="dialog"
        class="m-auto w-[calc(100%-2rem)] bg-transparent p-0 backdrop:bg-ink/45"
        :class="maxWidthClass"
        :aria-labelledby="ariaLabelledBy"
        :aria-describedby="ariaDescribedBy"
        @cancel.prevent="requestClose"
        @click.self="requestClose"
    >
        <div v-if="show" class="surface overflow-hidden" role="document">
            <slot />
        </div>
    </dialog>
</template>
