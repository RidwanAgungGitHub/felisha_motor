<!-- Include this in your kasir_styles.blade.php file -->
<style>
    /* General styles for the cashier interface */
    .card-header {
        font-weight: bold;
    }

    .product-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .cart-summary {
        position: sticky;
        top: 20px;
    }

    #selected-product-info {
        border-left: 5px solid #17a2b8;
    }

    /* Receipt styles */
    .receipt {
        font-family: 'Courier New', monospace;
        width: 100%;
        max-width: 400px;
        margin: 0 auto;
        padding: 10px;
    }

    .receipt-header {
        text-align: center;
        margin-bottom: 10px;
    }

    .receipt-header h4 {
        margin: 5px 0;
        font-size: 18px;
    }

    .receipt-header p {
        margin: 2px 0;
        font-size: 12px;
    }

    .receipt-content {
        margin-bottom: 10px;
    }

    .receipt-content p {
        margin: 2px 0;
        font-size: 12px;
    }

    .receipt-item {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        margin-bottom: 5px;
    }

    .receipt-totals {
        border-top: 1px dashed #000;
        margin-top: 10px;
        padding-top: 5px;
    }

    .receipt-totals p {
        display: flex;
        justify-content: space-between;
        margin: 2px 0;
        font-size: 12px;
    }

    .receipt-footer {
        text-align: center;
        border-top: 1px dashed #000;
        margin-top: 10px;
        padding-top: 5px;
        font-size: 12px;
    }

    @media print {
        body * {
            visibility: hidden;
        }

        .modal-dialog,
        .modal-content {
            border: none !important;
            box-shadow: none !important;
        }

        #printArea,
        #printArea * {
            visibility: visible;
        }

        #printArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .no-print {
            display: none !important;
        }
    }
</style>
