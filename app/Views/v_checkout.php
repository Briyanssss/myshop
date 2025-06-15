<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-6">
        <?= form_open('buy', 'class="row g-3"') ?>
        <?= form_hidden('username', session()->get('username')) ?>
        <?= form_input(['type' => 'hidden', 'name' => 'total_harga', 'id' => 'total_harga', 'value' => '']) ?>
        <div class="col-12">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" class="form-control" id="nama" value="<?= session()->get('username'); ?>" readonly>
        </div>
        <div class="col-12">
            <label for="alamat" class="form-label">Alamat Lengkap</label>
            <input type="text" class="form-control" id="alamat" name="alamat" placeholder="Masukkan alamat lengkap Anda" required>
        </div> 
        <div class="col-12">
            <label for="kelurahan" class="form-label">Cari Kelurahan/Kota Tujuan</label>
            <select class="form-control" id="kelurahan" name="kelurahan" required></select>
        </div>
        <div class="col-12">
            <label for="layanan" class="form-label">Pilih Layanan Pengiriman</label>
            <select class="form-control" id="layanan" name="layanan" required></select> 
        </div>
        <div class="col-12">
            <label for="ongkir" class="form-label">Ongkos Kirim</label>
            <input type="text" class="form-control" id="ongkir" name="ongkir" readonly>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="col-12">
            <h5>Ringkasan Pesanan</h5>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Nama</th>
                        <th scope="col">Harga</th>
                        <th scope="col">Jumlah</th>
                        <th scope="col">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)) : ?>
                        <?php foreach ($items as $item) : ?>
                            <tr>
                                <td><?= $item['name'] ?></td>
                                <td><?= number_to_currency($item['price'], 'IDR') ?></td>
                                <td><?= $item['qty'] ?></td>
                                <td><?= number_to_currency($item['price'] * $item['qty'], 'IDR') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <tr>
                        <td colspan="2"></td>
                        <td><strong>Subtotal</strong></td>
                        <td><strong><?= number_to_currency($total, 'IDR') ?></strong></td>
                    </tr>
                     <tr>
                        <td colspan="2"></td>
                        <td><strong>Ongkir</strong></td>
                        <td><strong id="ongkir-summary">IDR 0</strong></td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td><strong>Total</strong></td>
                        <td><strong id="total-summary"><?= number_to_currency($total, 'IDR') ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-primary">Buat Pesanan</button>
        </div>
        </form></div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
$(document).ready(function() {
    // Inisialisasi variabel
    let ongkir = 0;
    const subtotalProduk = <?= $total ?>; // Ambil subtotal dari PHP

    // Fungsi untuk memformat angka menjadi format mata uang Rupiah
    function formatRupiah(angka) {
        return "IDR " + angka.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
    }

    // Fungsi untuk menghitung dan memperbarui total
    function hitungTotal() {
        const totalHarga = subtotalProduk + ongkir;
        
        $("#ongkir").val(formatRupiah(ongkir));
        $("#ongkir-summary").html(formatRupiah(ongkir));
        $("#total-summary").html(formatRupiah(totalHarga));
        $("#total_harga").val(totalHarga);
    }
    
    // Inisialisasi Select2 untuk Kelurahan
    $('#kelurahan').select2({
        placeholder: 'Ketik nama kelurahan atau kota...',
        theme: "bootstrap-5", // Tema agar sesuai dengan Bootstrap 5
        ajax: {
            url: '<?= site_url('get-location') ?>',
            dataType: 'json',
            delay: 250, // Waktu tunggu sebelum mengirim request
            data: function (params) {
                return {
                    search: params.term // Kirim 'search' sebagai query parameter
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function(item) {
                        return {
                            id: item.id,
                            text: `${item.subdistrict_name}, ${item.city_name}, ${item.province_name}`
                        };
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 3
    });

    // Event handler ketika kelurahan diganti
    $("#kelurahan").on('change', function() {
        const id_kelurahan = $(this).val(); 
        
        $("#layanan").empty().append('<option selected disabled>Memuat layanan...</option>');
        ongkir = 0;
        hitungTotal();

        $.ajax({
            url: "<?= site_url('get-cost') ?>",
            type: 'GET',
            data: { 
                'destination': id_kelurahan, 
            },
            dataType: 'json',
            success: function(data) { 
                $("#layanan").empty().append('<option selected disabled>Pilih Layanan</option>');
                data.forEach(function(item) {
                    const text = `${item.description} (${item.service}) - Estimasi ${item.etd} hari`;
                    $("#layanan").append($('<option>', {
                        value: item.cost,
                        text: text 
                    }));
                });
            },
            error: function() {
                // Penanganan jika request gagal
                $("#layanan").empty().append('<option selected disabled>Gagal memuat layanan</option>');
                alert('Terjadi kesalahan saat mengambil data ongkos kirim. Silakan coba lagi.');
            }
        });
    });

    // Event handler ketika layanan pengiriman diganti
    $("#layanan").on('change', function() {
        ongkir = parseInt($(this).val()) || 0;
        hitungTotal();
    });
});
</script>
<?= $this->endSection() ?>