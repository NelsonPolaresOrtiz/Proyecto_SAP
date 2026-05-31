function switchView(viewName, element) {
    document.querySelectorAll('.view-section').forEach(v => v.classList.remove('active'));
    document.querySelectorAll('.menu-item').forEach(m => m.classList.remove('active'));

    const target = document.getElementById('view-' + viewName);
    if(target) target.classList.add('active');
    element.classList.add('active');

    const searchInput = document.getElementById('global-search');
    const placeholders = {
        'inicio': "Buscar procesos, transacciones...",
        'mm': "Buscar productos, stock, proveedores...",
        'fico': "Buscar asientos contables, facturas...",
        'sd': "Buscar cotizaciones, pedidos, clientes...",
        'crm': "Buscar en el historial de clientes...",
        'pp': "Buscar órdenes de fabricación, lotes...",
        'qm': "Buscar lotes en inspección, auditorías..."
    };
    searchInput.placeholder = placeholders[viewName] || "Buscar...";

    if(viewName === 'mm') cargarTablaInventario();
    if(viewName === 'fico') cargarAsientosContables();
    if(viewName === 'sd') cargarHistorialPedidos();
    if(viewName === 'pp') cargarHistorialProduccion();
    if(viewName === 'qm') cargarHistorialCalidad();
}

function filtrarContenido() {
    const textoBusqueda = document.getElementById('global-search').value.toLowerCase();
    
    const tablaInventario = document.querySelector('#inventario-dinamico-body');
    if (tablaInventario) {
        const filas = tablaInventario.getElementsByTagName('tr');
        for (let i = 0; i < filas.length; i++) {
            const textoFila = filas[i].textContent.toLowerCase();
            filas[i].style.display = textoFila.includes(textoBusqueda) ? '' : 'none';
        }
    }

    const historialPedidos = document.getElementById('historial-pedidos-ajax');
    if (historialPedidos) {
        const bloques = historialPedidos.children;
        for (let i = 0; i < bloques.length; i++) {
            const textoBloque = bloques[i].textContent.toLowerCase();
            bloques[i].style.display = textoBloque.includes(textoBusqueda) ? '' : 'none';
        }
    }
}

function cargarTablaInventario() {
    fetch('obtener_inventario.php')
    .then(res => res.text())
    .then(html => {
        const target = document.getElementById('inventario-dinamico-body');
        if(target) target.innerHTML = html;
    });
}

function cargarHistorialPedidos() {
    fetch('obtener_pedidos.php')
    .then(res => res.text())
    .then(html => {
        const target = document.getElementById('historial-pedidos-ajax');
        if(target) target.innerHTML = html;
    });
}

function cargarAsientosContables() {
    fetch('obtener_asientos.php')
    .then(res => res.text())
    .then(html => {
        const target = document.getElementById('finanzas-dinamicas-body');
        if(target) target.innerHTML = html;
    });
}

function cargarHistorialProduccion() {
    fetch('obtener_produccion.php')
    .then(res => res.text())
    .then(html => {
        const target = document.getElementById('historial-produccion-ajax');
        if(target) target.innerHTML = html;
    });
}

function cargarHistorialCalidad() {
    fetch('obtener_calidad.php')
    .then(res => res.text())
    .then(html => {
        const target = document.getElementById('inspecciones-calidad-ajax');
        if(target) target.innerHTML = html;
    });
}

// NUEVA FUNCIÓN: Procesa la decisión de empleo en el lote QM
function procesarLoteQM(loteId, decision) {
    if(decision === 'Aprobado') {
        lanzarToastERP(`✓ Lote ${loteId}: Conforme. Material liberado para libre utilización.`);
    } else {
        const wrapper = document.getElementById('toast-wrapper');
        if(wrapper) {
            const toast = document.createElement('div');
            toast.className = 'toast-sap';
            toast.style.borderLeftColor = 'var(--alerta-critico)';
            toast.innerHTML = `<span>⚠️</span> <span>Lote ${loteId}: Retenido por desviación de calidad. Stock bloqueado.</span>`;
            wrapper.appendChild(toast);
            setTimeout(() => toast.remove(), 3500);
        }
    }
}

