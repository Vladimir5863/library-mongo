<template>
    <div class="min-h-screen bg-gray-50 px-6 py-8">
        <div class="max-w-6xl mx-auto flex flex-col gap-6">
            <div class="flex items-center gap-4">
                <Link
                    :href="route('admin.index')"
                    class="text-sm text-emerald-700 hover:underline"
                    >← Nazad</Link
                >
                <h1
                    class="text-2xl font-serif font-semibold text-gray-800 tracking-tight"
                >
                    Korisnici
                </h1>
            </div>

            <input
                v-model="search"
                type="text"
                placeholder="Pretraži korisnike..."
                class="border border-gray-200 rounded-lg px-4 py-2 text-sm outline-none max-w-sm"
            />

            <div class="bg-white rounded-2xl shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-max w-full">
                    <thead
                        class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider"
                    >
                        <tr>
                            <th class="px-6 py-3 text-left">Korisnik</th>
                            <th class="px-6 py-3 text-left">Email</th>
                            <th class="px-6 py-3 text-left">Uloga</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Akcije</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr
                            v-for="user in filteredUsers"
                            :key="user.userId"
                            class="hover:bg-gray-50"
                        >
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-xs font-medium text-emerald-700"
                                    >
                                        {{ user.name[0] }}{{ user.surname[0] }}
                                    </div>
                                    <p
                                        class="text-sm font-medium text-gray-800"
                                    >
                                        {{ user.name }} {{ user.surname }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ user.email }}
                            </td>
                            <td class="px-6 py-4">
                                <select
                                    :value="user.userType"
                                    @change="
                                        changeRole(
                                            user.userId,
                                            $event.target.value,
                                        )
                                    "
                                    class="text-xs border border-gray-200 rounded-lg px-2 py-1 outline-none"
                                >
                                    <option value="user">Korisnik</option>
                                    <option value="librarian">
                                        Bibliotekar
                                    </option>
                                    <option value="postman">Poštar</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="text-xs px-2 py-1 rounded-full font-medium"
                                    :class="
                                        user.deleted_at
                                            ? 'bg-red-100 text-red-600'
                                            : 'bg-emerald-100 text-emerald-700'
                                    "
                                >
                                    {{
                                        user.deleted_at ? "Banovan" : "Aktivan"
                                    }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <button
                                    v-if="!user.deleted_at"
                                    @click="banUser(user.userId)"
                                    class="text-xs text-red-500 hover:text-red-400 transition-colors"
                                >
                                    Banuj
                                </button>
                                <button
                                    v-else
                                    @click="unbanUser(user.userId)"
                                    class="text-xs text-emerald-600 hover:text-emerald-500 transition-colors"
                                >
                                    Odbanuj
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>

            <Pagination :data="users" />
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import Pagination from "@/Pages/Components/Pagination.vue";

const props = defineProps({
    users: Object,
});

const search = ref("");

const filteredUsers = computed(() => {
    if (!search.value) return props.users.data;
    const q = search.value.toLowerCase();
    return props.users.data.filter(
        (u) =>
            u.name.toLowerCase().includes(q) ||
            u.surname.toLowerCase().includes(q) ||
            u.email.toLowerCase().includes(q),
    );
});

const banForm = useForm({});

const banUser = (id) => {
    if (confirm("Da li ste sigurni da želite da banujete ovog korisnika?")) {
        banForm.delete(route("admin.users.ban", id));
    }
};

const unbanUser = (id) => {
    banForm.post(route("admin.users.unban", id));
};

const roleForm = useForm({ userType: "" });

const changeRole = (id, role) => {
    roleForm.userType = role;
    roleForm.patch(route("admin.users.role", id));
};
</script>
