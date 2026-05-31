# ERPSystem - Simulador Académico SAP Lite ERP (Fiori Style)

Este proyecto es un prototipo interactivo de un sistema **ERP SAP** basado en la interfaz **SAP Fiori**. Permite simular los flujos de trabajo e interacciones en tiempo real entre múltiples mandantes y módulos empresariales utilizando una arquitectura web integrada.

## 👥 Consultor Certificado / Desarrollador
* **Nombre:** Nelson Polares Ortiz
* **Entidad Académica:** Universidad Mayor, Real y Pontificia de San Francisco Xavier de Chuquisaca (USFX)
* **Carrera:** Ingeniería de Sistemas

---

## 🛠️ Arquitectura y Tecnologías Utilizadas
* **Frontend:** HTML5, CSS3 (Estructura fluida a pantalla completa), JavaScript Nativo (Navegación Single Page Application y peticiones asíncronas).
* **Backend:** PHP (Lógica de negocios y control estricto de sesiones).
* **Base de Datos:** MySQL / MariaDB (Ecosistema relacional transaccional).
* **Analítica & Reportes:** Chart.js (Librería de gráficos interactivos) y html2pdf.js (Motor de exportación contable).

---

## 📦 Módulos Implementados
1. **Dashboard / Inicio:** Monitoreo analítico con gráficos interactivos de rendimiento e ingresos.
2. **MM (Material Management):** Control dinámico de inventario activo y alertas de stock crítico.
3. **FI/CO (Financial Accounting / Controlling):** Libro mayor, conciliación bancaria y exportación de balances a PDF real.
4. **SD (Sales and Distribution):** Formulario transaccional de órdenes de venta con simulador de descuentos por volumen (Pricing).
5. **CRM (Customer Relationship Management):** Ficha comercial predictiva basada en tendencias de compra históricas (SAP HANA Engine).
6. **PP (Production Planning):** Gestión y liberación de lotes de fabricación con descuento automático de materias primas.
7. **QM (Quality Management):** Auditoría técnica de muestreos y Decisiones de Empleo (Aprobación/Bloqueo de stock).

---

## 🚀 Instrucciones para la Instalación y Despliegue Local

Sigue estos pasos para hacer correr el simulador en tu entorno local:

### 1. Preparación del Servidor Local
1. Descarga e instala **XAMPP** (compatible con PHP 8.x).
2. Abre el **XAMPP Control Panel**.
3. Inicia los módulos de **Apache** y **MySQL** presionando sus respectivos botones de *Start*.
4. *Nota de configuración:* Asegúrate de verificar el puerto de tu MySQL. Si tu panel indica el puerto **3307**, el archivo `conexion.php` ya está preconfigurado para mapearlo de forma exclusiva.

### 2. Despliegue de los Archivos del Proyecto
1. Dirígete a la ruta raíz de tu servidor local: `C:\xampp\htdocs\`.
2. Crea una carpeta llamada exactamente `Proyecto SAP`.
3. Pega dentro de esa carpeta todos los archivos del sistema:
   * `index.php` (Pantalla de acceso)
   * `dashboard.php` (Ecosistema principal)
   * `conexion.php` (Enlace de datos)
   * `style.css` (Diseño de interfaz)
   * `script.js` (Lógica interactiva)
   * `obtener_inventario.php`, `obtener_pedidos.php`, `obtener_asientos.php`, `obtener_calidad.php`, `obtener_produccion.php` (Controladores asíncronos)
   * `guardar_pedido.php`, `guardar_produccion.php` (Procesadores transaccionales)
   * `logout.php` (Cierre de mandante)

### 3. Montaje de la Base de Datos (MySQL)
1. Abre tu navegador web e ingresa al gestor de bases de datos: `http://localhost/phpmyadmin/`.
2. Haz clic en **Nueva** en el panel izquierdo y crea una base de datos llamada exactamente `sap_lite`.
3. Selecciona la base de datos `sap_lite` recién creada, ve a la pestaña **SQL** en el menú superior, pega el contenido del archivo `sap_lite.sql` y presiona el botón **Continuar** / **Go**.

### 4. Ejecución del Sistema
1. Abre una nueva pestaña en tu navegador e ingresa a la siguiente URL:
   `http://localhost/Proyecto%20SAP/index.php`
2. **Credenciales Oficiales del Consultor:**
   * **Usuario:** `Nelson`
   * **Contraseña Corporativa:** `sap123`
3. Presiona **Iniciar Sesión** e ingresa al ecosistema profesional a pantalla completa.

---

## 💡 Recomendación de Navegación
* Utiliza el botón **"📺 Fullscreen"** en la barra superior del ERP para maximizar la experiencia de usuario y ocultar las barras del navegador, simulando un entorno de escritorio nativo de SAP.
* Realiza pruebas cruzadas: Crea un pedido en **SD**, disminuye stock en **PP** o aprueba/bloquea en **QM** para ver cómo impacta visual y matemáticamente a todo el sistema al instante.
