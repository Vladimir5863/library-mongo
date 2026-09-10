<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import Button from "@/Pages/Components/Button.vue";
import FormField from "@/Pages/Components/FormField.vue";

const showPassword = ref(false);
const avatarPreview = ref(null);

const form = useForm({
    name: "",
    surname: "",
    email: "",
    password: "",
    password_confirmation: "",
    avatar: null,
});

const submit = () => {
    form.post(
        route("register", {
            forceFormData: true,
            onError: () => form.reset("password", "password_confirmation"),
        }),
    );
};

const change = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    form.avatar = file;
    avatarPreview.value = URL.createObjectURL(file);
};
</script>

<template>
    <div
        class="min-h-screen bg-emerald-800 flex flex-col items-center justify-center px-4 py-12"
    >
        <!-- Logo -->
        <div class="flex items-center gap-3 mb-6">
            <img
                :src="'/storage/logo.png'"
                alt="logo"
                class="h-14 w-14 object-contain rounded-full"
            />
            <span class="text-2xl font-bold font-serif text-white"
                >Biblioteka Drvce</span
            >
        </div>

        <!-- Kartica -->
        <div
            class="bg-gray-200 rounded-2xl shadow-md w-full max-w-md px-8 py-10"
        >
            <!-- Naslov -->
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-1">
                Napravite nalog
            </h2>
            <p class="text-sm text-emerald-600 text-center mb-6">
                Pridruži te se biblioteci drvence i<br />započnite pozamljivati
                knjige
            </p>

            <!-- Form -->
            <form @submit.prevent="submit" class="flex flex-col gap-5">
                <!-- Ime i prezime -->
                <!-- Avatar -->
                <div class="flex flex-col items-center gap-2">
                    <div
                        class="h-24 w-24 rounded-full border-4 border-white shadow-md overflow-hidden bg-gray-300 flex items-center justify-center cursor-pointer"
                        @click="$refs.avatarInput.click()"
                    >
                        <img
                            v-if="avatarPreview"
                            :src="avatarPreview"
                            class="h-full w-full object-cover"
                        />
                        <span v-else class="text-4xl text-gray-400">👤</span>
                    </div>
                    <button
                        type="button"
                        @click="$refs.avatarInput.click()"
                        class="text-xs text-emerald-700 font-medium hover:underline"
                    >
                        Dodaj sliku profila
                    </button>
                    <input
                        ref="avatarInput"
                        type="file"
                        accept="image/*"
                        class="hidden"
                        @change="change"
                    />
                    <span
                        v-if="form.errors.avatar"
                        class="text-xs text-red-500"
                        >{{ form.errors.avatar }}</span
                    >
                </div>

                <!-- Ime -->
                <FormField label="Ime" :error="form.errors.name">
                    <div
                        class="flex items-center gap-3 bg-white border border-gray-300 rounded-lg px-4 py-3"
                    >
                        <span class="text-gray-400 text-lg">👤</span>
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="Petar"
                            class="flex-1 outline-none text-sm text-gray-700 bg-transparent"
                        />
                    </div>
                </FormField>

                <!-- Prezime -->
                <FormField label="Prezime" :error="form.errors.surname">
                    <div
                        class="flex items-center gap-3 bg-white border border-gray-300 rounded-lg px-4 py-3"
                    >
                        <span class="text-gray-400 text-lg">👤</span>
                        <input
                            v-model="form.surname"
                            type="text"
                            placeholder="Petrović"
                            class="flex-1 outline-none text-sm text-gray-700 bg-transparent"
                        />
                    </div>
                </FormField>

                <!-- Email -->
                <FormField label="Email" :error="form.errors.email">
                    <div
                        class="flex items-center gap-3 bg-white border border-gray-300 rounded-lg px-4 py-3"
                    >
                        <span class="text-gray-400 text-lg">✉️</span>
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="primer@gmail.com"
                            class="flex-1 outline-none text-sm text-gray-700 bg-transparent"
                        />
                    </div>
                </FormField>

                <!-- Šifra -->
                <FormField
                    label="Šifra"
                    :error="form.errors.password"
                >
                    <div
                        class="flex items-center gap-3 bg-white border border-gray-300 rounded-lg px-4 py-3"
                    >
                        <span class="text-gray-400 text-lg">🔑</span>
                        <input
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="********"
                            class="flex-1 outline-none text-sm text-gray-700 bg-transparent"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="text-gray-400 hover:text-gray-600"
                        >
                            <span v-if="showPassword">🙈</span>
                            <span v-else>👁️</span>
                        </button>
                    </div>
                </FormField>

                <!-- Potvrda šifre -->
                <FormField
                    label="Potvrda šifre"
                    :error="form.errors.password_confirmation"
                >
                    <div
                        class="flex items-center gap-3 bg-white border border-gray-300 rounded-lg px-4 py-3"
                    >
                        <span class="text-gray-400 text-lg">🔑</span>
                        <input
                            v-model="form.password_confirmation"
                            type="password"
                            placeholder="********"
                            class="flex-1 outline-none text-sm text-gray-700 bg-transparent"
                        />
                    </div>
                </FormField>

                <!-- Dugme registruj -->
                <Button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full mt-2"
                >
                    Registruj se
                </Button>
            </form>

            <!-- Login link -->
            <p class="text-center text-sm text-gray-600 mt-6">
                Vec imate nalog?
                <Link
                    :href="route('login')"
                    class="text-gray-800 font-semibold underline underline-offset-2 hover:text-emerald-700"
                >
                    Prijavite se
                </Link>
            </p>
        </div>

        <!-- Otkaži -->
        <Link
            :href="route('home')"
            class="flex items-center gap-2 mt-6 bg-emerald-700 hover:bg-emerald-600 text-white font-medium px-6 py-3 rounded-full transition-colors duration-200"
        >
            ← Otkaži
        </Link>
    </div>
</template>