function exportarBalancePDF() {
    const elementoReporte = document.getElementById('reporte-financiero');
    const membretePdf = document.querySelector('.pdf-only-header');
    if (!elementoReporte) return;

    if(membretePdf) membretePdf.style.display = 'block';

    const opciones = {
        margin:       15,
        filename:     'Reporte_Contable_FICO_ERPSystem.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, logging: false, useCORS: true },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    lanzarToastERP("📊 Procesando balance y generando PDF...");

    html2pdf().set(opciones).from(elementoReporte).save().then(() => {
        if(membretePdf) membretePdf.style.display = 'none';
        lanzarToastERP("🟢 Reporte contable descargado con éxito.");
    });
}

function actualizarPrevisualizacion() {
    const select = document.getElementById('select-producto');
    const cantidad = parseInt(document.getElementById('input-cantidad').value) || 0;
    const descuento = parseInt(document.getElementById('input-descuento').value) || 0;
    
    const box = document.getElementById('preview-orden');
    const texto = document.getElementById('preview-texto');
    const descuentoTxt = document.getElementById('valor-descuento-txt');

    if(descuentoTxt) descuentoTxt.innerText = descuento + "%";

    if(select.value !== "") {
        const stock = parseInt(select.options[select.selectedIndex].getAttribute('data-stock'));
        box.style.display = "block";
        if(cantidad > stock) {
            texto.innerHTML = "<span style='color:var(--alerta-critico)'>❌ Error SAP: Stock insuficiente en inventario MM.</span>";
        } else {
            const precioBaseTotal = cantidad * 150;
            const reduccion = (precioBaseTotal * descuento) / 100;
            const precioFinal = precioBaseTotal - reduccion;

            texto.innerHTML = `
                <div style="color: var(--exito-ia); margin-bottom: 5px;">🟢 Validación Exitosa de Material.</div>
                <div style="font-size:12px; color: var(--texto-principal); font-weight: normal; margin-top:8px;">
                    <b>Detalle de Facturación:</b><br>
                    • Subtotal Bruto: $${precioBaseTotal.toLocaleString()} USD<br>
                    • Margen Deducido (${descuento}%): -$${reduccion.toLocaleString()} USD<br>
                    <hr style="margin:5px 0; border:0; border-top:1px solid #CBD5E1;">
                    <span style="font-size:14px; color:var(--sidebar-activo);"><b>Monto Neto Total: $${precioFinal.toLocaleString()} USD</b></span>
                </div>
            `;
        }
    } else {
        box.style.display = "none";
    }
}

document.getElementById('form-nuevo-pedido')?.addEventListener('submit', function(e) {
    e.preventDefault();
    fetch('guardar_pedido.php', { method: 'POST', body: new FormData(this) })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            lanzarToastERP("🟢 Orden registrada correctamente en el Mandante SD.");
            this.reset();
            actualizarPrevisualizacion();
            cargarHistorialPedidos();
        } else {
            alert('Error en validación de transacciones: ' + data.message);
        }
    });
});

document.getElementById('form-nueva-produccion')?.addEventListener('submit', function(e) {
    e.preventDefault();
    fetch('guardar_produccion.php', { method: 'POST', body: new FormData(this) })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            lanzarToastERP("⚙️ Lote de fabricación liberado. Materia prima descontada.");
            this.reset();
            cargarHistorialProduccion();
        } else {
            alert('Error en producción: ' + data.message);
        }
    });
});

function lanzarToastERP(mensaje) {
    const wrapper = document.getElementById('toast-wrapper');
    if(!wrapper) return;

    const toast = document.createElement('div');
    toast.className = 'toast-sap';
    toast.innerHTML = `<span>⚙️</span> <span>${mensaje}</span>`;
    
    wrapper.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = "slideIn 0.3s ease-out reverse forwards";
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}

function inicializarGraficos() {
    const ctxRendimiento = document.getElementById('chartRendimiento');
    if (ctxRendimiento) {
        new Chart(ctxRendimiento, {
            type: 'bar',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
                datasets: [{
                    label: 'Ingresos Operativos ($ USD)',
                    data: [310000, 340000, 390000, 420000, 440000, ERP_INGRESOS_TOTALES],
                    backgroundColor: '#0A2540',
                    borderWidth: 0,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true }, x: { grid: { display: false } } },
                plugins: { legend: { display: false } }
            }
        });
    }

    const ctxAlmacen = document.getElementById('chartAlmacen');
    if (ctxAlmacen) {
        new Chart(ctxAlmacen, {
            type: 'doughnut',
            data: {
                labels: ['Ocupado', 'Disponible'],
                datasets: [{
                    data: [82, 18],
                    backgroundColor: ['#1E3A8A', '#E2E8F0'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } },
                cutout: '75%'
            }
        });
    }
}

function toggleFullScreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
    } else {
        document.exitFullscreen();
    }
}

document.addEventListener("DOMContentLoaded", () => {
    inicializarGraficos();
    cargarHistorialPedidos();
});