const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const test = require('node:test');
const vm = require('node:vm');

const script = fs.readFileSync(path.join(__dirname, '../../js/retorno_apartado.js'), 'utf8');

function inicia(hash, padreVisible, apartadoVisible) {
    let load;
    let observador;
    let desplazamientos = 0;
    const apartado = {
        parentElement: {getClientRects: () => padreVisible ? [1] : []},
        getClientRects: () => apartadoVisible || apartado.style.display === 'block' ? [1] : [],
        style: {},
        scrollIntoView: () => desplazamientos++,
    };
    class MutationObserver {
        constructor(callback) {
            observador = this;
            this.callback = callback;
            this.activo = false;
        }
        observe() { this.activo = true; }
        disconnect() { this.activo = false; }
    }
    const window = {
        location: {hash},
        addEventListener: (evento, callback) => { if (evento === 'load') load = callback; },
        requestAnimationFrame: callback => callback(),
    };
    vm.runInNewContext(script, {
        window,
        document: {body: {}, getElementById: () => apartado},
        MutationObserver,
    });
    return {
        cargar: () => load?.(),
        mostrarPadre: () => { padreVisible = true; if (observador?.activo) observador.callback(); },
        get apartado() { return apartado; },
        get desplazamientos() { return desplazamientos; },
        get observador() { return observador; },
    };
}

test('desplaza y abre el apartado solicitado', () => {
    const vista = inicia('#apartado_8', true, false);
    vista.cargar();
    assert.equal(vista.apartado.style.display, 'block');
    assert.equal(vista.desplazamientos, 1);
    assert.equal(vista.observador.activo, false);
});

test('espera a que se muestre la pestaña de etapa', () => {
    const vista = inicia('#apartado_etapa_3', false, true);
    vista.cargar();
    assert.equal(vista.desplazamientos, 0);
    vista.mostrarPadre();
    assert.equal(vista.desplazamientos, 1);
    assert.equal(vista.observador.activo, false);
});

test('ignora anclas ajenas a los apartados', () => {
    const vista = inicia('#otro', true, true);
    vista.cargar();
    assert.equal(vista.desplazamientos, 0);
    assert.equal(vista.observador, undefined);
});
