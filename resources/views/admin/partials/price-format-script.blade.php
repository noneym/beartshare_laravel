<script>
document.addEventListener('DOMContentLoaded', function () {
    // Türkçe biçim: 1.250.000,50 (nokta binlik, virgül ondalık). Yazarken karakter silinmez,
    // alandan çıkınca biçimlenir; sunucuya gizli alandaki sayı (1250000.5) gider.
    const parse = function (v) {
        v = (v || '').replace(/[^\d.,]/g, '');
        if (v.includes(',')) {
            v = v.replace(/\./g, '').replace(',', '.').replace(/,/g, '');
        } else if (!/^\d+\.\d{1,2}$/.test(v)) {
            v = v.replace(/\./g, '');
        }
        const n = parseFloat(v);
        return isNaN(n) ? null : Math.round(n * 100) / 100;
    };
    const fmt = n => n.toLocaleString('tr-TR', { minimumFractionDigits: 0, maximumFractionDigits: 2 });

    document.querySelectorAll('[data-price-format]').forEach(function (input) {
        const hidden = input.parentElement.querySelector('input[type="hidden"]');
        const sync = function () {
            const n = parse(input.value);
            if (hidden) hidden.value = n === null ? '' : n;
            return n;
        };
        input.addEventListener('input', function () {
            input.value = input.value.replace(/[^\d.,]/g, '');
            sync();
        });
        input.addEventListener('blur', function () {
            const n = sync();
            if (n !== null) input.value = fmt(n);
        });
        if (input.form) input.form.addEventListener('submit', sync);

        const n0 = parse(hidden && hidden.value !== '' ? String(hidden.value) : input.value);
        if (n0 !== null) input.value = fmt(n0);
    });
});
</script>
