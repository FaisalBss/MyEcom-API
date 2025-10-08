@extends('Layouts.master')

@section('content')
<div class="container my-5">
    <h2 class="mb-4">My Orders</h2>
    <div class="table-responsive">
        <table class="table table-bordered align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>Order ID</th>
                    <th>Products</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody id="orders-list">
            </tbody>
        </table>
    </div>

    <div id="pagination" class="d-flex justify-content-center mt-3"></div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    let currentPage = 1;

    function fetchOrders(page = 1) {
        fetch(`/api/orders?page=${page}`)
            .then(res => res.json())
            .then(response => {
                let container = document.getElementById('orders-list');
                let pagination = document.getElementById('pagination');
                container.innerHTML = '';
                pagination.innerHTML = '';

                if (response.success && response.orders.data.length > 0) {
                    response.orders.data.forEach(order => {
                        let productsHtml = '';
                        order.items.forEach(item => {
                            productsHtml += `${item.product.name} (x${item.quantity}) <br>`;
                        });

                        container.innerHTML += `
                            <tr>
                                <td>${order.id}</td>
                                <td>${productsHtml}</td>
                                <td><strong>${order.total ?? order.total_amount}</strong></td>
                                <td>${order.status.charAt(0).toUpperCase() + order.status.slice(1)}</td>
                                <td>${new Date(order.created_at).toLocaleString()}</td>
                            </tr>
                        `;
                    });

                    for (let i = 1; i <= response.orders.last_page; i++) {
                        pagination.innerHTML += `
                            <button class="btn ${i === response.orders.current_page ? 'btn-orange' : 'btn-light'} mx-1"
                                    onclick="fetchOrders(${i})">${i}</button>
                        `;
                    }
                } else {
                    container.innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center text-muted">You have no orders yet.</td>
                        </tr>
                    `;
                }
            })
            .catch(error => console.error('Error fetching orders:', error));
    }

    fetchOrders();
});
</script>
@endsection
