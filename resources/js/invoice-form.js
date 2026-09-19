import Alpine from 'alpinejs';

Alpine.data('invoiceForm', (initialItems = []) => ({
    items: Array.isArray(initialItems) && initialItems.length > 0
        ? initialItems
        : [{ quantity: 1, unit_price: 0 }],
    addItem() {
        this.items.push({ quantity: 1, unit_price: 0 });
    },
    removeItem(index) {
        this.items.splice(index, 1);
    },
    formatMoney(value) {
        return new Intl.NumberFormat('id-ID').format(value || 0);
    },
}));