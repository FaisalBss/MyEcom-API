@extends('Layouts.master')

@section('content')

<div class="product-section mt-150 mb-150">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2 text-center">
                <div class="section-title">
                    <h3><span class="orange-text">Products</span></h3>
                    <p>Each product has excellent quality</p>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6 offset-md-3">
                <form id="search-form" class="input-group">
                    <input type="text" id="search-key" class="form-control" placeholder="Search by name, ID or category...">
                    <button class="btn btn-orange" type="submit">Search</button>
                </form>
            </div>
        </div>

        <div class="row" id="product-list">
        </div>

        <div id="pagination" class="d-flex justify-content-center mt-3"></div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    let currentPage = 1;
    let searchKey = '';

    function fetchProducts(page = 1, search = '') {
        fetch(`/api/products?page=${page}&search=${search}`)
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    let container = document.getElementById('product-list');
                    let pagination = document.getElementById('pagination');
                    container.innerHTML = '';
                    pagination.innerHTML = '';

                    response.data.data.forEach(item => {
                        container.innerHTML += `
                            <div class="col-lg-4 col-md-6 text-center">
                                <div class="single-product-item">
                                    <div class="product-image">
                                        <a href="/single-product/${item.id}">
                                            <img src="/${item.image}"
                                                 style="max-height:200px!important;min-height:200px!important;"
                                                 alt="${item.name}">
                                        </a>
                                    </div>
                                    <h3>${item.name}</h3>
                                    <p class="product-price">
                                        <span>${item.quantity}</span> ${item.price} $
                                    </p>
                                    @auth
                                        <form action="/cart/add/${item.id}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-orange">
                                                <i class="fas fa-shopping-cart"></i> Add to Cart
                                            </button>
                                        </form>
                                    @else
                                        <a href="/login" class="btn btn-orange">
                                            <i class="fas fa-shopping-cart"></i> Add to Cart
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        `;
                    });

                    for (let i = 1; i <= response.data.last_page; i++) {
                        pagination.innerHTML += `
                            <button class="btn ${i === response.data.current_page ? 'btn-orange' : 'btn-light'} mx-1"
                                    onclick="fetchProducts(${i}, '${searchKey}')">${i}</button>
                        `;
                    }
                }
            })
            .catch(error => console.error('Error fetching products:', error));
    }

    document.getElementById('search-form').addEventListener('submit', function(e) {
        e.preventDefault();
        searchKey = document.getElementById('search-key').value;
        fetchProducts(1, searchKey);
    });

    fetchProducts();
});
</script>

@endsection

<style>
    svg {
        width: 50px;
        height: 50px;
    }
</style>
