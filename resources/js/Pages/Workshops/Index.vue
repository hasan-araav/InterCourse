<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import {
    Plus,
    Calendar,
    Clock,
    Users,
    Pencil,
    Trash2,
    Eye,
    ChevronDown,
    ChevronUp,
    Search,
    Filter,
    Camera,
    LayoutGrid,
    CheckCircle2,
    Activity,
    X
} from 'lucide-vue-next';
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    workshops: Object,
    filters: Object,
    stats: Object,
});

const showFormModal = ref(false);
const showDeleteConfirmation = ref(false);
const editingWorkshop = ref(null);
const workshopToDelete = ref(null);
const search = ref(props.filters.search || '');
const photoPreview = ref(null);
const speakerPhotoPreview = ref(null);

const form = useForm({
    title: '',
    description: '',
    starts_at: '',
    duration_minutes: '',
    capacity: '',
    cover_photo: null,
    speaker_name: '',
    speaker_bio: '',
    speaker_photo: null,
    _method: 'POST'
});

const openCreateModal = () => {
    editingWorkshop.value = null;
    form.reset();
    form.clearErrors();
    form._method = 'POST';
    photoPreview.value = null;
    speakerPhotoPreview.value = null;
    showFormModal.value = true;
};

const openEditModal = (workshop) => {
    editingWorkshop.value = workshop;
    // Format date for datetime-local input
    const date = new Date(workshop.starts_at);
    const formattedDate = date.toISOString().slice(0, 16);

    form.title = workshop.title;
    form.description = workshop.description;
    form.starts_at = formattedDate;
    form.duration_minutes = workshop.duration_minutes;
    form.capacity = workshop.capacity;
    form.cover_photo = null;
    form.speaker_name = workshop.speaker_name || '';
    form.speaker_bio = workshop.speaker_bio || '';
    form.speaker_photo = null;
    form._method = 'POST';
    photoPreview.value = workshop.cover_photo;
    speakerPhotoPreview.value = workshop.speaker_photo;
    showFormModal.value = true;
};

const handlePhotoChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.cover_photo = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const handleSpeakerPhotoChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.speaker_photo = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            speakerPhotoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submit = () => {
    if (editingWorkshop.value) {
        router.post(route('admin.workshops.update', editingWorkshop.value.id), {
            _method: 'PATCH',
            ...form.data(),
        }, {
            onSuccess: () => {
                showFormModal.value = false;
                form.reset();
            },
        });
    } else {
        form.post(route('admin.workshops.store'), {
            onSuccess: () => {
                showFormModal.value = false;
                form.reset();
            },
        });
    }
};

const confirmDelete = (workshop) => {
    workshopToDelete.value = workshop;
    showDeleteConfirmation.value = true;
};

const deleteWorkshop = () => {
    if (workshopToDelete.value) {
        router.delete(route('admin.workshops.destroy', workshopToDelete.value.id), {
            onSuccess: () => {
                showDeleteConfirmation.value = false;
                workshopToDelete.value = null;
            },
        });
    }
};

