<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
const currentPasswordInput = ref(null); const passwordInput = ref(null);
const form = useForm({ current_password: '', password: '', password_confirmation: '' });
const submit = () => form.put(route('password.update'), { preserveScroll: true, onSuccess: () => form.reset(), onError: () => { if (form.errors.password) passwordInput.value?.focus(); if (form.errors.current_password) currentPasswordInput.value?.focus(); } });
</script>

<template><div><h2 class="text-lg font-semibold text-ink">{{ $t('Password') }}</h2><p class="mt-1 text-sm leading-6 text-muted">{{ $t('Use a long, unique password you do not reuse elsewhere.') }}</p><form class="mt-6 space-y-5" @submit.prevent="submit"><div><InputLabel for="current_password" :value="$t('Current password')" /><TextInput id="current_password" ref="currentPasswordInput" v-model="form.current_password" type="password" class="mt-2" autocomplete="current-password" :aria-invalid="Boolean(form.errors.current_password)" /><InputError :message="form.errors.current_password" /></div><div><InputLabel for="new_password" :value="$t('New password')" /><TextInput id="new_password" ref="passwordInput" v-model="form.password" type="password" class="mt-2" autocomplete="new-password" :aria-invalid="Boolean(form.errors.password)" /><InputError :message="form.errors.password" /></div><div><InputLabel for="password_confirmation" :value="$t('Confirm password')" /><TextInput id="password_confirmation" v-model="form.password_confirmation" type="password" class="mt-2" autocomplete="new-password" /></div><div class="flex items-center gap-3"><PrimaryButton :disabled="form.processing">{{ form.processing ? $t('Saving…') : $t('Update password') }}</PrimaryButton><span v-if="form.recentlySuccessful" role="status" class="text-sm font-medium text-emerald-700">{{ $t('Saved') }}</span></div></form></div></template>
