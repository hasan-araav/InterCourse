<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Mail, ArrowLeft, Send } from 'lucide-vue-next';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password" />

        <div class="mb-8">
            <Link
                :href="route('login')"
                class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500 mb-6 transition-colors"
            >
                <ArrowLeft class="mr-2 w-4 h-4" />
                Back to login
            </Link>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Forgot password?</h1>
            <p class="text-gray-500">No worries, we'll send you reset instructions.</p>
        </div>

        <div
            v-if="status"
            class="mb-6 p-4 rounded-xl bg-green-50 text-sm font-medium text-green-600 border border-green-100"
        >
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="email" value="Email Address" class="text-gray-700 font-medium mb-1.5" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <Mail class="h-5 w-5 text-gray-400" />
                    </div>
                    <TextInput
                        id="email"
                        type="email"
                        class="block w-full pl-10 bg-white border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all rounded-xl py-3"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="you@company.com"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <PrimaryButton
                    class="w-full justify-center py-3.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl text-base normal-case tracking-normal transition-all hover:scale-[1.02] active:scale-[0.98]"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    Reset Password
                    <Send class="ml-2 w-4 h-4" />
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
