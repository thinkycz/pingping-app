<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

defineProps({ show: Boolean, title: String, description: String, confirmLabel: String, processing: Boolean });
defineEmits(['close', 'confirm']);
</script>

<template>
    <Modal
        :show="show"
        max-width="md"
        aria-labelled-by="confirm-dialog-title"
        aria-described-by="confirm-dialog-description"
        @close="$emit('close')"
    >
        <div class="p-6">
            <h2 id="confirm-dialog-title" class="text-lg font-bold text-ink">{{ title }}</h2>
            <p id="confirm-dialog-description" class="mt-2 text-sm leading-6 text-muted">{{ description }}</p>
            <div class="mt-6 flex justify-end gap-2">
                <SecondaryButton @click="$emit('close')">{{ $t('Cancel') }}</SecondaryButton>
                <DangerButton dusk="confirm-delete" :disabled="processing" @click="$emit('confirm')">{{ confirmLabel }}</DangerButton>
            </div>
        </div>
    </Modal>
</template>
