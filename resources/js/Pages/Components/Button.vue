<script setup>
const props = defineProps({
    variant: {
        type: String,
        default: "primary",
    },
    type: {
        type: String,
        default: "button",
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const variantClasses = {
    primary: "bg-emerald-700 hover:bg-emerald-600 text-white",
    outline:
        "border border-gray-200 text-gray-700 hover:bg-gray-50 bg-white",
    ghost: "text-gray-700 hover:bg-gray-100 bg-transparent",
    danger: "bg-red-600 hover:bg-red-500 text-white",
};
</script>

<template>
    <button
        :type="type"
        :disabled="disabled"
        :class="[
            'rounded-lg font-medium transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed px-4 py-3 text-sm',
            variantClasses[variant] ?? variantClasses.primary,
        ]"
    >
        <slot />
    </button>
</template>

