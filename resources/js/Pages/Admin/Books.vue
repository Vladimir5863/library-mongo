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
                    Knjige
                </h1>
            </div>

            <div class="bg-white rounded-2xl shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-max w-full">
                    <thead
                        class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider"
                    >
                        <tr>
                            <th class="px-6 py-3 text-left">Knjiga</th>
                            <th class="px-6 py-3 text-left">Trenutna cena</th>
                            <th class="px-6 py-3 text-left">
                                Za iznajmljivanje
                            </th>
                            <th class="px-6 py-3 text-left">Za kupovinu</th>
                            <th class="px-6 py-3 text-left">Akcije</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr
                            v-for="book in books.data"
                            :key="book.bookId"
                            class="hover:bg-gray-50"
                        >
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-800">
                                    {{ book.title }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ book.author }}
                                </p>
                            </td>
                            <td
                                class="px-6 py-4 text-sm text-emerald-700 font-medium"
                            >
                                {{
                                    book.current_price
                                        ? book.current_price.price + " RSD"
                                        : "Nema cene"
                                }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ book.remainingForLoan }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ book.remainingForSell }}
                            </td>
                            <td class="px-6 py-4 flex gap-3">
                                <button
                                    @click="openPriceModal(book)"
                                    class="text-xs text-emerald-700 hover:underline"
                                >
                                    Promeni cenu
                                </button>
                                <button
                                    @click="openStockModal(book)"
                                    class="text-xs text-blue-600 hover:underline"
                                >
                                    Promeni stanje
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>

            <Pagination :data="books" />

            <!-- Modal za cenu -->
            <div
                v-if="priceModal"
                class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50"
            >
                <div
                    class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md flex flex-col gap-4"
                >
                    <h3 class="text-lg font-semibold text-gray-800">
                        Promeni cenu — {{ selectedBook?.title }}
                    </h3>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-700"
                            >Nova cena (RSD)</label
                        >
                        <input
                            v-model="priceForm.price"
                            type="number"
                            class="border border-gray-200 rounded-lg px-4 py-2 text-sm outline-none"
                        />
                        <span
                            v-if="priceForm.errors.price"
                            class="text-xs text-red-500"
                            >{{ priceForm.errors.price }}</span
                        >
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-700"
                            >Datum početka</label
                        >
                        <input
                            v-model="priceForm.startDate"
                            type="date"
                            class="border border-gray-200 rounded-lg px-4 py-2 text-sm outline-none"
                        />
                        <span
                            v-if="priceForm.errors.startDate"
                            class="text-xs text-red-500"
                            >{{ priceForm.errors.startDate }}</span
                        >
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-700"
                            >Datum isteka</label
                        >
                        <input
                            v-model="priceForm.endDate"
                            type="date"
                            class="border border-gray-200 rounded-lg px-4 py-2 text-sm outline-none"
                        />
                        <span
                            v-if="priceForm.errors.endDate"
                            class="text-xs text-red-500"
                            >{{ priceForm.errors.endDate }}</span
                        >
                    </div>

                    <div class="flex gap-3">
                        <button
                            @click="priceModal = false"
                            class="flex-1 border border-gray-200 text-gray-600 py-2 rounded-lg text-sm hover:bg-gray-50"
                        >
                            Otkaži
                        </button>
                        <button
                            @click="submitPrice"
                            class="flex-1 bg-emerald-700 text-white py-2 rounded-lg text-sm hover:bg-emerald-600"
                        >
                            Sačuvaj
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal za stanje -->
            <div
                v-if="stockModal"
                class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50"
            >
                <div
                    class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md flex flex-col gap-4"
                >
                    <h3 class="text-lg font-semibold text-gray-800">
                        Promeni stanje — {{ selectedBook?.title }}
                    </h3>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-700"
                            >Za iznajmljivanje</label
                        >
                        <input
                            v-model="stockForm.remainingForLoan"
                            type="number"
                            min="0"
                            class="border border-gray-200 rounded-lg px-4 py-2 text-sm outline-none"
                        />
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-700"
                            >Za kupovinu</label
                        >
                        <input
                            v-model="stockForm.remainingForSell"
                            type="number"
                            min="0"
                            class="border border-gray-200 rounded-lg px-4 py-2 text-sm outline-none"
                        />
                    </div>

                    <div class="flex gap-3">
                        <button
                            @click="stockModal = false"
                            class="flex-1 border border-gray-200 text-gray-600 py-2 rounded-lg text-sm hover:bg-gray-50"
                        >
                            Otkaži
                        </button>
                        <button
                            @click="submitStock"
                            class="flex-1 bg-emerald-700 text-white py-2 rounded-lg text-sm hover:bg-emerald-600"
                        >
                            Sačuvaj
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import Pagination from "@/Pages/Components/Pagination.vue";

defineProps({
    books: Object,
});

const priceModal = ref(false);
const stockModal = ref(false);
const selectedBook = ref(null);

const priceForm = useForm({
    price: "",
    startDate: "",
    endDate: "",
});

const stockForm = useForm({
    remainingForLoan: 0,
    remainingForSell: 0,
});

const openPriceModal = (book) => {
    selectedBook.value = book;
    priceForm.price = book.current_price?.price ?? "";
    priceForm.startDate = new Date().toISOString().split("T")[0];
    priceForm.endDate = "";
    priceModal.value = true;
};

const openStockModal = (book) => {
    selectedBook.value = book;
    stockForm.remainingForLoan = book.remainingForLoan;
    stockForm.remainingForSell = book.remainingForSell;
    stockModal.value = true;
};

const submitPrice = () => {
    priceForm.patch(route("admin.books.price", selectedBook.value.bookId), {
        onSuccess: () => (priceModal.value = false),
    });
};

const submitStock = () => {
    stockForm.patch(route("admin.books.stock", selectedBook.value.bookId), {
        onSuccess: () => (stockModal.value = false),
    });
};
</script>
