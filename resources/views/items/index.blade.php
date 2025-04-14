<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1>Create Inventory Items</h1>
        @if (auth()->user()->is_admin)
            <form action="{{ route('items.store') }}" method="POST">
                @csrf
                <input type="text" name="name" placeholder="Item Name" required>
                <input type="text" name="description" placeholder="Description">
                <input type="number" name="quantity" placeholder="Quantity" required>
                <input type="number" name="price" placeholder="Price" step="0.01" required>
                <button type="submit">Add Item</button>
            </form>
        @endif

    <h1>Items</h1>
    @foreach ($items as $item)
        <h2>Item: $item->name</h2>
        <h2>Qty: $item->quantity</h2>
        @if (auth()->user()->is_admin)
                <form action="{{ route('items.destroy', $item) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Delete</button>
                </form>
            @endif
    @endforeach
</body>
</html>