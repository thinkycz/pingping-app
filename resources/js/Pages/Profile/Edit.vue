<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
defineProps({ mustVerifyEmail: Boolean, status: String });
</script>

<template><Head :title="$t('Account settings')" /><AuthenticatedLayout><div class="page-shell py-8 sm:py-10"><PageHeader :title="$t('Account settings')" :description="$t('Keep your identity, password, and account access up to date.')" /><div class="mt-7 grid gap-6 lg:grid-cols-2"><section class="surface p-5 sm:p-7"><UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status" /></section><section class="surface p-5 sm:p-7"><UpdatePasswordForm /></section><section class="rounded-2xl border border-red-200 bg-white p-5 shadow-surface sm:p-7 lg:col-span-2"><DeleteUserForm /></section></div></div></AuthenticatedLayout></template>
