(function () {
    if (!/^#apartado_(?:etapa_)?(?:[1-9]|1[0-2])$/.test(window.location.hash)) {
        return;
    }

    window.addEventListener('load', function () {
        const apartado = document.getElementById(window.location.hash.slice(1));
        if (!apartado) {
            return;
        }

        let pendiente = false;
        const observador = new MutationObserver(programaDesplazamiento);

        function programaDesplazamiento() {
            if (pendiente) {
                return;
            }
            pendiente = true;
            window.requestAnimationFrame(function () {
                pendiente = false;
                if (!apartado.parentElement.getClientRects().length) {
                    return;
                }
                if (!apartado.getClientRects().length) {
                    apartado.style.display = 'block';
                }
                observador.disconnect();
                apartado.scrollIntoView();
            });
        }

        observador.observe(document.body, {attributes: true, attributeFilter: ['class', 'style'], subtree: true});
        programaDesplazamiento();
    });
})();
