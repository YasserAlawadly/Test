<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title>Products</title>
</head>
<body>

<div class="container mt-5">

    @if(Session::has('status'))
        <p class="alert alert-info">{{ Session::get('status') }}</p>
    @endif

    <div class="alert alert-danger errors d-none"></div>

    <form method="post" action="" class="add_product">
        <div class="mb-3">
            <label for="exampleInputEmail1" class="form-label">Product name</label>
            <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"
                   name="product_name">
        </div>
        <div class="mb-3">
            <label for="exampleInputNumber1" class="form-label">Quantity in stock</label>
            <input type="number" class="form-control" id="exampleInputNumber1" name="quantity">
        </div>
        <div class="mb-3">
            <label for="exampleInputNumber2" class="form-label">Price per item</label>
            <input type="number" class="form-control" id="exampleInputNumber2" name="price">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <table class="table text-center mx-auto py-3 mt-5">

        <thead>
        <th>index</th>
        <th>Product Name</th>
        <th>Quantity in stock</th>
        <th>Price per item</th>
        <th>Datetime submitted</th>
        <th>Total value number</th>
        <th>update</th>
        </thead>

        <tbody id="tableBody">
        @forelse($products as $key => $product)
            <tr>
                <td>{{ $key }}</td>
                <td>{{$product['product_name']}}</td>
                <td>{{$product['quantity']}}</td>
                <td>{{$product['price']}}</td>
                <td>{{$product['date']}}</td>
                <td>{{$product['price'] * $product['quantity']}}</td>
                <td>
                    <button type="button" class="btn btn-outline-warning edit" data-toggle="modal"
                            data-target="#editModal" data-id="{{ $key }}">update
                    </button>
                </td>
            </tr>
        @empty
        @endforelse
        </tbody>

    </table>

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit product details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger edit-errors d-none"></div>

                    <form method="post" class="edit_product">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="exampleInputEmail1" class="form-label">Product name</label>
                            <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"
                                   name="product_name" required>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputNumber1" class="form-label">Quantity in stock</label>
                            <input type="number" class="form-control" id="exampleInputNumber1" name="quantity" required>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputNumber2" class="form-label">Price per item</label>
                            <input type="number" class="form-control" id="exampleInputNumber2" name="price" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


</div>


<script src="{{ asset('js/app.js') }}"></script>


<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('body').on('submit', '.add_product', function (e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajax({
                type: "POST",
                url: "{{ route('product.store') }}",
                data: formData,
                processData: false,
                contentType: false,
                success(response) {
                    console.log(response)
                    if (response.status) {
                        $('.errors').addClass('d-none');
                        let cartoona = ``;
                        for (var i = 0; i < response.data.length; i++) {
                            cartoona = `<tr>
                            <td>${i}</td>
                            <td>${response.data[i].product_name}</td>
                            <td>${response.data[i].quantity}</td>
                            <td>${response.data[i].price}</td>
                            <td>${response.data[i].date}</td>
                            <td>${response.data[i].price * response.data[i].quantity}</td>
                            <td> <button type="button" class="btn btn-outline-warning edit" data-toggle="modal"
                            data-target="#editModal" data-id="${i}">update</button></td>
                        </tr>`;
                        }
                        $('#tableBody').append(cartoona);

                    } else {
                        $('.errors').removeClass('d-none').empty();
                        $('.errors').append(`<ul>`)
                        response.errors.forEach((error) => {
                            $('.errors').append(`<li>${error[0]}</li>`)
                        })
                        $('.errors').append(`</ul>`)
                    }
                },
                error(error) {
                    console.log('error', error)
                }
            });
        })

        $('body').on('click', '.edit', function () {
            let url = "{{url('/')}}"
            let id = $(this).data('id');
            $.get('product/' + id + '/edit', function (response) {
                let product = response.data;
                $('#editModal').find('form').attr('action', `${url}/product/${id}`)
                $('#editModal').find('input[name=product_name]').val(product.product_name)
                $('#editModal').find('input[name=price]').val(product.price)
                $('#editModal').find('input[name=quantity]').val(product.quantity)
                $('#editModal').modal({
                    show: true
                });
            });
        });

    });
</script>
</body>
</html>
