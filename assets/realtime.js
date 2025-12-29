var conn = new WebSocket('ws://localhost:8080');

conn.onopen = function(e) {
    console.log("¡Conexión Socket Establecida!");
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
};

function crearLibroAJAX(e) {
    e.preventDefault();
    
    var form = document.getElementById('formNuevoLibro');
    var formData = new FormData(form);

    fetch('actions/guardar.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            document.getElementById('modalNuevo').style.display = 'none';
            form.reset();
            
            var payload = {
                tipo: 'nuevo_libro_creado',
                libro: result.libro
            };
            conn.send(JSON.stringify(payload));
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

    fetch('xml/importar.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            document.getElementById('modalImportar').style.display = 'none';
            form.reset();
    
            conn.send(JSON.stringify({ tipo: 'xml_importado' }));
        } else {
            alert("Errores:\n" + result.errores.join("\n"));
        }
    })
    .finally(() => {
        btnSubmit.innerText = textoOriginal;
        btnSubmit.disabled = false;
    });
}

function agregarFilaTabla(libro) {
    var tbody = document.querySelector('table tbody');
    
    if (tbody.rows.length === 1 && tbody.cells.length === 1) {
        if(tbody.rows[0].innerText.includes("No hay libros")) {
            tbody.innerHTML = '';
        }
    }

    var tr = document.createElement('tr');
    tr.style.backgroundColor = "#d4edda"; 
    tr.style.transition = "background-color 2s";
    var editorial = libro.editorial || '';
    var paginas = libro.paginas || '';
    var disponible = parseInt(libro.disponible); 

    tr.innerHTML = `
        <td>${libro.isbn}</td>
        <td>${libro.titulo}</td>
        <td>${libro.autor}</td>
        <td>${libro.genero}</td>
        <td>${libro.anio}</td>
        <td>${editorial}</td>
        <td>${paginas}</td>
        <td>$${parseFloat(libro.precio).toFixed(2)}</td>
        <td>
            <span id="badge-${libro.id}" class="badge available">Disponible</span>
        </td>
        <td>
            <button id="btn-${libro.id}" class="btn btn-secondary" 
                style="font-size: 0.8rem; padding: 2px 8px;"
                onclick="cambiarEstadoLibro(${libro.id}, 1)">
                Prestar
            </button>
        </td>
    `;
    
    tbody.insertBefore(tr, tbody.firstChild);

    setTimeout(() => { tr.style.backgroundColor = "transparent"; }, 2000);
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
            var payload = {
                tipo: 'actualizacion_estado',
                id: idLibro,
                estado: nuevoEstado
            };
            conn.send(JSON.stringify(payload));
            console.log("Aviso enviado al socket");
        } else {
            alert("Error al guardar en BD");
        }
    });
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