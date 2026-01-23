<div id="modalNuevo" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarModal('modalNuevo')">&times;</span>
        <h2 style="border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">
            <i class="fas fa-book-medical"></i> Registrar Nuevo Libro
        </h2>
        
        <form id="formNuevoLibro" onsubmit="crearLibroAJAX(event)">
            
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>ISBN:</label>
                        <input type="text" name="isbn" required placeholder="Ej: 978-3-16-148410-0">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>Título:</label>
                        <input type="text" name="titulo" required placeholder="Título del libro">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>Autor:</label>
                        <input type="text" name="autor" required placeholder="Nombre del Autor">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>Género:</label>
                        <select name="genero" required>
                            <option value="">Seleccione...</option>
                            <option value="Novela">Novela</option>
                            <option value="Ciencia Ficción">Ciencia Ficción</option>
                            <option value="Tecnología">Tecnología</option>
                            <option value="Fantasía">Fantasía</option>
                            <option value="Historia">Historia</option>
                            <option value="Ciencia">Ciencia</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>Editorial:</label>
                        <input type="text" name="editorial" placeholder="Editorial">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>Año de Publicación:</label>
                        <input type="number" name="anio" required min="1000" max="2099" placeholder="Ej: 2024">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>Páginas:</label>
                        <input type="number" name="paginas" placeholder="0">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>Precio ($):</label>
                        <input type="number" name="precio" step="0.01" required placeholder="0.00">
                    </div>
                </div>
            </div>

            <div style="margin-top: 20px; text-align: right;">
                <button type="button" class="btn btn-secondary" onclick="cerrarModal('modalNuevo')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Libro</button>
            </div>
        </form>
    </div>
</div>

<div id="modalImportar" class="modal">
    <div class="modal-content small">
        <span class="close" onclick="cerrarModal('modalImportar')">&times;</span>
        <h2 style="border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">
            <i class="fas fa-file-upload"></i> Importar Catálogo
        </h2>
        
        <p style="color: #666; font-size: 0.9rem; margin-bottom: 15px;">
            Selecciona un archivo <strong>.xml</strong> válido que cumpla con el esquema XSD de la biblioteca.
        </p>
        
        <form id="formImportarXML" onsubmit="importarXmlAJAX(event)" enctype="multipart/form-data">
            <div class="form-group" style="border: 2px dashed #ccc; padding: 20px; text-align: center; border-radius: 5px;">
                <input type="file" name="archivo_xml" accept=".xml" required style="width: 100%;">
            </div>
            
            <button type="submit" class="btn btn-info full-width">
                <i class="fas fa-cloud-upload-alt"></i> Subir e Importar
            </button>
        </form>
    </div>
</div>

<style>
    .row { display: flex; gap: 15px; margin-bottom: 5px; }
    .col { flex: 1; }
    .full-width { width: 100%; }
    .modal-content.small { max-width: 400px; }
    
    .form-group label { display: block; margin-bottom: 5px; font-weight: bold; font-size: 0.9rem; }
    .form-group input, .form-group select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
    }
</style>