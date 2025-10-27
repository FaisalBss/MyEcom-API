@extends('Layouts.master')

@section('content')

<div class="product-section mt-150 mb-150">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2 text-center">
                <div class="section-title">
                    <h3><span class="orange-text">Categories</span></h3>
                    <p>Each category has exciting products</p>
                </div>
            </div>
        </div>

        <div class="row" id="category-list">
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    fetch('/api/home/categories')
        .then(res => res.json())
        .then(response => {
            if (response.success) {
                let container = document.getElementById('category-list');
                response.data.data.forEach(item => {
                    container.innerHTML += `
                        <div class="col-lg-4 col-md-6 text-center">
                            <div class="single-product-item">
                                <div class="product-image">
                                    <a href="/product/${item.id}">
                                        <img src="/storage/${item.image ?? ''}"
                                             style="max-height:200px!important;min-height:200px!important;"
                                             alt="${item.name}">
                                    </a>
                                </div>
                                <h3>${item.name}</h3>
                            </div>
                        </div>
                    `;
                });
            }
        })
        .catch(error => console.error('Error fetching categories:', error));
});
</script>

@endsection
