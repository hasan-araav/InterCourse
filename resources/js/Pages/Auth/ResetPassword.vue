<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Mail, Lock, RefreshCcw } from 'lucide-vue-next';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Reset Password" />

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Set new password</h1>
            <p class="text-gray-500">Your new password must be different from previously used passwords.</p>
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
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="New Password" class="text-gray-700 font-medium mb-1.5" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <Lock class="h-5 w-5 text-gray-400" />
                    </div>
                    <TextInput
                        id="password"
                        type="password"
                        class="block w-full pl-10 bg-white border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all rounded-xl py-3"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
                <InputLabel for="password_confirmation" value="Confirm New Password" class="text-gray-700 font-medium mb-1.5" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <Lock class="h-5 w-5 text-gray-400" />
                    </div>
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="block w-full pl-10 bg-white border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all rounded-xl py-3"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div>
                <PrimaryButton
                    class="w-full justify-center py-3.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl text-base normal-case tracking-normal transition-all hover:scale-[1.02] active:scale-[0.98]"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    Reset Password
                    <RefreshCcw class="ml-2 w-4 h-4" />
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
