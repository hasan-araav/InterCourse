<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Mail, Lock, ArrowRight } from 'lucide-vue-next';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome back</h1>
            <p class="text-gray-500">Please enter your details to sign in.</p>
        </div>

        <div v-if="status" class="mb-6 p-4 rounded-xl bg-green-50 text-sm font-medium text-green-600 border border-green-100">
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
                <div class="flex items-center justify-between mb-1.5">
                    <InputLabel for="password" value="Password" class="text-gray-700 font-medium" />
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-500 transition-colors"
                    >
                        Forgot password?
                    </Link>
                </div>
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
                        autocomplete="current-password"
                        placeholder="••••••••"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center">
                <Checkbox name="remember" v-model:checked="form.remember" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                <span class="ms-2 text-sm text-gray-600 font-medium">Keep me signed in</span>
            </div>

            <div>
                <PrimaryButton
                    class="w-full justify-center py-3.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-xl text-base normal-case tracking-normal transition-all hover:scale-[1.02] active:scale-[0.98]"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    Sign in
                    <ArrowRight class="ml-2 w-5 h-5" />
                </PrimaryButton>
            </div>
        </form>

        <!-- Informational Footer for Login -->
        <div class="mt-8 pt-6 border-t border-gray-100 text-center">
            <p class="text-sm text-gray-500">
                New to InterCourse? 
                <span class="font-medium text-gray-400">Contact your administrator to request access.</span>
            </p>
        </div>
    </GuestLayout>
</template>
