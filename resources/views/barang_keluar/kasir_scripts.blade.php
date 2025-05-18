<!-- Include this in your kasir_scripts.blade.php file -->
<script>
    $(document).ready(function() {
        // Initialize variables
        let cart = [];
        let selectedProduct = null;
        let totalAmount = 0;
        let productCache = {}; // Cache for product data

        // Display product info
        function displaySelectedProductInfo(product) {
            $('#selected-kode').text(product.kode || product.id);
            $('#selected-nama').text(product.nama_barang);
            $('#selected-harga').text(formatRupiah(product.harga_jual));
            $('#selected-stok').text(product.stok);
            $('#selected-product-info').show();
        }

        // Format number to Rupiah
        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        // Add to cart button
        $('#btn-add-to-cart').click(function() {
            if (!selectedProduct) {
                alert('Silakan pilih barang terlebih dahulu');
                return;
            }

            const quantity = parseInt($('#jumlah').val());

            if (isNaN(quantity) || quantity < 1) {
                alert('Jumlah barang harus minimal 1');
                return;
            }

            if (quantity > selectedProduct.stok) {
                alert('Jumlah melebihi stok tersedia');
                return;
            }

            // Check if product already in cart
            const existingItem = cart.find(item => item.id === selectedProduct.id);

            if (existingItem) {
                if (existingItem.quantity + quantity > selectedProduct.stok) {
                    alert('Total jumlah melebihi stok tersedia');
                    return;
                }
                // Update quantity if product already exists
                existingItem.quantity += quantity;
                existingItem.subtotal = existingItem.quantity * existingItem.price;
            } else {
                // Add new product to cart
                cart.push({
                    id: selectedProduct.id,
                    code: selectedProduct.kode || selectedProduct.id,
                    name: selectedProduct.nama_barang,
                    price: selectedProduct.harga_jual,
                    quantity: quantity,
                    subtotal: selectedProduct.harga_jual * quantity
                });
            }

            // Update cart display
            updateCartDisplay();

            // Reset selection
            $('#barang_search').val(null).trigger('change');
            selectedProduct = null;
            $('#selected-product-info').hide();
        });

        // Update cart display
        function updateCartDisplay() {
            const cartTable = $('#cart-items');
            cartTable.empty();

            totalAmount = 0;

            if (cart.length === 0) {
                $('#empty-cart-message').show();
                $('#cart-table').hide();
            } else {
                $('#empty-cart-message').hide();
                $('#cart-table').show();

                cart.forEach((item, index) => {
                    totalAmount += item.subtotal;

                    cartTable.append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.code}</td>
                            <td>${item.name}</td>
                            <td>Rp. ${formatRupiah(item.price)}</td>
                            <td>${item.quantity}</td>
                            <td>Rp. ${formatRupiah(item.subtotal)}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-info btn-edit-item" data-index="${index}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-remove-item" data-index="${index}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `);
                });
            }

            // Update summary
            $('#total-items').text(cart.length);
            $('#total-belanja').text(formatRupiah(totalAmount));

            // Update payment button state
            updatePaymentButtonState();
        }

        // Remove item from cart
        $(document).on('click', '.btn-remove-item', function() {
            const index = $(this).data('index');
            cart.splice(index, 1);
            updateCartDisplay();
        });

        // Edit item quantity
        $(document).on('click', '.btn-edit-item', function() {
            const index = $(this).data('index');
            const item = cart[index];

            const newQuantity = prompt(`Ubah jumlah untuk ${item.name}:`, item.quantity);
            const qty = parseInt(newQuantity);

            if (!isNaN(qty) && qty > 0) {
                // Check stock from cache or assume current value is ok
                const cachedProduct = productCache[item.id];
                const maxStock = cachedProduct ? cachedProduct.stok : item.quantity;

                if (qty <= maxStock) {
                    cart[index].quantity = qty;
                    cart[index].subtotal = qty * item.price;
                    updateCartDisplay();
                } else {
                    alert('Jumlah melebihi stok tersedia');
                }
            }
        });

        // Calculate change
        $('#total_bayar').on('input', function() {
            const bayar = parseInt($(this).val()) || 0;
            const kembalian = bayar - totalAmount;

            $('#kembalian').text(formatRupiah(kembalian > 0 ? kembalian : 0));
            updatePaymentButtonState();
        });

        // Update payment button state
        function updatePaymentButtonState() {
            const bayar = parseInt($('#total_bayar').val()) || 0;

            if (cart.length > 0 && bayar >= totalAmount) {
                $('#btn-process-payment').prop('disabled', false);
            } else {
                $('#btn-process-payment').prop('disabled', true);
            }
        }

        // Process payment
        $('#btn-process-payment').click(function() {
            const bayar = parseInt($('#total_bayar').val());
            const kembalian = bayar - totalAmount;
            const customerName = $('#customer_name').val() || '-';
            const tanggal = $('#tanggal_transaksi').val();

            if (cart.length === 0) {
                alert('Keranjang masih kosong');
                return;
            }

            if (bayar < totalAmount) {
                alert('Pembayaran kurang dari total belanja');
                return;
            }

            // Create transaction data
            const transactionData = {
                tanggal: tanggal,
                customer_name: customerName,
                total_amount: totalAmount,
                total_items: cart.length,
                payment_amount: bayar,
                change_amount: kembalian,
                items: cart
            };

            // Save transaction to database
            $.ajax({
                url: '{{ route('kasir.save-transaction') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    transaction: transactionData
                },
                success: function(response) {
                    // Display receipt
                    generateReceipt(response.invoice_number, transactionData);
                    $('#receiptModal').modal('show');

                    // Clear cart after successful transaction
                    resetTransaction();
                },
                error: function(xhr, status, error) {
                    alert('Terjadi kesalahan saat menyimpan transaksi: ' +
                        (xhr.responseJSON?.message || error));
                }
            });
        });

        // Generate receipt
        function generateReceipt(invoiceNumber, transaction) {
            $('#receipt-invoice').text(invoiceNumber);
            $('#receipt-date').text(formatDate(transaction.tanggal || new Date()));
            $('#receipt-customer').text(transaction.customer_name);

            const receiptItems = $('#receipt-items');
            receiptItems.empty();

            let itemsList = '';
            transaction.items.forEach(item => {
                itemsList += `
                    <div class="receipt-item">
                        <div class="receipt-item-name">${item.name} x ${item.quantity}</div>
                        <div class="receipt-item-price">Rp. ${formatRupiah(item.subtotal)}</div>
                    </div>
                `;
            });

            receiptItems.html(itemsList);

            $('#receipt-total-items').text(transaction.total_items);
            $('#receipt-total-amount').text(formatRupiah(transaction.total_amount));
            $('#receipt-cash').text(formatRupiah(transaction.payment_amount));
            $('#receipt-change').text(formatRupiah(transaction.change_amount));
        }

        // Format date
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        // Reset transaction button
        $('#btn-reset-transaction').click(function() {
            $('#resetModal').modal('show');
        });

        // Confirm reset transaction
        $('#btn-confirm-reset').click(function() {
            resetTransaction();
            $('#resetModal').modal('hide');
        });

        // Reset transaction helper
        function resetTransaction() {
            // Clear cart
            cart = [];
            totalAmount = 0;
            selectedProduct = null;

            // Reset form fields
            $('#barang_search').val(null).trigger('change');
            $('#customer_name').val('');
            $('#total_bayar').val('');
            $('#kembalian').text('0');
            $('#selected-product-info').hide();

            // Update display
            updateCartDisplay();
        }

        // Print receipt
        $('#btn-print-receipt').click(function() {
            window.print();
        });

        // Initialize cart display
        updateCartDisplay();
    });
</script>
