<template>
    <div>
        <!-- Quantity + price row -->
        <div class="flex items-center gap-6 mb-6">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-600 mb-1">Price</label>
                <span class="text-gray-900 font-medium">${{ priceInDollars }}</span>
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-600 mb-1">Qty</label>
                <input
                    v-model="quantity"
                    type="number"
                    min="1"
                    :disabled="processing"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
            </div>
        </div>

        <!-- Buy button -->
        <button
            @click="openModal"
            :disabled="processing"
            class="w-full bg-blue-500 hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold py-3 px-4 rounded transition-colors"
            >
            {{ processing ? 'Processing...' : 'Buy Tickets' }}
        </button>

        <!-- Modal overlay -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/50" @click="closeModal"></div>

            <!-- Modal card -->
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md mx-4 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-1">Complete your purchase</h2>
                <p class="text-sm text-gray-500 mb-6">{{ quantity }} × {{ concertTitle }} — ${{ totalInDollars }}</p>

                <!-- Email field -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input
                        v-model="email"
                        type="email"
                        placeholder="you@example.com"
                        :disabled="processing"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                </div>

                <!-- Stripe Card Element mount point -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Card details</label>
                    <div id="payment-element" class="border border-gray-300 rounded px-3 py-2 min-h-[42px]"></div>
                </div>

                <!-- Error message -->
                <p v-if="cardError" class="text-red-600 text-sm mb-4">{{ cardError }}</p>

                <div class="flex gap-3">
                    <button
                        @click="closeModal"
                        :disabled="processing"
                        class="flex-1 border border-gray-300 text-gray-700 font-medium py-2 px-4 rounded hover:bg-gray-50 transition-colors disabled:opacity-50"
                        >
                        Cancel
                    </button>
                    <button
                        @click="submitPayment"
                        :disabled="processing || !email"
                        class="flex-1 bg-blue-500 hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed text-white font-medium py-2 px-4 rounded transition-colors"
                        >
                        {{ processing ? 'Processing...' : `Pay $${totalInDollars}` }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { loadStripe } from '@stripe/stripe-js'

    export default {
        props: ['price', 'concertTitle', 'concertId'],

        data() {
            return {
                quantity: 1,
                email: '',
                processing: false,
                showModal: false,
                cardError: null,
                stripe: null,
                cardElement: null,
            }
        },

        computed: {
            priceInDollars() {
                return (this.price / 100).toFixed(2)
            },
            totalInDollars() {
                return ((this.price * this.quantity) / 100).toFixed(2)
            },
        },

        async created() {
            this.stripe = await loadStripe(window.App.stripePublicKey)
        },

        methods: {
            async openModal() {
                this.showModal = true
                this.cardError = null

                // Wait for the #payment-element div to exist in the DOM
                await this.$nextTick()

                const elements = this.stripe.elements()
                this.cardElement = elements.create('card', {
                    style: {
                        base: {
                            fontSize: '14px',
                            color: '#111827',
                            '::placeholder': {color: '#9ca3af'},
                        },
                    },
                })
                this.cardElement.mount('#payment-element')
            },

            closeModal() {
                if (this.processing)
                    return
                this.showModal = false
                this.cardError = null
                if (this.cardElement) {
                    this.cardElement.destroy()
                    this.cardElement = null
                }
            },

            async submitPayment() {
                if (!this.email)
                    return

                this.processing = true
                this.cardError = null

                // createPaymentMethod gives us a pm_... ID — exactly what StripePaymentGateway expects
                const {paymentMethod, error} = await this.stripe.createPaymentMethod({
                    type: 'card',
                    card: this.cardElement,
                    billing_details: {email: this.email},
                })

                if (error) {
                    this.cardError = error.message
                    this.processing = false
                    return
                }

                axios.post(`/concerts/${this.concertId}/orders`, {
                    email: this.email,
                    ticket_quantity: this.quantity,
                    payment_token: paymentMethod.id,
                }).then(response => {
                    window.location = `/orders/${response.data.confirmation_number}`
                }).catch(() => {
                    this.cardError = 'Payment failed. Please check your details and try again.'
                    this.processing = false
                })
            },
        },
    }
</script>