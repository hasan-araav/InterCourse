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
    X
} from 'lucide-vue-next';

const props = defineProps({
    user: Object,
});

const isMobileMenuOpen = ref(false);

const navigation = [
    { name: 'Dashboard', href: route('admin.dashboard'), icon: LayoutDashboard, active: route().current('admin.dashboard') },
    { name: 'Manage Workshops', href: route('admin.workshops.index'), icon: Calendar, active: route().current('admin.workshops.*') },
    { name: 'User Directory', href: route('admin.users.index'), icon: Users, active: route().current('admin.users.index') },
];

const logout = () => {
    // Standard logout logic if needed, but we'll stick to the layout structure
};
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
                                <Users class="h-6 w-6 text-white" />
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
                        </nav>
                    </div>
                    <div class="flex-shrink-0 flex border-t border-gray-200 p-4">
                        <div class="flex-shrink-0 w-full group block">
                            <div class="flex items-center">
                                <div>
                                    <img class="inline-block h-9 w-9 rounded-full ring-2 ring-white" :src="`https://ui-avatars.com/api/?name=${$page.props.auth.user.name}&background=6366f1&color=fff`" alt="" />
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-700 group-hover:text-gray-900">{{ $page.props.auth.user.name }}</p>
                                    <p class="text-xs font-medium text-gray-500 group-hover:text-gray-700 uppercase tracking-wider">{{ $page.props.auth.user.role }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <main class="flex-1 relative overflow-y-auto focus:outline-none">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                        <slot />
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
