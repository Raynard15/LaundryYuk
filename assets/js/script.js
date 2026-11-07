/* LaundryKu - validasi & interaksi tambahan */
(function () {
    'use strict';

    function rupiah(angka) {
        return 'Rp ' + Math.round(angka).toLocaleString('id-ID');
    }

    /* ---------- Index: validasi pencarian resi ---------- */
    var formCari = document.getElementById('formCariResi');
    if (formCari) {
        var inputResi = document.getElementById('inputResi');
        var pesan = document.getElementById('pesanResi');

        inputResi.addEventListener('input', function () {
            inputResi.value = inputResi.value.toUpperCase().replace(/[^A-Z0-9-]/g, '');
            pesan.textContent = '';
        });

        formCari.addEventListener('submit', function (ev) {
            if (!/^RSI-[A-Z0-9]{4,}$/.test(inputResi.value.trim())) {
                ev.preventDefault();
                pesan.textContent = 'Format resi tidak valid. Contoh: RSI-A1B2C3';
                inputResi.focus();
            }
        });
    }

    /* ---------- Form tambah transaksi: hitung total otomatis ---------- */
    var layanan = document.getElementById('jenisLayanan');
    var berat = document.getElementById('beratKg');
    var totalEl = document.getElementById('totalHarga');

    function hitungTotal() {
        if (!layanan || !berat || !totalEl) { return; }
        var opt = layanan.options[layanan.selectedIndex];
        var harga = opt ? parseFloat(opt.getAttribute('data-harga')) || 0 : 0;
        var kg = parseFloat(String(berat.value).replace(',', '.')) || 0;
        totalEl.textContent = rupiah(harga * kg);
    }

    if (layanan && berat) {
        layanan.addEventListener('change', hitungTotal);
        berat.addEventListener('input', hitungTotal);
        hitungTotal();
    }

    /* ---------- Validasi form umum (form dengan atribut data-validate) ---------- */
    function tampilkanError(field, teks) {
        var label = field.closest('.field');
        if (!label) { return; }
        var lama = label.querySelector('.field-msg');
        if (lama) { lama.remove(); }
        label.classList.toggle('invalid', !!teks);
        if (teks) {
            var span = document.createElement('span');
            span.className = 'field-msg';
            span.textContent = teks;
            label.appendChild(span);
        }
    }

    document.querySelectorAll('form[data-validate]').forEach(function (form) {
        form.addEventListener('submit', function (ev) {
            var valid = true;
            form.querySelectorAll('[required]').forEach(function (el) {
                var kosong = !String(el.value).trim();
                var diBawahMin = el.type === 'number' && el.min !== '' && parseFloat(el.value) < parseFloat(el.min);
                if (kosong || diBawahMin) {
                    valid = false;
                    tampilkanError(el, kosong ? 'Kolom ini wajib diisi.' : 'Nilai minimal ' + el.min + '.');
                } else {
                    tampilkanError(el, '');
                }
            });
            if (!valid) {
                ev.preventDefault();
                var pertama = form.querySelector('.invalid input, .invalid select');
                if (pertama) { pertama.focus(); }
            }
        });
        form.addEventListener('input', function (ev) {
            if (ev.target.closest('.field')) { tampilkanError(ev.target, ''); }
        });
    });

    /* ---------- Dashboard: ubah status langsung submit ---------- */
    document.querySelectorAll('select[data-autosubmit]').forEach(function (sel) {
        var awal = sel.value;
        sel.addEventListener('change', function () {
            if (window.confirm('Ubah status cucian menjadi "' + sel.value + '"?')) {
                sel.form.submit();
            } else {
                sel.value = awal;
            }
        });
    });
})();
