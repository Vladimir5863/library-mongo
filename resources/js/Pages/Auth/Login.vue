<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import Button from "@/Pages/Components/Button.vue";
import FormField from "@/Pages/Components/FormField.vue";

const showPassword = ref(false);

const form = useForm({
    email: "",
    password: "",
});

const submit = () => {
    form.post(route("login.post"));
};
</script>

<template>
    <div
        class="min-h-screen bg-emerald-600 flex flex-col items-center justify-center px-4 py-12"
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
                Prijavite se
            </h2>
            <p class="text-sm text-emerald-600 text-center mb-6">
                Dobrodošli nazad!<br />Unesite vaše podatke za prijavu
            </p>

            <form @submit.prevent="submit" class="flex flex-col gap-5">
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

                <!-- Greška pri prijavi -->
                <div
                    v-if="form.errors.login"
                    class="text-xs text-red-500 text-center"
                >
                    {{ form.errors.login }}
                </div>

                <!-- Dugme prijavi -->
                <Button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full mt-2"
                >
                    Prijavite se
                </Button>
            </form>

            <!-- Register link -->
            <p class="text-center text-sm text-gray-600 mt-6">
                Nemate nalog?
                <Link
                    :href="route('register')"
                    class="text-gray-800 font-semibold underline underline-offset-2 hover:text-emerald-700"
                >
                    Registrujte se
                </Link>
            </p>
        </div>

        <!-- Otkaži -->
        <Link
            :href="route('home')"
            class="flex items-center gap-2 mt-6 bg-white text-emerald-700 font-medium px-6 py-3 rounded-full hover:bg-gray-50 transition-colors duration-200"
        >
            ← Otkaži
        </Link>
    </div>
</template>
