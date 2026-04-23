<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
    LayoutDashboard,
    Calendar,
    Users,
    BarChart3,
    Settings,
    UserPlus,
    LogOut,
    Menu,
    X,
    BookOpen,
    Bell,
    BellOff
} from 'lucide-vue-next';
import { usePushNotifications } from '@/Composables/usePushNotifications';

const props = defineProps({
    user: Object,
});

const isMobileMenuOpen = ref(false);
const { isSubscribed, permission, requestPermission, unsubscribeUser } = usePushNotifications();

const navigation = [
    { name: 'Dashboard', href: route('admin.dashboard'), icon: LayoutDashboard, active: route().current('admin.dashboard') },
    { name: 'Manage Workshops', href: route('admin.workshops.index'), icon: Calendar, active: route().current('admin.workshops.*') },
    { name: 'User Directory', href: route('admin.users.index'), icon: Users, active: route().current('admin.users.*') },
];
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex">
        <!-- Sidebar for Desktop -->
        <aside class="hidden lg:flex lg:flex-shrink-0">
            <div class="flex flex-col w-64">
                <div class="flex flex-col h-0 flex-1 bg-white border-r border-gray-200">
                    <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                        <div class="flex items-center flex-shrink-0 px-6 space-x-3">
                            <div class="bg-indigo-600 p-1.5 rounded-lg">
                                <BookOpen class="h-6 w-6 text-white" />
                            </div>
                            <span class="text-xl font-bold text-gray-900 tracking-tight">InterCourse</span>
                        </div>
                        <nav class="mt-8 flex-1 px-4 space-y-1">
                            <Link
                                v-for="item in navigation"
                                :key="item.name"
                                :href="item.href"
                                :class="[
                                    item.active
                                        ? 'bg-indigo-50 text-indigo-700'
                                        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                                    'group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200'
                                ]"
                            >
                                <component
                                    :is="item.icon"
                                    :class="[
                                        item.active ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500',
                                        'mr-3 flex-shrink-0 h-5 w-5 transition-colors duration-200'
                                    ]"
                                />
                                {{ item.name }}
                            </Link>

                            <!-- Notification Toggle -->
                            <button
                                @click="isSubscribed ? unsubscribeUser() : requestPermission()"
                                :class="[
                                    isSubscribed
                                        ? 'text-emerald-600 hover:bg-emerald-50'
                                        : 'text-gray-600 hover:bg-gray-50',
                                    'w-full group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 mt-4'
                                ]"
                            >
                                <component
                                    :is="isSubscribed ? Bell : BellOff"
                                    :class="[
                                        isSubscribed ? 'text-emerald-500' : 'text-gray-400 group-hover:text-gray-500',
                                        'mr-3 flex-shrink-0 h-5 w-5 transition-colors duration-200'
                                    ]"
                                />
                                {{ isSubscribed ? 'Notifications Active' : 'Enable Notifications' }}
                            </button>
                        </nav>
                    </div>

                    <!-- User Profile & Logout -->
                    <div class="flex-shrink-0 flex flex-col border-t border-gray-200 p-4 space-y-2">
                        <div class="flex items-center px-2 py-2">
                            <img class="h-9 w-9 rounded-full ring-2 ring-white shadow-sm" :src="$page.props.auth.user.photo_url" alt="" />
                            <div class="ml-3">
                                <p class="text-sm font-bold text-gray-700">{{ $page.props.auth.user.name }}</p>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $page.props.auth.user.role }}</p>
                            </div>
                        </div>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="w-full flex items-center px-3 py-2 text-sm font-bold text-gray-500 hover:bg-rose-50 hover:text-rose-600 rounded-xl transition-all duration-200 group"
                        >
                            <LogOut class="mr-3 h-5 w-5 text-gray-400 group-hover:text-rose-500" />
                            Sign Out
                        </Link>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <main class="flex-1 relative overflow-y-auto focus:outline-none">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                        <!-- Optional Header Slot -->
                        <div v-if="$slots.header" class="mb-8">
                            <slot name="header" />
                        </div>

                        <slot />
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
