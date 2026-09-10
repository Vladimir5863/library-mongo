<script setup>
import { ref } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import Book from "@/Pages/Components/Book.vue";

const props = defineProps({
    book: Object,
    relatedBooks: Array,
});

const showLoanModal = ref(false);
const showSellModal = ref(false);

const loanForm = useForm({
    bookId: props.book.bookId,
    deliveryType: "library",
});

const sellForm = useForm({
    bookId: props.book.bookId,
    deliveryType: "library",
});

const submitLoan = () => {
    loanForm.post(route("loans.store"), {
        onSuccess: () => (showLoanModal.value = false),
    });
};

const submitSell = () => {
    sellForm.post(route("sell.store"), {
        onSuccess: () => (showSellModal.value = false),
    });
};

const tab = ref("opis");
</script>

<template>
    <div class="min-h-screen bg-gray-50 px-6 py-8">
        <div class="max-w-5xl mx-auto flex flex-col gap-8">
            <!-- Nazad -->
            <Link
                href="/books"
                class="inline-flex items-center gap-2 bg-emerald-700 text-white text-sm px-4 py-2 rounded-full w-fit hover:bg-emerald-600 transition-colors"
            >
                ← Nazad
            </Link>

            <!-- Glavni deo -->
                <div class="flex flex-col md:flex-row gap-8">
                <!-- Slika -->
                    <div class="w-full sm:w-48 flex-shrink-0">
                    <img
                        v-if="book.preview_image"
                        :src="'/storage/' + book.preview_image"
                        :alt="book.title"
                        class="w-full rounded-xl shadow-lg object-cover"
                    />
                    <div
                        v-else
                        class="w-full h-64 bg-gray-200 rounded-xl flex items-center justify-center"
                    >
                        <span class="text-5xl">📚</span>
                    </div>
                </div>

                <!-- Info -->
                <div class="flex-1 flex flex-col gap-4">
                    <div>
                        <p class="text-xs text-gray-400 italic mb-1">
                            {{ book.genre }}
                        </p>
                        <h1 class="text-3xl font-bold text-gray-800">
                            {{ book.title }}
                        </h1>
                        <p class="text-gray-500 mt-1 text-sm">
                            {{ book.author }},
                            {{ book.publicationDate?.split("-")[0] }}
                        </p>
                    </div>

                    <!-- Tabovi -->
                    <div class="flex gap-2">
                        <button
                            @click="tab = 'opis'"
                            class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors"
                            :class="
                                tab === 'opis'
                                    ? 'bg-emerald-700 text-white'
                                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                            "
                        >
                            Opis
                        </button>
                        <button
                            @click="tab = 'info'"
                            class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors"
                            :class="
                                tab === 'info'
                                    ? 'bg-emerald-700 text-white'
                                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                            "
                        >
                            Info
                        </button>
                    </div>

                    <!-- Tab sadržaj -->
                    <div
                        class="bg-emerald-600 text-white rounded-xl p-4 text-sm leading-relaxed min-h-32"
                    >
                        <p v-if="tab === 'opis'">{{ book.description }}</p>
                        <div v-else class="flex flex-col gap-2">
                            <p>
                                <span class="font-semibold">Izdavač:</span>
                                {{ book.publisher }}
                            </p>
                            <p>
                                <span class="font-semibold">Jezik:</span>
                                {{ book.language }}
                            </p>
                            <p>
                                <span class="font-semibold">Broj strana:</span>
                                {{ book.numberOfPages }}
                            </p>
                            <p>
                                <span class="font-semibold"
                                    >Datum izdanja:</span
                                >
                                {{ book.publicationDate }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Status i akcije -->
                    <div class="w-full sm:w-44 flex-shrink-0 flex flex-col gap-3">
                    <!-- Status -->
                    <div
                        class="bg-white rounded-xl p-4 flex flex-col gap-2 shadow-sm"
                    >
                        <p class="text-xs font-semibold text-gray-700">
                            Status:
                        </p>
                        <span
                            class="text-xs px-2 py-1 rounded-full w-fit font-medium"
                            :class="
                                book.remainingForLoan > 0
                                    ? 'bg-emerald-100 text-emerald-700'
                                    : 'bg-red-100 text-red-600'
                            "
                        >
                            {{
                                book.remainingForLoan > 0
                                    ? "Dostupno"
                                    : "Nedostupno"
                            }}
                        </span>
                        <div class="flex flex-col gap-1 text-xs text-gray-400">
                            <p>
                                📖 {{ book.remainingForLoan }} za iznajmljivanje
                            </p>
                            <p>🛒 {{ book.remainingForSell }} za kupovinu</p>
                        </div>
                        <!-- Cena -->
                        <div class="pt-2 border-t border-gray-100 mt-1">
                            <p class="text-xs text-gray-500">Cena:</p>
                            <p
                                v-if="book.current_price"
                                class="text-base font-bold text-emerald-700"
                            >
                                {{ book.current_price.price }} RSD
                            </p>
                            <p v-else class="text-xs text-gray-400">
                                Cena nije definisana
                            </p>
                        </div>
                    </div>

                    <!-- Iznajmi -->
                    <div
                        v-if="showLoanModal"
                        class="flex flex-col gap-2 bg-white rounded-xl p-3 shadow-sm"
                    >
                        <p class="text-xs font-medium text-gray-700">
                            Tip preuzimanja:
                        </p>
                        <label
                            class="flex items-center gap-2 text-xs cursor-pointer"
                        >
                            <input
                                type="radio"
                                v-model="loanForm.deliveryType"
                                value="library"
                                class="accent-emerald-700"
                            />
                            U biblioteci
                        </label>
                        <label
                            class="flex items-center gap-2 text-xs cursor-pointer"
                        >
                            <input
                                type="radio"
                                v-model="loanForm.deliveryType"
                                value="physical"
                                class="accent-emerald-700"
                            />
                            Fizička dostava
                        </label>
                        <div class="flex gap-1 mt-1">
                            <button
                                @click="submitLoan"
                                class="flex-1 bg-emerald-700 text-white text-xs py-1.5 rounded-lg hover:bg-emerald-600"
                            >
                                Potvrdi
                            </button>
                            <button
                                @click="showLoanModal = false"
                                class="flex-1 bg-gray-100 text-gray-600 text-xs py-1.5 rounded-lg hover:bg-gray-200"
                            >
                                Otkaži
                            </button>
                        </div>
                        <p
                            v-if="loanForm.errors.loan"
                            class="text-xs text-red-500"
                        >
                            {{ loanForm.errors.loan }}
                        </p>
                    </div>

                    <button
                        v-else
                        @click="showLoanModal = true"
                        :disabled="book.remainingForLoan <= 0"
                        class="w-full bg-emerald-700 hover:bg-emerald-600 disabled:opacity-50 text-white text-xs font-medium py-2 px-3 rounded-lg transition-colors"
                    >
                        📖 Iznajmi
                    </button>

                    <!-- Kupi -->
                    <div
                        v-if="showSellModal"
                        class="flex flex-col gap-2 bg-white rounded-xl p-3 shadow-sm"
                    >
                        <p class="text-xs font-medium text-gray-700">
                            Tip dostave:
                        </p>
                        <label
                            class="flex items-center gap-2 text-xs cursor-pointer"
                        >
                            <input
                                type="radio"
                                v-model="sellForm.deliveryType"
                                value="library"
                                class="accent-emerald-700"
                            />
                            U biblioteci
                        </label>
                        <label
                            class="flex items-center gap-2 text-xs cursor-pointer"
                        >
                            <input
                                type="radio"
                                v-model="sellForm.deliveryType"
                                value="physical"
                                class="accent-emerald-700"
                            />
                            Fizička dostava
                        </label>
                        <div class="flex gap-1 mt-1">
                            <button
                                @click="submitSell"
                                class="flex-1 bg-emerald-700 text-white text-xs py-1.5 rounded-lg hover:bg-emerald-600"
                            >
                                Potvrdi
                            </button>
                            <button
                                @click="showSellModal = false"
                                class="flex-1 bg-gray-100 text-gray-600 text-xs py-1.5 rounded-lg hover:bg-gray-200"
                            >
                                Otkaži
                            </button>
                        </div>
                        <p
                            v-if="sellForm.errors.sell"
                            class="text-xs text-red-500"
                        >
                            {{ sellForm.errors.sell }}
                        </p>
                    </div>

                    <button
                        v-else
                        @click="showSellModal = true"
                        :disabled="book.remainingForSell <= 0"
                        class="w-full bg-emerald-700 hover:bg-emerald-600 disabled:opacity-50 text-white text-xs font-medium py-2 px-3 rounded-lg transition-colors"
                    >
                        🛒 Kupi
                    </button>
                </div>
            </div>

            <!-- Srodne preporuke -->
            <div class="mt-4">
                <h2 class="text-xl font-serif font-semibold text-gray-800 mb-6">
                    Srodne preporuke
                </h2>
                <div
                    class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6"
                >
                    <Book
                        v-for="related in relatedBooks"
                        :key="related.bookId"
                        :book="related"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
