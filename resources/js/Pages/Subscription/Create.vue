<template>
    <div
        class="min-h-screen bg-gray-50 flex flex-col items-center justify-center px-6 py-12"
    >
        <div class="w-full max-w-2xl flex flex-col gap-6">
            <h2 class="text-2xl font-serif font-bold text-center text-gray-800">
                Izaberi plan
            </h2>

            <!-- Planovi -->
            <div class="grid grid-cols-2 gap-6">
                <div
                    class="bg-white rounded-2xl shadow-md p-6 flex flex-col gap-4 cursor-pointer border-2 transition-colors"
                    :class="
                        form.plan === 'monthly'
                            ? 'border-emerald-700'
                            : 'border-transparent'
                    "
                    @click="form.plan = 'monthly'"
                >
                    <h3 class="text-lg font-bold text-gray-800">Mesečni</h3>
                    <p class="text-3xl font-bold text-emerald-700">
                        2.99
                        <span class="text-base font-normal text-gray-500"
                            >RSD/mes</span
                        >
                    </p>
                    <ul class="text-sm text-gray-600 flex flex-col gap-2">
                        <li>✅ Neograničeno iznajmljivanje</li>
                        <li>✅ Digitalne knjige</li>
                        <li>❌ Popust na kupovinu</li>
                    </ul>
                </div>

                <div
                    class="bg-white rounded-2xl shadow-md p-6 flex flex-col gap-4 cursor-pointer border-2 transition-colors"
                    :class="
                        form.plan === 'yearly'
                            ? 'border-emerald-700'
                            : 'border-transparent'
                    "
                    @click="form.plan = 'yearly'"
                >
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800">
                            Godišnji
                        </h3>
                        <span
                            class="text-xs bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full font-medium"
                            >Uštedi 17%</span
                        >
                    </div>
                    <p class="text-3xl font-bold text-emerald-700">
                        29.99
                        <span class="text-base font-normal text-gray-500"
                            >RSD/god</span
                        >
                    </p>
                    <ul class="text-sm text-gray-600 flex flex-col gap-2">
                        <li>✅ Neograničeno iznajmljivanje</li>
                        <li>✅ Digitalne knjige</li>
                        <li>✅ Popust na kupovinu</li>
                    </ul>
                </div>
            </div>
            <p v-if="form.errors.plan" class="text-xs text-red-500">
                {{ form.errors.plan }}
            </p>

            <!-- Broj računa -->
            <FormField
                label="Broj računa za naplatu"
                :error="form.errors.accountNumber"
            >
                <input
                    v-model="form.accountNumber"
                    type="text"
                    placeholder="npr. 160-123456789-12"
                    class="border border-gray-200 rounded-lg px-4 py-3 text-sm outline-none focus:border-emerald-500"
                />
            </FormField>

            <!-- Auto-renewal -->
            <label
                class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer"
            >
                <input
                    type="checkbox"
                    v-model="form.autoRenew"
                    class="accent-emerald-700"
                />
                Automatski obnovi pretplatu
            </label>

            <!-- Dugmad -->
            <div class="flex gap-3">
                <Link
                    :href="route('subscription.index')"
                    class="flex-1 text-center border border-gray-200 text-gray-600 hover:bg-gray-50 font-medium py-3 rounded-lg transition-colors text-sm"
                >
                    Otkaži
                </Link>
                <Button
                    type="button"
                    @click="form.post(route('subscription.store'))"
                    :disabled="!form.plan || form.processing"
                    class="flex-1"
                >
                    Aktiviraj pretplatu
                </Button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, useForm } from "@inertiajs/vue3";
import Button from "@/Pages/Components/Button.vue";
import FormField from "@/Pages/Components/FormField.vue";

const props = defineProps({
    accountNumber: String,
});

const form = useForm({
    plan: "",
    accountNumber: props.accountNumber ?? "",
    autoRenew: false,
});
</script>
