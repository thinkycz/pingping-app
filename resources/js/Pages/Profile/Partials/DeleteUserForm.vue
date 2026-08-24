<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';
const confirming = ref(false); const passwordInput = ref(null); const form = useForm({ password: '' });
const open = async () => { confirming.value = true; await nextTick(); passwordInput.value?.focus(); };
const close = () => { confirming.value = false; form.clearErrors(); form.reset(); };
const remove = () => form.delete(route('profile.destroy'), { preserveScroll: true, onSuccess: close, onError: () => passwordInput.value?.focus(), onFinish: () => form.reset() });
</script>

<template><div><h2 class="text-lg font-semibold text-red-800">{{ $t('Delete account') }}</h2><p class="mt-1 max-w-2xl text-sm leading-6 text-muted">{{ $t('This permanently deletes your account, monitors, and recorded checks. This action cannot be undone.') }}</p><DangerButton dusk="delete-account" class="mt-5" @click="open">{{ $t('Delete account') }}</DangerButton><Modal :show="confirming" max-width="md" aria-labelled-by="account-delete-title" aria-described-by="account-delete-description" @close="close"><form class="p-6" @submit.prevent="remove"><h2 id="account-delete-title" class="text-lg font-bold text-ink">{{ $t('Delete your PingPing account?') }}</h2><p id="account-delete-description" class="mt-2 text-sm leading-6 text-muted">{{ $t('Enter your password to permanently delete the account and all monitoring data.') }}</p><div class="mt-5"><InputLabel for="delete_password" :value="$t('Password')" /><TextInput id="delete_password" ref="passwordInput" v-model="form.password" type="password" class="mt-2" autocomplete="current-password" :aria-invalid="Boolean(form.errors.password)" /><InputError :message="form.errors.password" /></div><div class="mt-6 flex justify-end gap-2"><SecondaryButton @click="close">{{ $t('Cancel') }}</SecondaryButton><DangerButton dusk="confirm-account-delete" type="submit" :disabled="form.processing">{{ $t('Delete account') }}</DangerButton></div></form></Modal></div></template>
