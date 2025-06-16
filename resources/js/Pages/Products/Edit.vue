<script setup>
import { ref, computed, onMounted } from 'vue';
import { ImageIcon, Loader2 } from 'lucide-vue-next';
import { useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    categories: Array,
    product: Object,
    errors: Object
});

// Determine if we're editing an existing product
const productId = computed(() => route.params.id);

// Form state
const form = useForm({
    name: props.product.name,
    description: props.product.description,
    price: props.product.price,
    image: props.product.products_images,
    quantity: props.product.quantity,
    category_id: props.product.category_id,
    sku: props.product.sku
});

const isSubmitting = ref(false);
const imagePreview = ref(null);

// Methods
const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    // Validate file type
    if (!file.type.match('image.*')) {
        // errors.value.image = 'Please select an image file';
        return;
    }

    // Validate file size (max 10MB)
    if (file.size > 10 * 1024 * 1024) {
        // errors.value.image = 'Image size should not exceed 10MB';
        return;
    }

    // Clear previous error
    // errors.value.image = null;

    // Store file for form submission
    form.image = file;

    // Create preview
    const reader = new FileReader();
    reader.onload = (e) => {
        imagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
};

const submit = async () => {
    isSubmitting.value = true;

    router.post(`/products/${props.product.id}`, {
        _method: 'put',
        name: form.name,
        description: form.description,
        price: form.price,
        image: form.image,
        quantity: form.quantity,
        category_id: form.category_id,
        sku: form.sku,
        onSuccess: () => {
            isSubmitting.value = false;
            alert('Product updated successfully');
        },
        onError: (errors) => {
            console.error(errors);
            alert('An error occurred while updating the product');
        },
        onFinish: () => {
            isSubmitting.value = false;
        }
    });
};

onMounted(() => {
    // Set image preview if editing
    if (props.product.image) {
        imagePreview.value = `/storage/${props.product.image}`;
    }
});

</script>

<style>
:root {
    --color-primary: 59 130 246;
    /* blue-500 */
    --color-primary-dark: 37 99 235;
    /* blue-600 */
}

.text-primary {
    color: rgb(var(--color-primary));
}

.text-primary-dark {
    color: rgb(var(--color-primary-dark));
}

.bg-primary {
    background-color: rgb(var(--color-primary));
}

.bg-primary-dark {
    background-color: rgb(var(--color-primary-dark));
}

.border-primary {
    border-color: rgb(var(--color-primary));
}

.focus\:ring-primary:focus {
    --tw-ring-color: rgb(var(--color-primary));
}

.focus\:border-primary:focus {
    border-color: rgb(var(--color-primary));
}

.hover\:bg-primary-dark:hover {
    background-color: rgb(var(--color-primary-dark));
}

.hover\:text-primary-dark:hover {
    color: rgb(var(--color-primary-dark));
}
</style>

<template>
    <AuthenticatedLayout>
        <div class="min-h-screen bg-gray-50 p-4 sm:p-6 lg:p-8">
            <div class="max-w-3xl mx-auto">
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Edit Product</h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Update your product information
                    </p>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="bg-white shadow rounded-lg">
                    <div class="p-6 space-y-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Product Name <span
                                    class="text-red-500">*</span></label>
                            <input type="text" id="name" v-model="form.name"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm"
                                :class="{ 'border-red-300': errors.name }" placeholder="Enter product name" required />
                            <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description <span
                                    class="text-red-500">*</span></label>
                            <textarea id="description" v-model="form.description" rows="4"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm"
                                :class="{ 'border-red-300': errors.description }"
                                placeholder="Enter product description" required></textarea>
                            <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
                        </div>

                        <!-- Price and Quantity -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700">Price ($) <span
                                        class="text-red-500">*</span></label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" id="price" v-model="form.price" step="0.01" min="0"
                                        class="pl-7 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm"
                                        :class="{ 'border-red-300': errors.price }" placeholder="0.00" required />
                                </div>
                                <p v-if="errors.price" class="mt-1 text-sm text-red-600">{{ errors.price }}</p>
                            </div>
                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity <span
                                        class="text-red-500">*</span></label>
                                <input type="number" id="quantity" v-model="form.quantity" min="0"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm"
                                    :class="{ 'border-red-300': errors.quantity }" placeholder="Enter stock quantity"
                                    required />
                                <p v-if="errors.quantity" class="mt-1 text-sm text-red-600">{{ errors.quantity }}</p>
                            </div>
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700">Category <span
                                    class="text-red-500">*</span></label>
                            <select id="category" v-model="form.category_id"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md"
                                :class="{ 'border-red-300': errors.category_id }" required>
                                <option value="" disabled>Select a category</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                            <p v-if="errors.category_id" class="mt-1 text-sm text-red-600">{{ errors.category_id }}</p>
                        </div>

                        <!-- SKU -->
                        <div>
                            <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
                            <input type="text" id="sku" v-model="form.sku"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm"
                                :class="{ 'border-red-300': errors.sku }" placeholder="Enter product SKU" />
                            <p v-if="errors.sku" class="mt-1 text-sm text-red-600">{{ errors.sku }}</p>
                        </div>

                        <!-- Image Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Product Image</label>
                            <div
                                class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <div v-if="imagePreview" class="mb-4">
                                        <img :src="imagePreview" alt="Preview"
                                            class="mx-auto h-40 w-auto object-contain" />
                                    </div>
                                    <div v-else-if="form.image && typeof form.image === 'string'" class="mb-4">
                                        <img :src="form.image" alt="Current image"
                                            class="mx-auto h-40 w-auto object-contain" />
                                    </div>
                                    <div v-else class="flex justify-center">
                                        <ImageIcon class="mx-auto h-12 w-12 text-gray-400" />
                                    </div>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="image-upload"
                                            class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-primary-dark focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary">
                                            <span>Upload a file</span>
                                            <input id="image-upload" name="image-upload" type="file" class="sr-only"
                                                accept="image/*" @input="form.image = $event.target.files[0]"
                                                @change="handleImageUpload" />
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        PNG, JPG, GIF up to 10MB
                                    </p>
                                </div>
                            </div>
                            <p v-if="errors.image" class="mt-1 text-sm text-red-600">{{ errors.image }}</p>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="px-6 py-4 bg-gray-50 flex items-center justify-end space-x-3 rounded-b-lg">
                        <a href="/products"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">Cancel</a>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                            :disabled="isSubmitting">
                            <Loader2 v-if="isSubmitting" class="animate-spin h-4 w-4 mr-2" />
                            {{ isSubmitting ? 'Updating...' : 'Update Product' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>