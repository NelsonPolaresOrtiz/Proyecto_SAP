// --- 1. CONTROLADOR DE NAVEGACIÓN MULTINIVEL (ESTILO SAP) ---

// Manejo colapsable de los menús laterales principales
function toggleSubMenu(menuId, element) {
    // Cerrar de forma limpia cualquier otro submenú abierto para optimizar espacio
    document.querySelectorAll('.sub-menu').forEach(menu => {
        if(menu.id !== menuId) menu.classList.remove('open');
    });
    document.querySelectorAll('.menu-item').forEach(item => {
        if(item !== element) item.classList.remove('active');
    });

    const targetMenu = document.getElementById(menuId);
    if(targetMenu) {
        targetMenu.classList.toggle('open');
        element.classList.add('active');
        
        // Simulación: Hace clic de forma automática en el primer submódulo al desplegar el padre
        const primerSubItem = targetMenu.querySelector('.sub-item');
        if(primerSubItem) primerSubItem.click();
    }
}

// Alternancia exclusiva para los botones raíz sin submódulos (como el Dashboard)
function switchMainView(viewName, element) {
    document.querySelectorAll('.sub-menu').forEach(m => m.classList.remove('open'));
    document.querySelectorAll('.menu-item').forEach(m => m.classList.remove('active'));
    document.querySelectorAll('.view-section').forEach(v => v.classList.remove('active'));

    const target = document.getElementById('view-' + viewName);
    if(target) target.classList.add('active');
    element.classList.add('active');

    // Reset dinámico de la barra de búsqueda universal
    document.getElementById('global-search').placeholder = "Buscar procesos, transacciones...";
}

// CONTROLADOR DE SUB-VISTAS: Sincroniza las sub-pestañas internas densas sin recargar
function switchSubView(mainViewId, subViewId, element) {
    // Desactivar sub-ítems hermanos del bloque actual
    const parentMenu = element.parentElement;
    parentMenu.querySelectorAll('.sub-item').forEach(item => item.classList.remove('active'));
    element.classList.add('active');

    // Encender la sección del módulo principal
    document.querySelectorAll('.view-section').forEach(v => v.classList.remove('active'));
    const mainView = document.getElementById('view-' + mainViewId);
    if(mainView) mainView.classList.add('active');

    // Encender la sub-vista técnica seleccionada
    mainView.querySelectorAll('.sub-view-section').forEach(sv => sv.classList.remove('active'));
    const targetSubView = document.getElementById('subview-' + subViewId);
    if(targetSubView) targetSubView.classList.add('active');

    // Cambiar placeholders adaptativos en el Header según el submódulo actual
    const searchInput = document.getElementById('global-search');
    const placeholders = {
        'mm-stock': "Buscar materiales o existencias en stock...",
        'mm-purch': "Buscar proveedores homologados o contratos...",
        'fico-gl': "Buscar asientos en el Libro Mayor contable...",
        'fico-ap': "Buscar facturas y pasivos pendientes...",
        'sd-sales': "Buscar cotizaciones u órdenes de venta...",
        'sd-pricing': "Buscar condiciones y listas de precios...",
        'crm-analytics': "Buscar en el historial predictivo de clientes...",
        'pp-order': "Buscar lotes o la planeación en planta...",
        'qm-insp': "Buscar muestreos o auditorías de calidad..."
    };
    searchInput.placeholder = placeholders[subViewId] || "Buscar...";

    // Disparadores automáticos de llamadas asíncronas (AJAX) en caliente
    if(subViewId === 'mm-stock') cargarTablaInventario();
    if(subViewId === 'fico-gl') cargarAsientosContables();
    if(subViewId === 'sd-sales') { cargarHistorialPedidos(); actualizarPrevisualizacion(); }
    if(subViewId === 'pp-order') cargarHistorialProduccion();
    if(subViewId === 'qm-insp') cargarHistorialCalidad();
}

// --- 2. MOTOR DE FILTRADO REACTIVO EN TIEMPO REAL ---
function filtrarContenido() {
    const textoBusqueda = document.getElementById('global-search').value.toLowerCase();
    
    // 1. Filtrado dinámico de filas para las tablas del almacén (MM) o libro mayor (FICO)
    const tablaInventario = document.querySelector('#inventario-dinamico-body');
    if (tablaInventario) {
        const filas = tablaInventario.getElementsByTagName('tr');
        for (let i = 0; i < filas.length; i++) {
            const textoFila = filas[i].textContent.toLowerCase();
            filas[i].style.display = textoFila.includes(textoBusqueda) ? '' : 'none';
        }
    }

    // 2. Filtrado dinámico de bloques de historial comercial lateral (SD)
    const historialPedidos = document.getElementById('historial-pedidos-ajax');
    if (historialPedidos) {
        const bloques = historialPedidos.children;
        for (let i = 0; i < bloques.length; i++) {
            const textoBloque = bloques[i].textContent.toLowerCase();
            bloques[i].style.display = textoBloque.includes(textoBusqueda) ? '' : 'none';
        }
    }
}

