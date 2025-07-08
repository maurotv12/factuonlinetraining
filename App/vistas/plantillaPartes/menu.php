<div id="sidebar" class="active">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header">
                    <div class="d-flex justify-content-between">
                        <div style=" font-family: 'Nunito', sans-serif;"><span class="colorCursos">Cali</span><span class="colorCursos2">Belula</span>
                            <!-- <a href="inicio"><img src="vistas/assets/images/logo/logo-pawers-n.png" alt="Logo" ></a> -->
                        </div>

                        <div class="toggler">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>

                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Menu</li>

                        <li class="sidebar-item">
                            <a href="inicio" class='sidebar-link'>
                                <i class="bi bi-house"></i>
                                <span>Inicio</span>
                            </a>
                        </li>
                         <?php //if($usuario["rol"] == "cliente") { ?>
                         <li class="sidebar-item">
                            <a href="misCursos" class='sidebar-link'>
                                <i class="bi bi-book-half"></i>
                                <span>Mis cursos</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="inscripciones" class='sidebar-link'>
                                <i class="bi bi-bookmarks"></i>
                                <span>Inscripciones</span>
                            </a>
                        </li>
                        <?php //} ?>

                        <?php //if($usuario["rol"] == "admin") { ?>
                         <li class="sidebar-item">
                            <a href="cursos" class='sidebar-link'>
                                <i class="bi bi-book-half"></i>
                                <span>Cursos</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="inscripcionessAdmin" class='sidebar-link'>
                                <i class="bi bi-bookmarks"></i>
                                <span>Inscripciones</span>
                            </a>
                        </li>
                         <li class="sidebar-item">
                            <a href="profesores" class='sidebar-link'>
                                <i class="bi bi-person-square"></i>
                                <span>Profesores</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="usuarios" class='sidebar-link'>
                                <i class="bi bi-person-plus-fill"></i>
                                <span>Usuarios</span>
                            </a>
                        </li>
                         <?php //} ?>

                        <li class="sidebar-item active">
                            <a href="perfil" class='sidebar-link'>
                                <i class="bi bi-person-circle"></i>
                                <span>Perfil</span>
                            </a>
                        </li>
                         <li class="sidebar-item">
                            <a href="salir" class='sidebar-link'>
                                <i class="bi bi-door-closed"></i>
                                <span>Salir</span>
                            </a>
                        </li>

                    </ul>
                </div>
                <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
            </div>
        </div>