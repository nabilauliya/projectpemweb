<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
   public function index(Request $request)
{
    $query = Produk::query();

    // Pencarian berdasarkan nama produk
    if ($request->has('search')) {
        $search = $request->input('search');
        $query->where('namaProduk', 'like', '%' . $search . '%');
    }

    // Filter berdasarkan kategori
    if ($request->has('kategori')) {
        $kategori = $request->input('kategori');
        $query->whereIn('kategori', $kategori);
    }

    $produk = $query->get();

    return view('produk.index', compact('produk'));
}

    public function create()
    {
        return view('produk.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'namaProduk' => 'required|max:255',
            'stokProduk' => 'required|integer',
            'fotoProduk' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'kategori' => 'required|in:baju,celana,aksesoris',
        ]);

        $path = $request->file('fotoProduk')->store('public/images');

        Produk::create([
            'namaProduk' => $request->namaProduk,
            'stokProduk' => $request->stokProduk,
            'fotoProduk' => $path,
            'kategori' => $request->kategori,
        ]);

        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit(Produk $produk)
    {
        return view('produk.edit', compact('produk'));
    }

     public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);
        $produk->stokProduk = $request->input('stokProduk');
        $produk->save();

        return redirect()->route('produk.index')->with('success', 'Stok produk berhasil diperbarui');
    }

    // Method to delete the product
    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);
        $produk->delete();

        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus');
    }
    public function search()
    {
        $produk = Produk::all();
        return view('produk.index', compact('produk'));
    }
}
