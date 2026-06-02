@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Buat Order</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
                        @csrf

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Validasi Gagal!</strong>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="product_id" class="form-label">Nama Product <span class="text-danger">*</span></label>
                            <select class="form-control @error('product_id') is-invalid @enderror" id="product_id" name="product_id" required onchange="updateProductInfo()">
                                <option value="">-- Pilih Product --</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" data-quantity="{{ $product->quantity }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} (Tersedia: {{ $product->quantity }})
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="materialsInfo" style="display:none;">
                            <div class="alert alert-info">
                                <strong><i class="fas fa-info-circle"></i> Material yang Digunakan:</strong>
                                <div id="materialsList" class="mt-2"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity Order <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', 1) }}" min="1" max="0" required onchange="updateMaterialDeductions()">
                            <small class="form-text text-muted">Max quantity: <span id="maxQty">0</span></small>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="deductionInfo" style="display:none;">
                            <div class="alert alert-warning">
                                <strong><i class="fas fa-arrow-down"></i> Pengurangan Material:</strong>
                                <div id="deductionsList" class="mt-2"></div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-success">Buat Order</button>
                            <a href="{{ route('orders.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const productData = @json($products->keyBy('id'));

    function updateProductInfo() {
        const select = document.getElementById('product_id');
        const selectedId = select.value;
        const materialsInfo = document.getElementById('materialsInfo');
        const materialsList = document.getElementById('materialsList');
        const deductionInfo = document.getElementById('deductionInfo');

        if (!selectedId) {
            materialsInfo.style.display = 'none';
            deductionInfo.style.display = 'none';
            return;
        }

        // Fetch product with materials
        fetch(`/api/products/${selectedId}/materials`)
            .then(response => response.json())
            .then(data => {
                let html = '<ul class="mb-0">';
                data.materials.forEach(material => {
                    html += `<li>${material.name} (Tersedia: ${material.quantity}, Dibutuhkan: ${material.quantity_needed} per unit)</li>`;
                });
                html += '</ul>';
                materialsList.innerHTML = html;
                materialsInfo.style.display = 'block';
                updateMaterialDeductions();
            })
            .catch(error => {
                console.error('Error:', error);
                materialsInfo.style.display = 'none';
            });

        const selectedOption = select.options[select.selectedIndex];
        const maxQty = selectedOption.getAttribute('data-quantity') || 0;
        document.getElementById('maxQty').textContent = maxQty;
        document.getElementById('quantity').max = maxQty;
        document.getElementById('quantity').value = 1;
    }

    function updateMaterialDeductions() {
        const select = document.getElementById('product_id');
        const quantity = parseInt(document.getElementById('quantity').value) || 0;
        const selectedId = select.value;
        const deductionInfo = document.getElementById('deductionInfo');
        const deductionsList = document.getElementById('deductionsList');

        if (!selectedId || quantity <= 0) {
            deductionInfo.style.display = 'none';
            return;
        }

        fetch(`/api/products/${selectedId}/materials`)
            .then(response => response.json())
            .then(data => {
                let html = '<ul class="mb-0" style="color: #856404;">';
                data.materials.forEach(material => {
                    const deductQuantity = material.quantity_needed * quantity;
                    html += `<li>${material.name}: ${material.quantity} - ${deductQuantity} = <strong>${material.quantity - deductQuantity}</strong></li>`;
                });
                html += '</ul>';
                deductionsList.innerHTML = html;
                deductionInfo.style.display = 'block';
            })
            .catch(error => {
                console.error('Error:', error);
                deductionInfo.style.display = 'none';
            });
    }

    // Call on page load if product is already selected
    window.addEventListener('load', updateProductInfo);
</script>
@endsection
