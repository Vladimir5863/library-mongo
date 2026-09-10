<template>
    <div class="min-h-screen bg-gray-50 px-6 py-8">
        <div class="max-w-4xl mx-auto flex flex-col gap-8">
            <h1 class="text-2xl font-serif font-semibold text-gray-800">
                Moj profil
            </h1>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Leva kolona - avatar i osnovne info -->
                <div class="flex flex-col gap-4">
                    <!-- Avatar -->
                    <div
                        class="bg-white rounded-2xl shadow-sm p-6 flex flex-col items-center gap-4"
                    >
                        <div
                            class="w-24 h-24 rounded-full overflow-hidden border-4 border-emerald-100 cursor-pointer relative group"
                            @click="$refs.avatarInput.click()"
                        >
                            <img
                                v-if="page.props.auth.user?.avatar"
                                :src="page.props.auth.user.avatar"
                                class="w-full h-full object-cover"
                            />
                            <div
                                v-else
                                class="w-full h-full bg-emerald-100 flex items-center justify-center text-3xl font-bold text-emerald-700"
                            >
                                {{ page.props.auth.user?.name[0]
                                }}{{ page.props.auth.user?.surname[0] }}
                            </div>
                            <div
                                class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                            >
                                <span class="text-white text-xs">Promeni</span>
                            </div>
                        </div>
                        <input
                            ref="avatarInput"
                            type="file"
                            accept="image/*"
                            class="hidden"
                            @change="onAvatarChange"
                        />

                        <div class="text-center">
                            <p class="font-semibold text-gray-800">
                                {{ user.name }} {{ user.surname }}
                            </p>
                            <span
                                class="text-xs px-2 py-1 rounded-full font-medium mt-1 inline-block"
                                :class="{
                                    'bg-purple-100 text-purple-700':
                                        user.userType === 'admin',
                                    'bg-blue-100 text-blue-700':
                                        user.userType === 'librarian',
                                    'bg-orange-100 text-orange-700':
                                        user.userType === 'postman',
                                    'bg-emerald-100 text-emerald-700':
                                        user.userType === 'user',
                                }"
                            >
                                {{ roleLabels[user.userType] }}
                            </span>
                        </div>

                        <button
                            v-if="avatarChanged"
                            @click="submitAvatar"
                            class="w-full bg-emerald-700 hover:bg-emerald-600 text-white text-sm font-medium py-2 rounded-lg transition-colors"
                        >
                            Sačuvaj avatar
                        </button>
                    </div>

                    <!-- Statistike -->
                    <div
                        class="bg-white rounded-2xl shadow-sm p-6 flex flex-col gap-3"
                    >
                        <h3 class="text-sm font-semibold text-gray-700">
                            Statistike
                        </h3>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500"
                                >Dostupne pozajmice</span
                            >
                            <span class="font-medium text-emerald-700">{{
                                user.numberOfLoans
                            }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Ukupno pozajmica</span>
                            <span class="font-medium text-gray-800">{{
                                stats.totalLoans
                            }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Ukupno kupovina</span>
                            <span class="font-medium text-gray-800">{{
                                stats.totalSells
                            }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Pretplata</span>
                            <span
                                class="font-medium"
                                :class="
                                    stats.hasSubscription
                                        ? 'text-emerald-700'
                                        : 'text-red-500'
                                "
                            >
                                {{
                                    stats.hasSubscription
                                        ? "Aktivna"
                                        : "Neaktivna"
                                }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Desna kolona - forme -->
                <div class="md:col-span-2 flex flex-col gap-4">
                    <!-- Osnovne informacije -->
                    <div
                        class="bg-white rounded-2xl shadow-sm p-6 flex flex-col gap-4"
                    >
                        <h3 class="text-lg font-semibold text-gray-800">
                            Osnovne informacije
                        </h3>

                        <form
                            @submit.prevent="submitInfo"
                            class="flex flex-col gap-4"
                        >
                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex flex-col gap-1">
                                    <label
                                        class="text-sm font-medium text-gray-700"
                                        >Ime</label
                                    >
                                    <input
                                        v-model="infoForm.name"
                                        type="text"
                                        class="border border-gray-200 rounded-lg px-4 py-2 text-sm outline-none focus:border-emerald-500"
                                    />
                                    <span
                                        v-if="infoForm.errors.name"
                                        class="text-xs text-red-500"
                                        >{{ infoForm.errors.name }}</span
                                    >
                                </div>
                                <div class="flex flex-col gap-1">
                                    <label
                                        class="text-sm font-medium text-gray-700"
                                        >Prezime</label
                                    >
                                    <input
                                        v-model="infoForm.surname"
                                        type="text"
                                        class="border border-gray-200 rounded-lg px-4 py-2 text-sm outline-none focus:border-emerald-500"
                                    />
                                    <span
                                        v-if="infoForm.errors.surname"
                                        class="text-xs text-red-500"
                                        >{{ infoForm.errors.surname }}</span
                                    >
                                </div>
                            </div>

                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-700"
                                    >Email</label
                                >
                                <input
                                    v-model="infoForm.email"
                                    type="email"
                                    class="border border-gray-200 rounded-lg px-4 py-2 text-sm outline-none focus:border-emerald-500"
                                />
                                <span
                                    v-if="infoForm.errors.email"
                                    class="text-xs text-red-500"
                                    >{{ infoForm.errors.email }}</span
                                >
                            </div>

                            <button
                                type="submit"
                                :disabled="infoForm.processing"
                                class="self-end bg-emerald-700 hover:bg-emerald-600 disabled:opacity-50 text-white text-sm font-medium px-6 py-2 rounded-lg transition-colors"
                            >
                                Sačuvaj izmene
                            </button>
                        </form>
                    </div>

                    <!-- Promena lozinke -->
                    <div
                        class="bg-white rounded-2xl shadow-sm p-6 flex flex-col gap-4"
                    >
                        <h3 class="text-lg font-semibold text-gray-800">
                            Promena lozinke
                        </h3>

                        <form
                            @submit.prevent="submitPassword"
                            class="flex flex-col gap-4"
                        >
                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-700"
                                    >Trenutna lozinka</label
                                >
                                <input
                                    v-model="passwordForm.current_password"
                                    type="password"
                                    class="border border-gray-200 rounded-lg px-4 py-2 text-sm outline-none focus:border-emerald-500"
                                />
                                <span
                                    v-if="passwordForm.errors.current_password"
                                    class="text-xs text-red-500"
                                    >{{
                                        passwordForm.errors.current_password
                                    }}</span
                                >
                            </div>

                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-700"
                                    >Nova lozinka</label
                                >
                                <input
                                    v-model="passwordForm.password"
                                    type="password"
                                    class="border border-gray-200 rounded-lg px-4 py-2 text-sm outline-none focus:border-emerald-500"
                                />
                                <span
                                    v-if="passwordForm.errors.password"
                                    class="text-xs text-red-500"
                                    >{{ passwordForm.errors.password }}</span
                                >
                            </div>

                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-700"
                                    >Potvrda nove lozinke</label
                                >
                                <input
                                    v-model="passwordForm.password_confirmation"
                                    type="password"
                                    class="border border-gray-200 rounded-lg px-4 py-2 text-sm outline-none focus:border-emerald-500"
                                />
                            </div>

                            <button
                                type="submit"
                                :disabled="passwordForm.processing"
                                class="self-end bg-emerald-700 hover:bg-emerald-600 disabled:opacity-50 text-white text-sm font-medium px-6 py-2 rounded-lg transition-colors"
                            >
                                Promeni lozinku
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";

const page = usePage();

const props = defineProps({
    user: Object,
    stats: Object,
});

const roleLabels = {
    admin: "Admin",
    librarian: "Bibliotekar",
    postman: "Poštar",
    user: "Korisnik",
};

const avatarChanged = ref(false);
const avatarForm = useForm({ avatar: null });

const onAvatarChange = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    avatarForm.avatar = file;
    avatarChanged.value = true;
};

const submitAvatar = () => {
    avatarForm.post(route("profile.avatar"), {
        forceFormData: true,
        onSuccess: () => (avatarChanged.value = false),
    });
};

const infoForm = useForm({
    name: props.user.name,
    surname: props.user.surname,
    email: props.user.email,
});

const submitInfo = () => {
    infoForm.patch(route("profile.update"));
};

const passwordForm = useForm({
    current_password: "",
    password: "",
    password_confirmation: "",
});

const submitPassword = () => {
    passwordForm.patch(route("profile.password"), {
        onSuccess: () => passwordForm.reset(),
    });
};
</script>
