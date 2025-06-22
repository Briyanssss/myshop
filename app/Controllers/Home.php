<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\TransactionModel;
use App\Models\TransactionDetailModel;

class Home extends BaseController
{
    protected $product;
    protected $transaction;
    protected $transaction_detail;

    public function __construct()
    {
        helper('form');
        helper('number');

        // Inisialisasi setiap model ke propertinya masing-masing
        $this->product            = new ProductModel();
        $this->transaction        = new TransactionModel();
        $this->transaction_detail = new TransactionDetailModel();
    }

    public function index(): string
    {
        // $this->product di sini sudah benar merujuk ke ProductModel
        $product = $this->product->findAll();
        $data['product'] = $product;

        return view('v_home', $data);
    }

    public function profile()
    {
        $username = session()->get('username');
        $data['username'] = $username;

        // Sekarang $this->transaction dan $this->transaction_detail tidak akan null lagi
        $buy = $this->transaction->where('username', $username)->findAll();
        $data['buy'] = $buy;

        $product = [];

        if (!empty($buy)) {
            foreach ($buy as $item) {
                $detail = $this->transaction_detail
                               ->select('transaction_detail.*, product.nama, product.harga, product.foto')
                               ->join('product', 'transaction_detail.product_id=product.id')
                               ->where('transaction_id', $item['id'])
                               ->findAll();

                if (!empty($detail)) {
                    $product[$item['id']] = $detail;
                }
            }
        }

        $data['product'] = $product;

        return view('v_profile', $data);
    }
}