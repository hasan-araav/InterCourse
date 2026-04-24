<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import {
    Users,
    UserPlus,
    ShieldCheck,
    UserCircle,
    Calendar,
    Search,
    ChevronDown,
    ChevronUp,
    MoreVertical,
    Mail,
    Filter,
    X,
    Plus,
    Pencil,
    Trash2,
    Eye,
    Camera
} from 'lucide-vue-next';
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    users: Object,
    filters: Object,
    stats: Object,
});

const showCreateModal = ref(false);
const showDeleteConfirmation = ref(false);
const editingUser = ref(null);
const userToDelete = ref(null);
const search = ref(props.filters.search || '');
const photoPreview = ref(null);

// Form for creating/editing user
const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'employee',
    photo: null,
    _method: 'POST'
});

const openCreateModal = () => {
    editingUser.value = null;
    form.reset();
    form.clearErrors();
    form._method = 'POST';
    photoPreview.value = null;
    showCreateModal.value = true;
};

const openEditModal = (user) => {
    editingUser.value = user;
    form.name = user.name;
    form.email = user.email;
    form.role = user.role;
    form.password = '';
    form.password_confirmation = '';
    form.photo = null;
    form._method = 'PATCH'; // We use POST with _method=PATCH for multipart/form-data
    photoPreview.value = user.photo;
    showCreateModal.value = true;
};

const handlePhotoChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.photo = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submit = () => {
    if (editingUser.value) {
        // Inertia doesn't support PATCH with files natively via form.patch
        // So we use POST and tell Laravel it's a PATCH
        router.post(route('admin.users.update', editingUser.value.id), {
            _method: 'PATCH',
            ...form.data(),
        }, {
            onSuccess: () => {
                showCreateModal.value = false;
                form.reset();
            },
        });
    } else {
        form.post(route('admin.users.store'), {
            onSuccess: () => {
                showCreateModal.value = false;
                form.reset();
            },
        });
    }
};

const deleteUser = (user) => {
    userToDelete.value = user;
    showDeleteConfirmation.value = true;
};

const confirmDelete = () => {
    if (userToDelete.value) {
        router.delete(route('admin.users.destroy', userToDelete.value.id), {
            onSuccess: () => {
                showDeleteConfirmation.value = false;
                userToDelete.value = null;
            },
        });
    }
};

// Search with debounce
watch(search, debounce((value) => {
    router.get(route('admin.users.index'), {
        search: value,
        sort: props.filters.sort,
        direction: props.filters.direction
    }, {
        preserveState: true,
        replace: true
    });
}, 300));

const toggleSort = (field) => {
    const direction = props.filters.sort === field && props.filters.direction === 'asc' ? 'desc' : 'asc';
    router.get(route('admin.users.index'), {
        search: search.value,
        sort: field,
        direction: direction
    }, {
        preserveState: true,
        replace: true
    });
};

const getSortIcon = (field) => {
    if (props.filters.sort !== field) return Filter;
    return props.filters.direction === 'asc' ? ChevronUp : ChevronDown;
};
</script>