watch(search, debounce((value) => {
    router.get(route('admin.workshops.index'), {
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
    router.get(route('admin.workshops.index'), {
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
    <Head title="Workshop Inventory" />

    <AdminLayout>
        <div class="space-y-8 pb-12">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Workshop Inventory</h1>
                    <p class="mt-2 text-sm text-gray-500 font-medium">Manage and monitor InterCourse Academy events.</p>
                </div>
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white font-bold rounded-2xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100"
                >
                    <Plus class="h-5 w-5 mr-2" />
                    New Workshop
                </button>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center space-x-4">
                    <div class="p-3 bg-indigo-50 rounded-2xl">
                        <LayoutGrid class="h-6 w-6 text-indigo-600" />
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Events</p>
                        <p class="text-2xl font-black text-gray-900">{{ stats.total_workshops }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center space-x-4">
                    <div class="p-3 bg-emerald-50 rounded-2xl">
                        <Calendar class="h-6 w-6 text-emerald-600" />
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Upcoming</p>
                        <p class="text-2xl font-black text-gray-900">{{ stats.upcoming }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center space-x-4">
                    <div class="p-3 bg-amber-50 rounded-2xl">
                        <CheckCircle2 class="h-6 w-6 text-amber-600" />
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Confirmations</p>
                        <p class="text-2xl font-black text-gray-900">{{ stats.total_confirmed }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center space-x-4">
                    <div class="p-3 bg-rose-50 rounded-2xl">
                        <Activity class="h-6 w-6 text-rose-600" />
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Avg. Fill Rate</p>
                        <p class="text-2xl font-black text-gray-900">{{ stats.avg_fill_rate }}%</p>
                    </div>
                </div>
            </div>

            <!-- Table & Search -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Search Bar -->
                <div class="p-6 border-b border-gray-50 flex flex-col md:flex-row gap-4 items-center justify-between bg-gray-50/30">
                    <div class="relative w-full md:w-96">
                        <Search class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" />
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search workshops..."
                            class="w-full pl-12 pr-4 py-3 bg-white border-gray-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm font-medium"
                        />
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-xs font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 bg-gray-50/50">
                                <th class="px-8 py-5 cursor-pointer hover:text-indigo-600 transition-colors" @click="toggleSort('title')">
                                    <div class="flex items-center space-x-2">
                                        <span>Workshop</span>
                                        <component :is="getSortIcon('title')" class="h-3 w-3" />
                                    </div>
                                </th>
                                <th class="px-8 py-5 cursor-pointer hover:text-indigo-600 transition-colors" @click="toggleSort('starts_at')">
                                    <div class="flex items-center space-x-2">
                                        <span>Schedule</span>
                                        <component :is="getSortIcon('starts_at')" class="h-3 w-3" />
                                    </div>
                                </th>
                                <th class="px-8 py-5">Engagement</th>
                                <th class="px-8 py-5">Capacity</th>
                                <th class="px-8 py-5"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-for="workshop in workshops.data" :key="workshop.id" class="group hover:bg-gray-50/80 transition-all">
                                <td class="px-8 py-6">
                                    <div class="flex items-center space-x-4">
                                        <img :src="workshop.cover_photo" class="h-12 w-12 rounded-xl object-cover shadow-sm ring-2 ring-white" />
                                        <div>
                                            <div class="font-black text-gray-900 group-hover:text-indigo-600 transition-colors">{{ workshop.title }}</div>
                                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-0.5">ID: #{{ workshop.id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="space-y-1">
                                        <div class="flex items-center text-sm font-bold text-gray-700">
                                            <Calendar class="h-4 w-4 mr-2 text-gray-300" />
                                            {{ workshop.starts_at }}
                                        </div>
                                        <div class="flex items-center text-xs font-bold text-gray-400 uppercase tracking-widest">
                                            <Clock class="h-3 w-3 mr-2" />
                                            {{ workshop.duration_minutes }} Mins
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center space-x-4">
                                        <div class="text-center">
                                            <div class="text-sm font-black text-gray-900">{{ workshop.confirmed_count }}</div>
                                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Confirmed</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-sm font-black text-gray-600">{{ workshop.waitlist_count }}</div>
                                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Waitlist</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-600 text-[10px] font-black uppercase tracking-widest rounded-full">
                                        <Users class="h-3 w-3 mr-1.5" />
                                        {{ workshop.capacity }} Seats
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <Link
                                            :href="route('admin.workshops.show', workshop.id)"
                                            class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all"
                                            title="View Attendees"
                                        >
                                            <Eye class="h-5 w-5" />
                                        </Link>
                                        <button
                                            @click="openEditModal(workshop)"
                                            class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-xl transition-all"
                                            title="Edit Workshop"
                                        >
                                            <Pencil class="h-5 w-5" />
                                        </button>
                                        <button
                                            @click="confirmDelete(workshop)"
                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all"
                                            title="Delete Workshop"
                                        >
                                            <Trash2 class="h-5 w-5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="workshops.data.length === 0">
                                <td colspan="5" class="px-8 py-24 text-center">
                                    <div class="max-w-xs mx-auto text-gray-400">
                                        <Calendar class="h-12 w-12 mx-auto mb-4 opacity-20" />
                                        <p class="font-black uppercase tracking-widest text-sm">No workshops found</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="workshops.links.length > 3" class="px-8 py-6 bg-gray-50/50 border-t border-gray-50 flex items-center justify-center gap-2">
                    <template v-for="(link, key) in workshops.links" :key="key">
                        <div v-if="link.url === null" class="px-4 py-2 text-xs font-black text-gray-300 uppercase tracking-widest cursor-not-allowed" v-html="link.label" />
                        <Link
                            v-else
                            :href="link.url"
                            class="px-4 py-2 text-xs font-black uppercase tracking-widest rounded-xl transition-all"
                            :class="link.active
                                ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100'
                                : 'text-gray-500 hover:bg-white hover:text-indigo-600'"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>

        <!-- Create/Edit Workshop Modal -->
        <Modal :show="showFormModal" @close="showFormModal = false" maxWidth="2xl">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-indigo-600 rounded-2xl shadow-lg shadow-indigo-100">
                            <component :is="editingWorkshop ? Pencil : Plus" class="h-6 w-6 text-white" />
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-gray-900 tracking-tight">
                                {{ editingWorkshop ? 'Edit Workshop' : 'Launch Workshop' }}
                            </h2>
                            <p class="text-sm text-gray-500 font-medium">
                                {{ editingWorkshop ? 'Update event specifications.' : 'Create a new educational experience.' }}
                            </p>
                        </div>
                    </div>
                    <button @click="showFormModal = false" class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                        <X class="h-6 w-6" />
                    </button>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Cover Photo Upload -->
                    <div class="flex flex-col items-center justify-center space-y-4 mb-6">
                        <div class="relative group w-full max-w-md h-48 rounded-3xl overflow-hidden shadow-md ring-4 ring-gray-50">
                            <img
                                :src="photoPreview"
                                class="w-full h-full object-cover group-hover:opacity-75 transition-all"
                                v-if="photoPreview"
                            />
                            <div v-else class="w-full h-full bg-gray-100 flex items-center justify-center border-2 border-dashed border-gray-300">
                                <Camera class="h-10 w-10 text-gray-300" />
                            </div>
                            <label
                                for="cover_photo"
                                class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity"
                            >
                                <div class="bg-black/50 p-3 rounded-2xl">
                                    <Plus class="h-6 w-6 text-white" />
                                </div>
                            </label>
                            <input
                                type="file"
                                id="cover_photo"
                                class="hidden"
                                @change="handlePhotoChange"
                                accept="image/*"
                            />
                        </div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Click to upload cover photo</p>
                        <InputError class="mt-2" :message="form.errors.cover_photo" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <h3 class="text-sm font-black text-indigo-600 uppercase tracking-widest border-b border-indigo-50 pb-2 mb-4">General Information</h3>
                            <InputLabel for="title" value="Workshop Title" class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2" />
                            <TextInput
                                id="title"
                                type="text"
                                class="mt-1 block w-full bg-gray-50 border-transparent focus:bg-white transition-all rounded-2xl"
                                v-model="form.title"
                                required
                                placeholder="e.g. Advanced Vue.js Masterclass"
                            />
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>

                        <div class="md:col-span-2">
                            <InputLabel for="description" value="Event Description" class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2" />
                            <TextArea
                                id="description"
                                class="mt-1 block w-full bg-gray-50 border-transparent focus:bg-white transition-all rounded-2xl"
                                v-model="form.description"
                                required
                                rows="3"
                                placeholder="What will participants learn?"
                            />
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

                        <!-- Speaker Section -->
                        <div class="md:col-span-2 pt-4">
                            <h3 class="text-sm font-black text-indigo-600 uppercase tracking-widest border-b border-indigo-50 pb-2 mb-4">Speaker Profile</h3>
                            <div class="flex flex-col md:flex-row gap-6 items-start">
                                <div class="flex-shrink-0">
                                    <div class="relative group h-24 w-24 rounded-3xl overflow-hidden shadow-sm ring-4 ring-gray-50">
                                        <img
                                            :src="speakerPhotoPreview"
                                            class="w-full h-full object-cover group-hover:opacity-75 transition-all"
                                            v-if="speakerPhotoPreview"
                                        />
                                        <div v-else class="w-full h-full bg-gray-100 flex items-center justify-center border-2 border-dashed border-gray-200">
                                            <Camera class="h-6 w-6 text-gray-300" />
                                        </div>
                                        <label
                                            for="speaker_photo"
                                            class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity"
                                        >
                                            <div class="bg-black/50 p-1.5 rounded-lg">
                                                <Plus class="h-4 w-4 text-white" />
                                            </div>
                                        </label>
                                        <input
                                            type="file"
                                            id="speaker_photo"
                                            class="hidden"
                                            @change="handleSpeakerPhotoChange"
                                            accept="image/*"
                                        />
                                    </div>
                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest text-center mt-2">Speaker Photo</p>
                                </div>
                                <div class="flex-1 space-y-4 w-full">
                                    <div>
                                        <InputLabel for="speaker_name" value="Speaker Name" class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2" />
                                        <TextInput
                                            id="speaker_name"
                                            type="text"
                                            class="mt-1 block w-full bg-gray-50 border-transparent focus:bg-white transition-all rounded-2xl"
                                            v-model="form.speaker_name"
                                            placeholder="e.g. Jane Smith"
                                        />
                                        <InputError class="mt-2" :message="form.errors.speaker_name" />
                                    </div>
                                    <div>
                                        <InputLabel for="speaker_bio" value="Speaker Bio" class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2" />
                                        <TextArea
                                            id="speaker_bio"
                                            class="mt-1 block w-full bg-gray-50 border-transparent focus:bg-white transition-all rounded-2xl"
                                            v-model="form.speaker_bio"
                                            rows="2"
                                            placeholder="A short introduction of the speaker."
                                        />
                                        <InputError class="mt-2" :message="form.errors.speaker_bio" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-2 pt-4">
                            <h3 class="text-sm font-black text-indigo-600 uppercase tracking-widest border-b border-indigo-50 pb-2 mb-4">Logistics</h3>
                        </div>

                        <div>
                            <InputLabel for="starts_at" value="Starts At" class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2" />
                            <TextInput
                                id="starts_at"
                                type="datetime-local"
                                class="mt-1 block w-full bg-gray-50 border-transparent focus:bg-white transition-all rounded-2xl"
                                v-model="form.starts_at"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.starts_at" />
                        </div>

                        <div>
                            <InputLabel for="duration_minutes" value="Duration (Minutes)" class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2" />
                            <TextInput
                                id="duration_minutes"
                                type="number"
                                class="mt-1 block w-full bg-gray-50 border-transparent focus:bg-white transition-all rounded-2xl"
                                v-model="form.duration_minutes"
                                required
                                placeholder="60"
                            />
                            <InputError class="mt-2" :message="form.errors.duration_minutes" />
                        </div>

                        <div>
                            <InputLabel for="capacity" value="Max Capacity" class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2" />
                            <TextInput
                                id="capacity"
                                type="number"
                                class="mt-1 block w-full bg-gray-50 border-transparent focus:bg-white transition-all rounded-2xl"
                                v-model="form.capacity"
                                required
                                placeholder="20"
                            />
                            <InputError class="mt-2" :message="form.errors.capacity" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-6 space-x-4">
                        <SecondaryButton @click="showFormModal = false" class="rounded-2xl border-2 font-bold px-6 py-3">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton
                            class="rounded-2xl bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-100 font-bold px-8 py-3"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            {{ editingWorkshop ? 'Update Workshop' : 'Launch Workshop' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteConfirmation" @close="showDeleteConfirmation = false" maxWidth="md">
            <div class="p-8 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-6">
                    <Trash2 class="h-8 w-8 text-red-600" />
                </div>
                <h2 class="text-2xl font-black text-gray-900 tracking-tight mb-2">Terminate Workshop</h2>
                <p class="text-sm text-gray-500 font-medium mb-8">
                    Are you sure you want to delete <span class="font-black text-gray-900">{{ workshopToDelete?.title }}</span>?
                    This will remove all registrations and historical data for this event.
                </p>

                <div class="flex items-center justify-center space-x-4">
                    <SecondaryButton @click="showDeleteConfirmation = false" class="rounded-2xl border-2 font-bold px-6 py-3 flex-1">
                        No, Keep It
                    </SecondaryButton>
                    <button
                        @click="deleteWorkshop"
                        class="inline-flex items-center justify-center px-6 py-3 bg-red-600 text-white font-bold rounded-2xl hover:bg-red-700 transition-all shadow-lg shadow-red-100 flex-1"
                    >
                        Yes, Terminate
                    </button>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>
