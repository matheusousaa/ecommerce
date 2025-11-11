<script setup>
import { X, Plus, Minus, ShoppingCartIcon } from 'lucide-vue-next';

const props = defineProps({
  showCart: Boolean,
  cart: Array,
  cartTotal: Number
});

const emit = defineEmits(['close', 'update-cart-quantity', 'remove-item-from-cart']);

function handleClose() {
  emit('close');
}

function handleUpdateCartQuantity(productId, newQuantity) {
  emit('update-cart-quantity', productId, newQuantity);
}

function handleRemoveItemFromCart(productId) {
  emit('remove-item-from-cart', productId);
}

const formatPrice = (price) => {
  return parseFloat(price).toFixed(2);
};

</script>

<template>
  <div v-if="showCart" class="fixed inset-0 overflow-hidden z-50">
    <div class="absolute inset-0 overflow-hidden">
      <div class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="handleClose"></div>
      <section class="absolute right-0 top-0 h-full w-full max-w-md flex flex-col bg-white shadow-xl">
        <div class="flex-1 overflow-y-auto py-6 px-4 sm:px-6">
          <div class="flex items-start justify-between">
            <h2 class="text-lg font-medium text-gray-900">Shopping Cart</h2>
            <div class="ml-3 h-7 flex items-center">
              <button @click="handleClose" class="text-gray-400 hover:text-gray-500">
                <X class="h-6 w-6" />
              </button>
            </div>
          </div>

          <div class="mt-8">
            <div v-if="cart.length === 0" class="text-center py-12">
              <ShoppingCartIcon class="mx-auto h-12 w-12 text-gray-400" />
              <h3 class="mt-2 text-sm font-medium text-gray-900">Your cart is empty</h3>
              <p class="mt-1 text-sm text-gray-500">Start adding some products!</p>
            </div>
            
            <div v-else class="flow-root">
              <ul class="-my-6 divide-y divide-gray-200">
                <li v-for="item in cart" :key="item.id" class="py-6 flex">
                  <div class="flex-shrink-0 w-16 h-16 border border-gray-200 rounded-md overflow-hidden">
                    <img :src="`/storage/${item.image}` || '/placeholder.svg'" :alt="item.name" class="w-full h-full object-center object-cover">
                  </div>

                  <div class="ml-4 flex-1 flex flex-col">
                    <div>
                      <div class="flex justify-between text-base font-medium text-gray-900">
                        <h3>{{ item.name }}</h3>
                        <p class="ml-4">${{ formatPrice(item.price * item.quantity) }}</p>
                      </div>
                    </div>
                    <div class="flex-1 flex items-end justify-between text-sm">
                      <div class="flex items-center space-x-2">
                        <button
                          @click="handleUpdateCartQuantity(item.id, item.quantity - 1)"
                          class="p-1 rounded-md border border-gray-300 hover:bg-gray-50"
                          :disabled="item.quantity <= 1"
                        >
                          <Minus class="h-3 w-3" />
                        </button>
                        <span class="text-gray-500">Qty {{ item.quantity }}</span>
                        <button
                          @click="handleUpdateCartQuantity(item.id, item.quantity + 1)"
                          class="p-1 rounded-md border border-gray-300 hover:bg-gray-50"
                        >
                          <Plus class="h-3 w-3" />
                        </button>
                      </div>

                      <div class="flex">
                        <button @click="handleRemoveItemFromCart(item.id)" class="font-medium text-primary hover:text-primary-dark">
                          Remove
                        </button>
                      </div>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div v-if="cart.length > 0" class="border-t border-gray-200 py-6 px-4 sm:px-6">
          <div class="flex justify-between text-base font-medium text-gray-900">
            <p>Subtotal</p>
            <p>${{ cartTotal.toFixed(2) }}</p>
          </div>
          <p class="mt-0.5 text-sm text-gray-500">Shipping and taxes calculated at checkout.</p>
          <div class="mt-6">
            <button class="w-full flex justify-center items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-primary hover:bg-primary-dark">
              Checkout
            </button>
          </div>
          <div class="mt-6 flex justify-center text-sm text-center text-gray-500">
            <p>
              or 
              <button @click="handleClose" class="text-primary font-medium hover:text-primary-dark">
                Continue Shopping<span aria-hidden="true"> &rarr;</span>
              </button>
            </p>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>