// --- 3. PETICIONES ASÍNCRONAS DE CARGA EN SEGUNDO PLANO (AJAX) ---
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

// --- 4. CONTROLADOR INTEGRADO DEL MÓDULO QM (PROCESADOR EN VIVO) ---
function procesarLoteQM(loteId, decision, productoId, cantidad) {
    if(loteId === "SYS01") {
        if(decision === 'Aprobado') lanzarToastERP("✓ QM Confirmación: Lote SYS01 liberado de forma estándar.");
        else lanzarToastERP("⚠️ Lote SYS01 retenido preventivamente por auditoría.");
        return;
    }

    const formData = new FormData();
    formData.append('producto_id', productoId);
    formData.append('cantidad', cantidad);
    formData.append('decision', decision);

    fetch('guardar_calidad.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            if(data.decision === 'Aprobado') {
                lanzarToastERP(`✓ QM: Lote #${loteId} CONFORME. ${cantidad} un. añadidas al stock utilizable.`);
            } else {
                const wrapper = document.getElementById('toast-wrapper');
                if(wrapper) {
                    const toast = document.createElement('div');
                    toast.className = 'toast-sap';
                    toast.style.borderLeftColor = 'var(--alerta-critico)';
                    toast.innerHTML = `<span>⚠️</span> <span>QM: Lote #${loteId} RECHAZADO por fallas de tolerancia. Stock bloqueado.</span>`;
                    wrapper.appendChild(toast);
                    setTimeout(() => toast.remove(), 3500);
                }
            }
            cargarHistorialCalidad();
        } else {
            alert('Error en el control de calidad QM: ' + data.message);
        }
    });
}

// --- 5. EXPORTADOR FISCAL A FORMATO PDF (MÓDULO FICO) ---
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

// --- 6. SIMULADOR DE PRICING INTERACTIVO EN CALIENTE (MÓDULO SD) ---
function actualizarPrevisualizacion() {
    const select = document.getElementById('select-producto');
    const cantidad = parseInt(document.getElementById('input-cantidad').value) || 0;
    const descuento = parseInt(document.getElementById('input-descuento').value) || 0;
    
    const box = document.getElementById('preview-orden');
    const texto = document.getElementById('preview-texto');
    const descuentoTxt = document.getElementById('valor-descuento-txt');

    if(descuentoTxt) descuentoTxt.innerText = descuento + "%";

    if(select && select.value !== "") {
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
                    <b>Detalle de Facturación Simulación:</b><br>
                    • Subtotal Bruto: $${precioBaseTotal.toLocaleString()} USD<br>
                    • Margen Deducido (${descuento}%): -$${reduccion.toLocaleString()} USD<br>
                    <hr style="margin:5px 0; border:0; border-top:1px solid #CBD5E1;">
                    <span style="font-size:14px; color:var(--sidebar-activo);"><b>Monto Neto Total: $${precioFinal.toLocaleString()} USD</b></span>
                </div>
            `;
        }
    } else if(box) {
        box.style.display = "none";
    }
}

// --- 7. PROCESADORES DE ENVÍO DE TRANSACCIONES POR FORMULARIOS ---

// Recepción y despacho de Órdenes SD (Ventas)
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

// CORRECCIÓN RADICAL: Recepción y liberación de Órdenes PP (Producción)
document.getElementById('form-nueva-produccion')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Aislar manualmente los parámetros del formulario de planta para erradicar el campo 'pago'
    const cleanFormData = new FormData();
    cleanFormData.append('lote_nombre', this.querySelector('select[name="lote_nombre"]').value);
    cleanFormData.append('producto_id', this.querySelector('select[name="producto_id"]').value);
    cleanFormData.append('cantidad', this.querySelector('input[name="cantidad"]').value);

    // Se envían exclusivamente los datos sanitizados al controlador de backend
    fetch('guardar_produccion.php', { 
        method: 'POST', 
        body: cleanFormData 
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            lanzarToastERP("⚙️ Lote de fabricación liberado. Materia prima descontada.");
            this.reset();
            cargarHistorialProduccion();
        } else {
            alert('Error en producción: ' + data.message);
        }
    })
    .catch(err => {
        alert('Falla crítica en comunicación asíncrona: ' + err);
    });
});

// --- 8. EMISOR PROCEDURAL DE NOTIFICACIONES TOAST FLOTANTES ---
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

// --- 9. MOTOR GRÁFICO ANALÍTICO (CHART.JS) ---
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

// --- 10. INTERFAZ INTEGRAL A PANTALLA COMPLETA ---
function toggleFullScreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
    } else {
        document.exitFullscreen();
    }
}

// Inicialización de hilos al cargar el Mandante
document.addEventListener("DOMContentLoaded", () => {
    inicializarGraficos();
    cargarHistorialPedidos();
});