<template>
    <Head title="User Directory" />

    <AdminLayout>
        <div class="space-y-8 pb-12">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">User Directory</h1>
                    <p class="mt-2 text-sm text-gray-500 font-medium">Manage organization members and their access levels.</p>
                </div>
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white font-bold rounded-2xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200"
                >
                    <UserPlus class="h-5 w-5 mr-2" />
                    Add New User
                </button>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center space-x-4">
                    <div class="p-3 bg-indigo-50 rounded-2xl">
                        <Users class="h-6 w-6 text-indigo-600" />
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Users</p>
                        <p class="text-2xl font-black text-gray-900">{{ stats.total_users }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center space-x-4">
                    <div class="p-3 bg-emerald-50 rounded-2xl">
                        <ShieldCheck class="h-6 w-6 text-emerald-600" />
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Administrators</p>
                        <p class="text-2xl font-black text-gray-900">{{ stats.admins }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center space-x-4">
                    <div class="p-3 bg-amber-50 rounded-2xl">
                        <UserCircle class="h-6 w-6 text-amber-600" />
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Employees</p>
                        <p class="text-2xl font-black text-gray-900">{{ stats.employees }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center space-x-4">
                    <div class="p-3 bg-rose-50 rounded-2xl">
                        <Calendar class="h-6 w-6 text-rose-600" />
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Joined Monthly</p>
                        <p class="text-2xl font-black text-gray-900">{{ stats.new_this_month }}</p>
                    </div>
                </div>
            </div>

            <!-- Table & Search -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Search & Filters Bar -->
                <div class="p-6 border-b border-gray-50 flex flex-col md:flex-row gap-4 items-center justify-between bg-gray-50/30">
                    <div class="relative w-full md:w-96">
                        <Search class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search by name or email..."
                            class="w-full pl-12 pr-4 py-3 bg-white border-gray-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm font-medium"
                        />
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 bg-gray-50/50">
                                <th class="px-8 py-5 cursor-pointer hover:text-indigo-600 transition-colors" @click="toggleSort('name')">
                                    <div class="flex items-center space-x-2">
                                        <span>User</span>
                                        <component :is="getSortIcon('name')" class="h-3 w-3" />
                                    </div>
                                </th>
                                <th class="px-8 py-5">Role</th>
                                <th class="px-8 py-5 cursor-pointer hover:text-indigo-600 transition-colors" @click="toggleSort('created_at')">
                                    <div class="flex items-center space-x-2">
                                        <span>Joined Date</span>
                                        <component :is="getSortIcon('created_at')" class="h-3 w-3" />
                                    </div>
                                </th>
                                <th class="px-8 py-5"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-for="user in users.data" :key="user.id" class="group hover:bg-gray-50/80 transition-all duration-200">
                                <td class="px-8 py-6">
                                    <div class="flex items-center space-x-4">
                                        <img :src="user.photo" class="h-10 w-10 rounded-xl shadow-sm ring-2 ring-white" />
                                        <div>
                                            <div class="font-black text-gray-900">{{ user.name }}</div>
                                            <div class="flex items-center text-xs text-gray-400 font-bold mt-0.5">
                                                <Mail class="h-3 w-3 mr-1" />
                                                {{ user.email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span
                                        :class="[
                                            user.role === 'admin' ? 'bg-emerald-50 text-emerald-700' : 'bg-indigo-50 text-indigo-700',
                                            'px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-full border',
                                            user.role === 'admin' ? 'border-emerald-100' : 'border-indigo-100'
                                        ]"
                                    >
                                        {{ user.role }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="text-sm font-bold text-gray-600">{{ user.created_at }}</div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <Link
                                            :href="route('admin.users.show', user.id)"
                                            class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all"
                                            title="View Details"
                                        >
                                            <Eye class="h-5 w-5" />
                                        </Link>
                                        <button
                                            @click="openEditModal(user)"
                                            class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-xl transition-all"
                                            title="Edit User"
                                        >
                                            <Pencil class="h-5 w-5" />
                                        </button>
                                        <button
                                            @click="deleteUser(user)"
                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all"
                                            title="Delete User"
                                        >
                                            <Trash2 class="h-5 w-5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="users.data.length === 0">
                                <td colspan="4" class="px-8 py-24 text-center">
                                    <div class="max-w-xs mx-auto">
                                        <div class="bg-gray-50 p-6 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6">
                                            <Users class="h-10 w-10 text-gray-300" />
                                        </div>
                                        <h3 class="text-lg font-black text-gray-900">No Users Found</h3>
                                        <p class="text-sm text-gray-400 font-bold mt-2">Try adjusting your search criteria.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="users.links.length > 3" class="px-8 py-6 bg-gray-50/50 border-t border-gray-50 flex items-center justify-center gap-2">
                    <template v-for="(link, key) in users.links" :key="key">
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

        <!-- Create/Edit User Modal -->
        <Modal :show="showCreateModal" @close="showCreateModal = false" maxWidth="lg">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-indigo-600 rounded-2xl shadow-lg shadow-indigo-100">
                            <component :is="editingUser ? Pencil : UserPlus" class="h-6 w-6 text-white" />
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-gray-900 tracking-tight">
                                {{ editingUser ? 'Edit User' : 'Onboard User' }}
                            </h2>
                            <p class="text-sm text-gray-500 font-medium">
                                {{ editingUser ? 'Update account information.' : 'Create a new organization account.' }}
                            </p>
                        </div>
                    </div>
                    <button @click="showCreateModal = false" class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                        <X class="h-6 w-6" />
                    </button>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Photo Upload -->
                    <div class="flex flex-col items-center justify-center space-y-4 mb-6">
                        <div class="relative group">
                            <img
                                :src="photoPreview"
                                class="h-24 w-24 rounded-3xl object-cover ring-4 ring-gray-50 shadow-md group-hover:opacity-75 transition-all"
                                v-if="photoPreview"
                            />
                            <div v-else class="h-24 w-24 rounded-3xl bg-gray-100 flex items-center justify-center border-2 border-dashed border-gray-300">
                                <Camera class="h-8 w-8 text-gray-300" />
                            </div>
                            <label
                                for="photo"
                                class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity"
                            >
                                <div class="bg-black/50 p-2 rounded-full">
                                    <Plus class="h-5 w-5 text-white" />
                                </div>
                            </label>
                            <input
                                type="file"
                                id="photo"
                                class="hidden"
                                @change="handlePhotoChange"
                                accept="image/*"
                            />
                        </div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Click to upload photo</p>
                        <InputError class="mt-2" :message="form.errors.photo" />
                    </div>

                    <div>
                        <InputLabel for="name" value="Full Name" class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2" />
                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full bg-gray-50 border-transparent focus:bg-white transition-all rounded-2xl"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="e.g. John Doe"
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div>
                        <InputLabel for="email" value="Email Address" class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full bg-gray-50 border-transparent focus:bg-white transition-all rounded-2xl"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="john@example.com"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="role" value="Access Level" class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2" />
                            <select
                                id="role"
                                class="mt-1 block w-full bg-gray-50 border-transparent focus:bg-white focus:ring-indigo-500 focus:border-indigo-500 transition-all rounded-2xl text-sm font-bold text-gray-700"
                                v-model="form.role"
                                required
                            >
                                <option value="employee">Employee</option>
                                <option value="admin">Administrator</option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.role" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="password" :value="editingUser ? 'New Password (Optional)' : 'Password'" class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2" />
                            <TextInput
                                id="password"
                                type="password"
                                class="mt-1 block w-full bg-gray-50 border-transparent focus:bg-white transition-all rounded-2xl"
                                v-model="form.password"
                                :required="!editingUser"
                                autocomplete="new-password"
                            />
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div>
                            <InputLabel for="password_confirmation" value="Confirm Password" class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2" />
                            <TextInput
                                id="password_confirmation"
                                type="password"
                                class="mt-1 block w-full bg-gray-50 border-transparent focus:bg-white transition-all rounded-2xl"
                                v-model="form.password_confirmation"
                                :required="!editingUser"
                                autocomplete="new-password"
                            />
                            <InputError class="mt-2" :message="form.errors.password_confirmation" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-6 space-x-4">
                        <SecondaryButton @click="showCreateModal = false" class="rounded-2xl border-2 font-bold px-6 py-3">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton
                            class="rounded-2xl bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-100 font-bold px-8 py-3"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            {{ editingUser ? 'Update User' : 'Create Account' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete User Confirmation Modal -->
        <Modal :show="showDeleteConfirmation" @close="showDeleteConfirmation = false" maxWidth="md">
            <div class="p-8 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
                    <Trash2 class="h-8 w-8 text-red-600" />
                </div>
                <h2 class="text-2xl font-black text-gray-900 tracking-tight mb-2">Confirm Deletion</h2>
                <p class="text-sm text-gray-500 font-medium mb-8">
                    Are you sure you want to delete <span class="font-black text-gray-900">{{ userToDelete?.name }}</span>?
                    This action is permanent and will remove all their data from the InterCourse Academy.
                </p>

                <div class="flex items-center justify-center space-x-4">
                    <SecondaryButton @click="showDeleteConfirmation = false" class="rounded-2xl border-2 font-bold px-6 py-3 flex-1">
                        No, Keep User
                    </SecondaryButton>
                    <button
                        @click="confirmDelete"
                        class="inline-flex items-center justify-center px-6 py-3 bg-red-600 text-white font-bold rounded-2xl hover:bg-red-700 transition-all shadow-lg shadow-red-100 flex-1"
                    >
                        Yes, Delete
                    </button>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>
