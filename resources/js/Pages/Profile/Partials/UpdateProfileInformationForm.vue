<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
defineProps({ mustVerifyEmail: Boolean, status: String });
const user = usePage().props.auth.user;
const form = useForm({ name: user.name, email: user.email });
</script>

<template><div><h2 class="text-lg font-semibold text-ink">{{ $t('Profile information') }}</h2><p class="mt-1 text-sm leading-6 text-muted">{{ $t('Used for account access and status notification email.') }}</p><form class="mt-6 space-y-5" @submit.prevent="form.patch(route('profile.update'), { preserveScroll: true })"><div><InputLabel for="name" :value="$t('Name')" /><TextInput id="name" v-model="form.name" class="mt-2" autocomplete="name" required :aria-invalid="Boolean(form.errors.name)" /><InputError :message="form.errors.name" /></div><div><InputLabel for="email" :value="$t('Email address')" /><TextInput id="email" v-model="form.email" type="email" class="mt-2" autocomplete="username" required :aria-invalid="Boolean(form.errors.email)" /><InputError :message="form.errors.email" /></div><div v-if="mustVerifyEmail && user.email_verified_at === null" class="rounded-xl bg-amber-50 p-3 text-sm text-amber-800">{{ $t('Your email address is not verified.') }} <Link :href="route('verification.send')" method="post" as="button" class="font-semibold underline">{{ $t('Send another verification email') }}</Link><p v-if="status === 'verification-link-sent'" class="mt-1 font-semibold">{{ $t('A new verification link has been sent.') }}</p></div><div class="flex items-center gap-3"><PrimaryButton :disabled="form.processing || !form.isDirty">{{ form.processing ? $t('Saving…') : $t('Save changes') }}</PrimaryButton><span v-if="form.recentlySuccessful" role="status" class="text-sm font-medium text-emerald-700">{{ $t('Saved') }}</span></div></form></div></template>
