(function ($) {
    function formatRupiah(value) {
        value = String(value).replace(/\D/g, "");

        if (value === "") {
            return "";
        }

        return value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function initRupiahInput() {
        $(".rupiah").each(function () {
            let value = $(this).val();

            if (value) {
                $(this).val(formatRupiah(value));
            }
        });

        $(document).on("input", ".rupiah", function () {
            $(this).val(formatRupiah($(this).val()));
        });

        $(document).on("submit", "form", function () {
            $(".rupiah").each(function () {
                $(this).val($(this).val().replace(/\./g, ""));
            });
        });
    }

    $(document).ready(function () {
        initRupiahInput();
    });
})(jQuery);
