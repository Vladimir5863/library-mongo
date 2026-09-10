<template>
    <div
        class="min-h-screen bg-gray-50 flex flex-col items-center justify-center px-6 py-12"
    >
        <div
            class="bg-white rounded-2xl shadow-md w-full max-w-2xl p-8 flex flex-col gap-6"
        >
            <h2 class="text-2xl font-serif font-semibold text-gray-800">
                Nova vest
            </h2>

            <form @submit.prevent="submit" class="flex flex-col gap-5">
                <!-- Tip -->
                <FormField label="Tip" :error="form.errors.type">
                    <select
                        v-model="form.type"
                        class="border border-gray-200 rounded-lg px-4 py-3 text-sm outline-none"
                    >
                        <option value="">Izaberi tip</option>
                        <option value="announcement">Obaveštenje</option>
                        <option value="event">Događaj</option>
                        <option value="promotion">Promocija</option>
                    </select>
                </FormField>

                <!-- Naslov -->
                <FormField label="Naslov" :error="form.errors.title">
                    <input
                        v-model="form.title"
                        type="text"
                        placeholder="Naslov vesti"
                        class="border border-gray-200 rounded-lg px-4 py-3 text-sm outline-none"
                    />
                </FormField>

                <!-- Tekst -->
                <FormField label="Tekst" :error="form.errors.text">
                    <textarea
                        v-model="form.text"
                        rows="5"
                        placeholder="Tekst vesti..."
                        class="border border-gray-200 rounded-lg px-4 py-3 text-sm outline-none resize-none"
                    />
                </FormField>

                <!-- Datumi -->
                <div class="grid grid-cols-2 gap-4">
                    <FormField
                        label="Datum početka"
                        :error="form.errors.startDate"
                    >
                        <input
                            v-model="form.startDate"
                            type="date"
                            class="border border-gray-200 rounded-lg px-4 py-3 text-sm outline-none"
                        />
                    </FormField>

                    <FormField
                        label="Datum isteka"
                        :error="form.errors.endDate"
                    >
                        <input
                            v-model="form.endDate"
                            type="date"
                            class="border border-gray-200 rounded-lg px-4 py-3 text-sm outline-none"
                        />
                    </FormField>
                </div>

                <!-- Logo (obavezno) -->
                <FormField
                    label="Logo"
                    :error="form.errors.logo"
                    :required="true"
                >
                    <div
                        class="border-2 border-dashed rounded-xl p-4 flex flex-col items-center gap-2 cursor-pointer transition-colors"
                        :class="
                            form.errors.logo
                                ? 'border-red-300'
                                : 'border-gray-200 hover:border-emerald-400'
                        "
                        @click="$refs.logoInput.click()"
                    >
                        <img
                            v-if="logoPreview"
                            :src="logoPreview"
                            class="w-20 h-20 object-cover rounded-full"
                        />
                        <span v-else class="text-4xl text-gray-300">🖼️</span>
                        <p class="text-xs text-gray-400">
                            Klikni da dodaš logo
                        </p>
                    </div>
                    <input
                        ref="logoInput"
                        type="file"
                        accept="image/*"
                        class="hidden"
                        @change="onLogoChange"
                    />
                </FormField>

                <!-- Multimedia (opciono) -->
                <FormField
                    label="Naslovna slika (opciono)"
                    :error="form.errors.multimedia"
                >
                    <div
                        class="border-2 border-dashed border-gray-200 rounded-xl p-4 flex flex-col items-center gap-2 cursor-pointer hover:border-emerald-400 transition-colors"
                        @click="$refs.multimediaInput.click()"
                    >
                        <img
                            v-if="multimediaPreview"
                            :src="multimediaPreview"
                            class="w-full h-32 object-cover rounded-lg"
                        />
                        <span v-else class="text-4xl text-gray-300">🖼️</span>
                        <p class="text-xs text-gray-400">
                            Klikni da dodaš sliku
                        </p>
                    </div>
                    <input
                        ref="multimediaInput"
                        type="file"
                        accept="image/*"
                        class="hidden"
                        @change="onMultimediaChange"
                    />
                </FormField>

                <!-- Dugmad -->
                <div class="flex gap-3">
                    <Link
                        :href="route('news.index')"
                        class="flex-1 text-center border border-gray-200 text-gray-600 hover:bg-gray-50 font-medium py-3 rounded-lg transition-colors text-sm"
                    >
                        Otkaži
                    </Link>
                    <Button
                        type="submit"
                        :disabled="form.processing"
                        class="flex-1"
                    >
                        Objavi vest
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import Button from "@/Pages/Components/Button.vue";
import FormField from "@/Pages/Components/FormField.vue";

const logoPreview = ref(null);
const multimediaPreview = ref(null);

const form = useForm({
    type: "",
    title: "",
    text: "",
    startDate: "",
    endDate: "",
    logo: null,
    multimedia: null,
});

const onLogoChange = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    form.logo = file;
    logoPreview.value = URL.createObjectURL(file);
};

const onMultimediaChange = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    form.multimedia = file;
    multimediaPreview.value = URL.createObjectURL(file);
};

const submit = () => {
    form.post(route("news.store"), {
        forceFormData: true,
    });
};
</script>
