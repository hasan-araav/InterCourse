<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { 
    Plus, 
    Calendar, 
    Clock, 
    Users, 
    Pencil, 
    Trash2, 
    Eye,
    ChevronLeft,
    ChevronRight,
    Search
} from 'lucide-vue-next';

const props = defineProps({
    workshops: Object,
});

const deleteWorkshop = (id) => {
    if (confirm('Are you sure you want to delete this workshop? This will remove all registrations.')) {
        router.delete(route('admin.workshops.destroy', id));
    }
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString([], { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <Head title="Manage Workshops" />

    <AdminLayout>
        <div class="space-y-8 pb-12">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Workshop Inventory</h1>
                    <p class="mt-2 text-sm text-gray-500 font-medium">Create and manage your educational events.</p>
                </div>
                <Link 
                    :href="route('admin.workshops.create')" 
                    class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white font-bold rounded-2xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200"
                >
                    <Plus class="h-5 w-5 mr-2" />
                    New Workshop
                </Link>
            </div>

            <!-- Flash Message -->
            <div v-if="$page.props.flash?.message" class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center text-emerald-700 font-bold text-sm animate-in fade-in slide-in-from-top-4 duration-500">
                <div class="bg-emerald-100 p-1 rounded-lg mr-3">
                    <Plus class="h-4 w-4" />
                </div>
                {{ $page.props.flash.message }}
            </div>

            <!-- Workshops Table Card -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 bg-gray-50/50">
                                <th class="px-8 py-5">Event Details</th>
                                <th class="px-8 py-5">Schedule</th>
                                <th class="px-8 py-5">Capacity</th>
                                <th class="px-8 py-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-for="workshop in workshops.data" :key="workshop.id" class="group hover:bg-gray-50/80 transition-all duration-200">
                                <td class="px-8 py-6">
                                    <div class="font-black text-gray-900 group-hover:text-indigo-600 transition-colors">{{ workshop.title }}</div>
                                    <div class="text-xs text-gray-400 font-bold mt-1 line-clamp-1 max-w-xs">{{ workshop.description }}</div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="space-y-1">
                                        <div class="flex items-center text-sm font-bold text-gray-700">
                                            <Calendar class="h-4 w-4 mr-2 text-gray-400" />
                                            {{ formatDate(workshop.starts_at) }}
                                        </div>
                                        <div class="flex items-center text-xs font-bold text-gray-400 uppercase tracking-widest">
                                            <Clock class="h-3 w-3 mr-2" />
                                            {{ workshop.duration_minutes }} Mins
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-600 text-xs font-black rounded-full">
                                        <Users class="h-3 w-3 mr-1.5" />
                                        {{ workshop.capacity }} Seats
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center justify-end space-x-2">
                                        <Link 
                                            :href="route('admin.workshops.show', workshop.id)" 
                                            class="p-2.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all"
                                            title="View Attendees"
                                        >
                                            <Eye class="h-5 w-5" />
                                        </Link>
                                        <Link 
                                            :href="route('admin.workshops.edit', workshop.id)" 
                                            class="p-2.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-xl transition-all"
                                            title="Edit"
                                        >
                                            <Pencil class="h-5 w-5" />
                                        </Link>
                                        <button 
                                            @click="deleteWorkshop(workshop.id)" 
                                            class="p-2.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all"
                                            title="Delete"
                                        >
                                            <Trash2 class="h-5 w-5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="workshops.data.length === 0">
                                <td colspan="4" class="px-8 py-24 text-center">
                                    <div class="max-w-xs mx-auto">
                                        <div class="bg-gray-50 p-6 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6">
                                            <Calendar class="h-10 w-10 text-gray-300" />
                                        </div>
                                        <h3 class="text-lg font-black text-gray-900">No Workshops Yet</h3>
                                        <p class="text-sm text-gray-400 font-bold mt-2">Get started by creating your first educational event.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Professional Pagination -->
                <div v-if="workshops.links.length > 3" class="px-8 py-6 bg-gray-50/50 border-t border-gray-50 flex items-center justify-center gap-2">
                    <template v-for="(link, key) in workshops.links" :key="key">
                        <div v-if="link.url === null" class="px-4 py-2 text-xs font-black text-gray-300 uppercase tracking-widest cursor-not-allowed" v-html="link.label" />
                        <Link
                            v-else
                            :href="link.url"
                            class="px-4 py-2 text-xs font-black uppercase tracking-widest rounded-xl transition-all duration-200"
                            :class="link.active 
                                ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' 
                                : 'text-gray-500 hover:bg-white hover:text-indigo-600 hover:shadow-sm'"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
