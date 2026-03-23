<template>
    <div>
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
                    class="w-full border border-gray-300 rounded px-3 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>
        </div>
        <button
            @click="openStripe"
            :disabled="processing"
            class="w-full bg-blue-500 hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold py-3 px-4 rounded transition-colors"
        >
            {{ processing ? 'Processing...' : 'Buy Tickets' }}
        </button>
    </div>
</template>

<script>
    export default {
        props: ['price', 'concertTitle', 'concertId'],
        data() {
            return {
                quantity: 1,
                stripeHandler: null,
                processing: false,
            }
        },
        computed: {
            description() {
                return this.quantity > 1
                        ? `${this.quantity} tickets to ${this.concertTitle}`
                        : `One ticket to ${this.concertTitle}`
            },
            totalPrice() {
                return this.quantity * this.price
            },
            priceInDollars() {
                return (this.price / 100).toFixed(2)
            },
        },
        methods: {
            initStripe() {
                const handler = StripeCheckout.configure({
                    key: window.App.stripePublicKey,
                })
                window.addEventListener('popstate', () => handler.close())
                return handler
            },
            openStripe() {
                this.stripeHandler.open({
                    name: 'TicketBeast',
                    description: this.description,
                    currency: 'usd',
                    allowRememberMe: false,
                    panelLabel: 'Pay {{amount}}',
                    amount: this.totalPrice,
                    token: this.purchaseTickets,
                })
            },
            purchaseTickets(token) {
                this.processing = true
                axios.post(`/concerts/${this.concertId}/orders`, {
                    email: token.email,
                    ticket_quantity: this.quantity,
                    payment_token: token.id,
                }).then(() => {
                    console.log('Charge succeeded')
                }).catch(() => {
                    this.processing = false
                })
            },
        },
        created() {
            this.stripeHandler = this.initStripe()
        },
    }
</script>