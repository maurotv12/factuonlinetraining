<!-- vistas/paginas/usuarios.php -->

<div class="page-heading">
    <h3>Gestión de Usuarios</h3>
</div>

<div class="page-content">
    <section class="section">
        <div class="card">
            <div class="card-header">
                Lista de Usuarios Registrados
            </div>
            <div class="card-body">
                <table id="tablaUsuarios" class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aquí deberías incluir datos desde el controlador -->
                        <tr>
                            <td>1</td>
                            <td>Juan Pérez</td>
                            <td>juan@example.com</td>
                            <td>Estudiante</td>
                            <td>Activo</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>María Gómez</td>
                            <td>maria@example.com</td>
                            <td>Instructor</td>
                            <td>Pendiente</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>