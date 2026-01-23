const SOCKET_URL = 'ws://192.168.100.182:8080'; 
var conn = new WebSocket(SOCKET_URL);

conn.onopen = function(e) {
    console.log("✅ ¡Conexión Socket Establecida!");
};

conn.onclose = function(e) {
    console.warn("⚠️ Conexión Socket cerrada. Intentando reconectar en 5 segundos...");
    setTimeout(function() {
        location.reload(); 
    }, 5000);
};

conn.onerror = function(err) {
    console.error("❌ Error en el Socket: ", err);
};

conn.onmessage = function(e) {
    var data = JSON.parse(e.data);
    
    if (data.tipo === 'actualizacion_estado') {
        actualizarBadgeVisual(data.id, data.estado);
    }
    if (data.tipo === 'nuevo_libro_creado') {
        agregarFilaTabla(data.libro);
    }
    if (data.tipo === 'xml_importado') {
        alert("📚 ¡Atención! Se han importado nuevos libros al catálogo.");
        location.reload();
    }
    if (data.tipo === 'libro_eliminado') {
        removerFilaTabla(data.id);
    }
};

function enviarAlSocket(payload) {
    if (conn.readyState === WebSocket.OPEN) {
        conn.send(JSON.stringify(payload));
    } else {
        console.warn("Socket ocupado o conectando. Reintentando en 100ms...");
        setTimeout(function() {
            enviarAlSocket(payload);
        }, 100);
    }
}

function crearLibroAJAX(e) {
    e.preventDefault();
    var form = document.getElementById('formNuevoLibro');
    var formData = new FormData(form);

    fetch('actions/guardar.php', { method: 'POST', body: formData })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            document.getElementById('modalNuevo').style.display = 'none';
            form.reset();
            
            enviarAlSocket({
                tipo: 'nuevo_libro_creado',
                libro: result.libro
            });
        } else {
            alert("Error: " + result.error);
        }
    })
    .catch(error => console.error('Error:', error));
}

function importarXmlAJAX(e) {
    e.preventDefault();
    var form = document.getElementById('formImportarXML');
    var formData = new FormData(form);
    var btnSubmit = form.querySelector('button[type="submit"]');
    var textoOriginal = btnSubmit.innerText;
    
    btnSubmit.innerText = "Procesando...";
    btnSubmit.disabled = true;

    fetch('xml/importar.php', { method: 'POST', body: formData })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            document.getElementById('modalImportar').style.display = 'none';
            form.reset();
            enviarAlSocket({ tipo: 'xml_importado' });
        } else {
            alert("Errores:\n" + result.errores.join("\n"));
        }
    })
    .finally(() => {
        btnSubmit.innerText = textoOriginal;
        btnSubmit.disabled = false;
    });
}

function cambiarEstadoLibro(idLibro, estadoActual) {
    let nuevoEstado = estadoActual ? 0 : 1;

    fetch('actions/cambiar_estado.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: idLibro, estado: nuevoEstado })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            enviarAlSocket({
                tipo: 'actualizacion_estado',
                id: idLibro,
                estado: nuevoEstado
            });
        } else {
            if(result.error) alert("⛔ " + result.error);
            else alert("Error al conectar con la base de datos.");
        }
    });
}

function eliminarLibro(id) {
    if(!confirm("¿Estás seguro de que deseas eliminar este libro permanentemente?")) return;

    fetch('actions/eliminar.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            enviarAlSocket({
                tipo: 'libro_eliminado',
                id: id
            });
        } else {
            alert("Error: " + result.error);
        }
    });
}

function agregarFilaTabla(libro) {
    var tbody = document.querySelector('table tbody');

    if (tbody.rows.length === 1 && tbody.cells.length === 1) {
        if(tbody.rows[0].innerText.includes("No hay")) {
            tbody.innerHTML = '';
        }
    }

    var tr = document.createElement('tr');
    tr.style.backgroundColor = "#d4edda"; 
    tr.style.transition = "background-color 2s";

    var editorial = libro.editorial || '';
    var paginas = libro.paginas || '';
    var precio = parseFloat(libro.precio).toFixed(2);
    
    var botonAccion = '';
    
    if (typeof ES_ADMIN !== 'undefined' && ES_ADMIN === true) {
         botonAccion = `<button class="btn btn-danger" style="font-size: 0.7rem;" onclick="eliminarLibro(${libro.id})">Eliminar</button>`;
    } else {
         botonAccion = `
            <button id="btn-${libro.id}" class="btn btn-secondary" 
                style="font-size: 0.8rem; padding: 2px 8px;"
                onclick="cambiarEstadoLibro(${libro.id}, 1)">
                Prestar
            </button>`;
    }

    tr.innerHTML = `
        <td>${libro.isbn}</td>
        <td>${libro.titulo}</td>
        <td>${libro.autor}</td>
        <td>${libro.genero}</td>
        <td>${libro.anio}</td>
        <td>${editorial}</td>
        <td>${paginas}</td>
        <td>$${precio}</td>
        <td>
            <span id="badge-${libro.id}" class="badge available">Disponible</span>
        </td>
        <td>
            ${botonAccion}
        </td>
    `;
    
    tbody.insertBefore(tr, tbody.firstChild);
    setTimeout(() => { tr.style.backgroundColor = "transparent"; }, 2000);
}

function actualizarBadgeVisual(id, nuevoEstado) {
    let badge = document.getElementById('badge-' + id);
    let btn = document.getElementById('btn-' + id); 
    
    if (badge) {
        if (nuevoEstado == 1) {
            badge.className = 'badge available';
            badge.innerText = 'Disponible';
            if(btn) {
                btn.innerText = 'Prestar';
                btn.onclick = function() { cambiarEstadoLibro(id, 1); };
            }
        } else {
            badge.className = 'badge borrowed';
            badge.innerText = 'Prestado';
            if(btn) {
                btn.innerText = 'Devolver';
                btn.onclick = function() { cambiarEstadoLibro(id, 0); };
            }
        }
        badge.style.opacity = 0;
        setTimeout(() => badge.style.opacity = 1, 300);
    }
}

function removerFilaTabla(id) {
    var badge = document.getElementById('badge-' + id);
    if (badge) {
        var tr = badge.closest('tr');
        tr.style.backgroundColor = "#f8d7da";
        tr.style.transition = "opacity 0.5s";
        setTimeout(() => {
            tr.style.opacity = "0";
            setTimeout(() => tr.remove(), 500);
        }, 100);
    }
}