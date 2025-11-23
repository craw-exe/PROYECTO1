document.addEventListener("DOMContentLoaded", () => {
    // Asignar eventos a los botones de eliminar
    document.querySelectorAll(".btn-eliminar").forEach(btn => {
        btn.addEventListener("click", function () {
            const id = this.dataset.id;
            eliminarProducto(id);
        });
    });
});

/**
 * Función principal para eliminar un producto
 * @param {string} id - El ID del producto a eliminar
 */
function eliminarProducto(id) {
    const contenedorProducto = document.getElementById("producto-" + id);

    if (!contenedorProducto) return;

    fetch("eliminarCarrito.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "id=" + id
    })
    .then(resp => resp.text())
    .then(data => {
        console.log("Respuesta servidor:", data);

        // 1. Animación de salida (Fade out)
        contenedorProducto.style.transition = "opacity 0.3s ease, transform 0.3s ease";
        contenedorProducto.style.opacity = "0";
        contenedorProducto.style.transform = "translateX(20px)"; // Efecto de desplazamiento

        // Esperar a que termine la animación para remover del DOM
        setTimeout(() => {
            // 2. Remover el producto y la línea separadora
            const hr = contenedorProducto.nextElementSibling;
            contenedorProducto.remove();

            if (hr && hr.tagName === "HR") {
                hr.remove();
            }

            // 3. Recalcular los totales numéricos
            recalcularTotales();

            // 4. Verificar si el carrito quedó vacío
            const itemsRestantes = document.querySelectorAll(".producto-item");
            if (itemsRestantes.length === 0) {
                // Recargar para que PHP muestre el mensaje de "Carrito Vacío"
                location.reload();
            }
        }, 300); // 300ms coincide con la transición CSS
    })
    .catch(error => console.error("Error al eliminar:", error));
}

/**
 * Recalcula el Subtotal, Descuentos y Total basándose en los elementos HTML restantes
 */
function recalcularTotales() {
    let subtotal = 0;
    let descuentos = 0;

    // Busca todos los elementos que tengan la clase .subtotal-item
    document.querySelectorAll(".subtotal-item").forEach(item => {
        // Lee los atributos data- que agregamos en el PHP
        const precio = parseFloat(item.dataset.precio || 0);
        const descuento = parseFloat(item.dataset.descuento || 0);

        subtotal += precio;
        descuentos += descuento;
    });

    const total = subtotal - descuentos;

    // Actualiza el DOM asegurando 2 decimales
    const elSubtotal = document.getElementById("subtotal");
    const elDescuentos = document.getElementById("descuentos");
    const elTotal = document.getElementById("total");

    if (elSubtotal) elSubtotal.innerText = "$" + subtotal.toFixed(2);
    if (elDescuentos) elDescuentos.innerText = "$" + descuentos.toFixed(2);
    if (elTotal) elTotal.innerText = "$" + total.toFixed(2);
}