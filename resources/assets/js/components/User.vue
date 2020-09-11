<template>
            <main class="main">
            <!-- Breadcrumb -->
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Escritorio</a></li>
            </ol>
            <div class="container-fluid">
                <!-- Ejemplo de tabla Listado -->
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i>&nbsp;Usuarios&nbsp;
                        <button type="button" @click="abrirModal('persona','registrar')" class="btn btn-pill btn-primary btn-sm">
                            <i class="icon-plus"></i>&nbsp;Nuevo
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-auto my-1 pr-1">Columna</label>
                            <div class="col-md-4 pl-1">
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <select class="form-control" v-model="usuarioObjCons.criterio">
                                            <option value="nombre">Nombre</option>
                                            <option value="num_documento">Documento</option>
                                            <option value="email">Email</option>
                                            <option value="telefono">Teléfono</option>
                                        </select>
                                    </span>
                                    <input type="text" ref="personaocTextoBuscar" v-model="usuarioObjCons.buscar" @keyup.enter="cambiarPagina(1, usuarioObjCons, pagination, 1)" class="form-control" placeholder="Texto a buscar">
                                </div>
                            </div>
                            <label class="col-md-auto my-1 pr-1">Ver</label>
                            <div class="col-md-auto pl-1">
                                <select class="form-control" @change="cambiarPagina(1, usuarioObjCons, pagination, 1)" v-model="usuarioObjCons.numRegs">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <button type="button" @click="cambiarPagina(1, usuarioObjCons, pagination, 1)" class="btn btn-pill btn-primary btn-sm"><i class="fa fa-search"></i>&nbsp;Buscar</button>
                        </div>
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Nombre</th>
                                    <th class="text-center">Tipo Documento</th>
                                    <th class="text-center">Número</th>
                                    <th class="text-center">Dirección</th>
                                    <th class="text-center">Teléfono</th>
                                    <th class="text-center">Email</th>
                                    <th class="text-center">Usuario</th>
                                    <th class="text-center">Cargo</th>
                                    <th class="text-center">Documentos pendientes</th>
                                    <th class="text-center">Carpetas</th>
                                    <th class="text-center">Permisos</th>
                                    <th class="text-center">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(persona, index) in arrayPersona" :key="persona.id">
                                    <td class="text-center" v-text="index + 1"></td>
                                    <td v-text="persona.nombre_completo"></td>
                                    <td class="text-center" v-text="persona.tipo_documento"></td>
                                    <td v-text="persona.num_documento"></td>
                                    <td v-text="persona.direccion"></td>
                                    <td v-text="persona.telefono"></td>
                                    <td v-text="persona.email"></td>
                                    <td v-text="persona.usuario"></td>
                                    <td v-text="persona.cargo"></td>
                                    <td class="text-center">
                                        <span class='badge docs-pends' :class="['badge-'+ (persona.documentosPendientes_count == 0 ? 'success' : 'warning')]" v-text="persona.indicadorDocsPends"></span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn rounded btn-info btn-sm" @click="abrirModal('usuarioCarpetaListar', undefined, {personaInd: index, personaId: persona.id, personaNombre: persona.nombre_completo, cargoId: persona.fk_id_cargos, personaCargo: persona.cargo})" v-if="persona.condicion">
                                            <i class="fa fa-archive" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                            <i class="fa fa-archive" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" @click="abrirModal('persona','permisos', {personaId: persona.id, nombre_completo: persona.nombre_completo, personaIdRol: persona.idrol})" class="btn rounded btn-info btn-sm" v-if="persona.logueable">
                                            <i class="icon-list"></i>
                                        </button>
                                        <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                            <i class="icon-list"></i>
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" @click="abrirModal('persona','actualizar',persona)" class="btn rounded btn-warning btn-sm">
                                          <i class="icon-pencil"></i>
                                        </button>
                                        <template v-if="persona.condicion">
                                            <button type="button" class="btn rounded btn-danger btn-sm" @click="desactivarUsuario(persona.id)">
                                                <i class="icon-trash"></i>
                                            </button>
                                        </template>
                                        <template v-else>
                                            <button type="button" class="btn rounded btn-success btn-sm" @click="activarUsuario(persona.id)">
                                                <i class="icon-check"></i>
                                            </button>
                                        </template>
                                    </td>
                                </tr>                                
                            </tbody>
                        </table>
                        <nav>
                            <ul class="pagination">
                                <li class="page-item" v-if="pagination.current_page > 1">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1, usuarioObjCons, pagination, 1)">Ant</a>
                                </li>
                                <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page, usuarioObjCons, pagination, 1)" v-text="page"></a>
                                </li>
                                <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1, usuarioObjCons, pagination, 1)">Sig</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <!-- Fin ejemplo de tabla Listado -->
            </div>
            <!--Inicio del modal agregar/actualizar-->
            <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-primary modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="tituloModal"></h4>
                            <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                                <div class="form-group row justify-content-center">
                                    <label class="col-md-2 my-1 pr-1">Nombre 1</label>
                                    <div class="col-md-3 pl-1">
                                        <input type="text" class="form-control" :class="{'is-invalid': noHayNombre1}" v-model="nombre1">
                                    </div>
                                    <label class="col-md-2 my-1 pr-1">Nombre 2</label>
                                    <div class="col-md-3 pl-1">
                                        <input type="text" class="form-control" v-model="nombre2">
                                    </div>
                                </div>
                                <div class="form-group row justify-content-center">
                                    <label class="col-md-2 my-1 pr-1">Apellido 1</label>
                                    <div class="col-md-3 pl-1">
                                        <input type="text" class="form-control" :class="{'is-invalid': noHayApellido1}" v-model="apellido1">
                                    </div>
                                    <label class="col-md-2 my-1 pr-1">Apellido 2</label>
                                    <div class="col-md-3 pl-1">
                                        <input type="text" class="form-control" v-model="apellido2">
                                    </div>
                                </div>
                                <div class="form-group row justify-content-center">
                                    <label class="col-md-2 my-1 pr-1">Tipo documento</label>
                                    <div class="col-md-3 pl-1">
                                        <select class="form-control" :class="{'is-invalid': noHayTipoDocumento}" v-model="tipo_documento">
                                            <option value="">Seleccione</option>
                                            <option value="CC">CÉDULA DE CIUDADANÍA</option>
                                            <option value="NUIP">NUMERO ÚNICO DE IDENTIFICACIÓN PERSONAL</option>
                                            <option value="PASS">PASAPORTE</option>
                                            <option value="TI">TARJETA DE IDENTIDAD</option>
                                        </select>
                                    </div>
                                    <label class="col-md-2 my-1 pr-1">Documento</label>
                                    <div class="col-md-3 pl-1">
                                        <input type="text" class="form-control" :class="{'is-invalid': noHayUsuarioDocumento}" v-model="num_documento">
                                    </div>
                                </div>
                                <div class="form-group row justify-content-center">
                                    <label class="col-md-2 my-1 pr-1">Fecha de nacimiento</label>
                                    <div class="col-md-3 pl-1">
                                        <input type="date" class="form-control" :class="{'is-invalid': noHayFecNac}" v-model="fec_nac">
                                    </div>
                                    <label class="col-md-2 my-1 pr-1">Sexo</label>
                                    <div class="col-md-3 pl-1">
                                        <select class="form-control" :class="{'is-invalid': noHaySexo}" v-model="sexo">
                                            <option value="">Seleccione</option>
                                            <option value="Masculino">MASCULINO</option>
                                            <option value="Femenino">FEMENINO</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row justify-content-center">
                                    <label class="col-md-2 my-1 pr-1">Dirección</label>
                                    <div class="col-md-3 pl-1">
                                        <input type="text" class="form-control" :class="{'is-invalid': noHayDireccion}" v-model="direccion">
                                    </div>
                                    <label class="col-md-2 my-1 pr-1">Teléfono</label>
                                    <div class="col-md-3 pl-1">
                                        <input type="text" class="form-control" :class="{'is-invalid': noHayTelefono}" v-model="telefono">
                                    </div>
                                </div>
                                <div class="form-group row justify-content-center">
                                    <label class="col-md-2 my-1 pr-1">Correo electrónico</label>
                                    <div class="col-md-3 pl-1">
                                        <input type="email" class="form-control" :class="{'is-invalid': noHayEmail}" v-model="email" placeholder="usuario@dominio.com">
                                    </div>
                                    <label class="col-md-2 my-1 pr-1">Cargo</label>
                                    <div class="col-md-3 pl-1">
                                        <select class="form-control" :class="{'is-invalid': noHayCargo}" v-model="cargoId">
                                            <option value="">Seleccione</option>
                                            <option v-for="cargoSimple in cargoSimpleArr" :key="cargoSimple.id" :value="cargoSimple.id" v-text="cargoSimple.cargo"></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row justify-content-center">
                                    <label class="col-md-auto col-form-label pr-1 pt-1" for="toggleAcceso">Permitir acceso al sistema</label>
                                    <div class="col-md-auto pl-1">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="checkbox" id="toggleAcceso" v-model="logueable">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <template v-if="logueable">
                                    <div class="form-group row justify-content-center">
                                        <label class="col-md-2 my-1 pr-1">Usuario</label>
                                        <div class="col-md-3 pl-1">
                                            <input type="text" class="form-control" :class="{'is-invalid': noHayUsuario}" v-model.trim="usuario">
                                        </div>
                                        <label class="col-md-2 my-1 pr-1">Contraseña</label>
                                        <div class="col-md-3 pl-1">
                                            <input type="password" class="form-control" v-model.trim="password">
                                        </div>
                                    </div>
                                </template>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-pill btn-danger btn-sm" @click="cerrarModal()">Cerrar</button>
                            <button type="button" class="btn btn-pill btn-success btn-sm" @click="tipoAccion == 'Guardar'? registrarPersona() : actualizarPersona()" v-text="tipoAccion"></button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!--Fin del modal-->
            <!--Inicio modal permisos-->
            <div class="modal fade" tabindex="-1" :class="{'mostrar' : modalPermisos}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-primary modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="tituloModal"></h4>
                            <button type="button" class="close" @click="cerrarModalPermisos()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="form-group row justify-content-center">
                                    <label class="col-md-auto my-1 pr-1">Marcar permisos como</label>
                                    <div class="col-md-auto">
                                        <select class="form-control" @change="marcarPermisosRol" v-model="idrol">
                                            <option value="">Ninguno</option>
                                            <option v-for="rol in arrRolesEmpresa" :value="rol.id" v-text="rol.nombre"></option>
                                        </select>
                                    </div>
                                </div>
                                <!--Inicio tabla permisos -->
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Módulo</th>
                                            <th class="text-center">Crear</th>
                                            <th class="text-center">Leer</th>
                                            <th class="text-center">Actualizar</th>
                                            <th class="text-center">Anular</th>
                                            <th class="text-center">Imprimir</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr :class="moduloPermisos.padre == null ? 'table-info' : 'table-light'" v-for="(moduloPermisos, key) in arrModulosPermisos" :key="moduloPermisos.id_modulos_empresas">
                                            <td v-text="moduloPermisos.nombre"></td>
                                            <td class="text-center">
                                                <input v-if="moduloPermisos.padre == null" v-model="moduloPermisos.crear" @click="alternarMarcaHijos(key, 'crear')" type="checkbox">
                                                <input v-else v-model="moduloPermisos.crear" @click="marcarPadre(key, 'crear')" type="checkbox">
                                            </td>
                                            <td class="text-center">
                                                <input v-if="moduloPermisos.padre == null" v-model="moduloPermisos.leer" @click="alternarMarcaHijos(key, 'leer')" type="checkbox">
                                                <input v-else v-model="moduloPermisos.leer" @click="marcarPadre(key, 'leer')" type="checkbox">
                                            </td>
                                            <td class="text-center">
                                                <input v-if="moduloPermisos.padre == null" v-model="moduloPermisos.actualizar" @click="alternarMarcaHijos(key, 'actualizar')" type="checkbox">
                                                <input v-else v-model="moduloPermisos.actualizar" @click="marcarPadre(key, 'actualizar')" type="checkbox">
                                            </td>
                                            <td class="text-center">
                                                <input v-if="moduloPermisos.padre == null" v-model="moduloPermisos.anular" @click="alternarMarcaHijos(key, 'anular')" type="checkbox">
                                                <input v-else v-model="moduloPermisos.anular" @click="marcarPadre(key, 'anular')" type="checkbox">
                                            </td>
                                            <td class="text-center">
                                                <input v-if="moduloPermisos.padre == null" v-model="moduloPermisos.imprimir" @click="alternarMarcaHijos(key, 'imprimir')" type="checkbox">
                                                <input v-else v-model="moduloPermisos.imprimir" @click="marcarPadre(key, 'imprimir')" type="checkbox">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <!--Fin tabla permisos-->
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-pill btn-danger btn-sm" @click="cerrarModalPermisos()">Cerrar</button>
                            <!-- DEBE MODIFICARSE ------------------------------------------------>
                            <button type="button" class="btn btn-pill btn-success btn-sm" @click="guardarPermisos()">Guardar</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- Modal CRUD usuario-carpeta-->
            <div id="usuariocarpeta-crud-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-primary modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 v-text="tituloModal"></h4>
                            <button type="button" class="close" aria-label="Close" @click="carpetaModalCerrar()">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row justify-content-between">
                                        <div class="col-md-auto">
                                            <i class="fa fa-folder-open-o" aria-hidden="true"></i>&nbsp;Carpetas&nbsp;
                                            <button type="button" @click="abrirModal('usuariocarpetaRegistrar')" class="btn btn-pill btn-primary btn-sm">
                                                <i class="icon-plus"></i>&nbsp;Nuevo
                                            </button>
                                        </div>
                                        <button type="button" class="btn btn-pill btn-info btn-sm" data-dismiss="modal" data-toggle="modal" data-target="#roldocumento-leer-mdl"><i class="fa fa-address-book-o" aria-hidden="true"></i>&nbsp;Documentos obligatorios</button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="form-group row justify-content-start">
                                        <label class="col-md-auto my-1 pr-1">Carpeta</label>
                                        <div class="col-md-3 pl-1">
                                            <input type="text" class="form-control" v-model="carpetaObjCons.carpeta">
                                        </div>
                                        <button type="button" class="btn btn-pill btn-primary btn-sm" @click="cambiarPagina(1, carpetaObjCons, carpetaPagin, 2)">
                                            <i class="fa fa-search"></i>&nbsp;Buscar
                                        </button>
                                    </div>
                                    <table class="table table-striped table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th class="text-center">#</th>
                                                <th class="text-center">Carpeta</th>
                                                <th class="text-center">Descripción</th>
                                                <th class="text-center">Documentos</th>
                                                <th class="text-center">Opciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template v-if="carpetaArr.length == 0">
                                                <tr>
                                                    <td class="text-center" colspan="5">No hay registros</td>
                                                </tr>
                                            </template>
                                            <template v-else>
                                                <tr v-for="(carpeta, index) in carpetaArr" :key="carpeta.id">
                                                    <td class="text-center" v-text="index + 1"></td>
                                                    <td v-text="carpeta.carpeta"></td>
                                                    <td v-text="carpeta.descripcion"></td>
                                                    <td class="text-center">
                                                        <button type="button" @click="abrirModal('usuariodocumentoListar', undefined, {carpetaId : carpeta.id, carpeta : carpeta.carpeta})" class="btn btn-info rounded btn-sm">
                                                            <i class="fa fa-files-o" aria-hidden="true"></i>
                                                        </button>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" @click="abrirModal('usuariocarpetaEditar', undefined, carpeta)" class="btn btn-warning rounded btn-sm">
                                                            <i class="icon-pencil"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger rounded btn-sm" @click="carpetaDescartar(carpeta.id)">
                                                            <i class="fa fa-times" aria-hidden="true"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                    <nav>
                                        <ul class="pagination">
                                            <li class="page-item" v-if="carpetaPagin.current_page > 1">
                                                <a class="page-link" href="#" @click.prevent="cambiarPagina(carpetaPagin.current_page - 1, carpetaObjCons, carpetaPagin, 2)">Ant</a>
                                            </li>
                                            <li class="page-item" v-for="page in carpetaPagesNumber" :key="page" :class="[page == carpetaPagin.current_page ? 'active' : '']">
                                                <a class="page-link" href="#" @click.prevent="cambiarPagina(page, carpetaObjCons, carpetaPagin, 2)" v-text="page"></a>
                                            </li>
                                            <li class="page-item" v-if="carpetaPagin.current_page < carpetaPagin.last_page">
                                                <a class="page-link" href="#" @click.prevent="cambiarPagina(carpetaPagin.current_page + 1, carpetaObjCons, carpetaPagin, 2)">Sig</a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-pill btn-primary btn-sm" @click="carpetaModalCerrar()"><i aria-hidden="true" class="fa fa-reply"></i>&nbsp;Atrás</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal usuario-carpeta -->
            <div id="usuariocarpeta-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-primary modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 v-text="carpetaMdlMdlEnc"></h4>
                            <button type="button" class="close" aria-label="Close" @click="carpetaModalModalCerrar()">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                                <div class="form-row justify-content-center">
                                    <div class="form-group col-md-10">
                                        <label>Carpeta</label>
                                        <input type="text" ref="carpetacbCarpeta" class="form-control" :class="{'is-invalid' : noHayCarpeta}" v-model="carpetaBody.carpeta">
                                    </div>
                                </div>
                                <div class="form-row justify-content-center">
                                    <div class="form-group col-md-10">
                                        <label>Descripción (opcional)</label>
                                        <textarea class="form-control" rows="2" v-model="carpetaBody.descripcion"></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-pill btn-danger btn-sm" @click="carpetaModalModalCerrar()">Cerrar</button>
                            <button type="button" class="btn btn-pill btn-success btn-sm" @click="tipoAccion == 'Guardar'? carpetaRegistrar() : carpetaActualizar()" v-text="tipoAccion"></button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal CRUD usuario-documento -->
            <div id="usuariodocumento-crud-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-primary modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 v-text="carpetaMdlMdlEnc"></h4>
                            <button type="button" class="close" aria-label="Close" @click="documentoModalCerrar()">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row justify-content-between">
                                        <div class="col-md-auto">
                                            <i class="fa fa-files-o" aria-hidden="true"></i>&nbsp;Documentos&nbsp;
                                            <button type="button" @click="abrirModal('usuariodocumentoRegistrar')" class="btn btn-pill btn-primary btn-sm">
                                                <i class="icon-plus"></i>&nbsp;Nuevo
                                            </button>
                                        </div>
                                        <div class="col-md-auto">
                                            <button type="button" class="btn btn-pill btn-info btn-sm" data-dismiss="modal" data-toggle="modal" data-target="#roldocumento-leer-mdl"><i class="fa fa-address-book-o" aria-hidden="true"></i>&nbsp;Documentos obligatorios</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="form-group row justify-content-start">
                                        <label class="col-md-auto my-1 pr-1">Documento</label>
                                        <div class="col-md-3 pl-1">
                                            <input type="text" class="form-control" v-model="documentoObjCons.documento">
                                        </div>
                                        <button type="button" class="btn btn-pill btn-primary btn-sm" @click="cambiarPagina(1, documentoObjCons, documentoPagin, 3)">
                                            <i class="fa fa-search"></i>&nbsp;Buscar
                                        </button>
                                    </div>
                                    <table class="table table-striped table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th class="text-center">#</th>
                                                <th class="text-center">Documento</th>
                                                <th class="text-center">Ver</th>
                                                <th class="text-center">Opciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template v-if="documentoArr.length == 0">
                                                <tr>
                                                    <td class="text-center" colspan="4">No hay registros</td>
                                                </tr>
                                            </template>
                                            <template v-else>
                                                <tr v-for="(documento, index) in documentoArr" :key="documento.id">
                                                    <td class="text-center" v-text="index + 1"></td>
                                                    <td v-text="documento.documento"></td>
                                                    <td class="text-center"><a type="button" title="Visualizar" :href="documento.ruta" target="_blank" class="btn btn-info rounded btn-sm rutabtn"><i class="fa fa-eye" aria-hidden="true"></i></a></td>
                                                    <td class="text-center">
                                                        <button type="button" @click="abrirModal('usuariodocumentoEditar', undefined, documento)" class="btn btn-warning rounded btn-sm">
                                                            <i class="icon-pencil"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger rounded btn-sm" @click="documentoDescartar(documento.id)">
                                                            <i class="fa fa-times" aria-hidden="true"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                    <nav>
                                        <ul class="pagination">
                                            <li class="page-item" v-if="documentoPagin.current_page > 1">
                                                <a class="page-link" href="#" @click.prevent="cambiarPagina(documentoPagin.current_page - 1, documentoObjCons, documentoPagin, 3)">Ant</a>
                                            </li>
                                            <li class="page-item" v-for="page in documentoPagesNumber" :key="page" :class="[page == documentoPagin.current_page ? 'active' : '']">
                                                <a class="page-link" href="#" @click.prevent="cambiarPagina(page, documentoObjCons, documentoPagin, 3)" v-text="page"></a>
                                            </li>
                                            <li class="page-item" v-if="documentoPagin.current_page < documentoPagin.last_page">
                                                <a class="page-link" href="#" @click.prevent="cambiarPagina(documentoPagin.current_page + 1, documentoObjCons, documentoPagin, 3)">Sig</a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-pill btn-primary btn-sm" @click="documentoModalCerrar()"><i aria-hidden="true" class="fa fa-reply"></i>&nbsp;Atrás</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal usuario-documento -->
            <div id="usuariodocumento-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-primary" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 v-text="documentoMdlMdlEnc"></h4>
                            <button type="button" class="close" aria-label="Close" @click="documentoModalModalCerrar()">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                                <div class="form-row justify-content-center">
                                    <div class="form-group col-md-6">
                                        <label>Documento</label>
                                        <input type="text" ref="documentocbDocumento" class="form-control" :class="{'is-invalid' : noHayDocumento}" v-model="documento">
                                    </div>
                                    <div class="form-group col-md-5">
                                        <label>Documento cargo (opcional)</label>
                                        <select class="form-control" v-model="fk_id_cargos_documentos">
                                            <option value="">Seleccione</option>
                                            <option v-for="documento in porSubirArr" :key="documento.id" :value="documento.id" v-text="documento.obligatorio"></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row justify-content-center">
                                    <div class="form-group col-md-11">
                                        <label for="inputDocumento">Subir documento</label>
                                        <input id="inputDocumento" type="file" class="form-control form-control-file" :class="{'is-invalid' : noHayDocumentoRuta || documentoInvalido}" @change="obtenerDocumento">
                                        <small class="form-text text-muted font-weight-bold">Documentos admitidos: PDF</small>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-pill btn-danger btn-sm" @click="documentoModalModalCerrar()">Cerrar</button>
                            <button type="button" class="btn btn-pill btn-success btn-sm" @click="tipoAccion == 'Guardar'? documentoRegistrar() : documentoActualizar()" v-text="tipoAccion"></button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal rol-documento subidos y pendientes -->
            <div id="roldocumento-leer-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-primary modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 v-text="'Documentos '+ personaCargo"></h4>
                            <button type="button" aria-label="Close" class="close" data-dismiss="modal" data-toggle="modal" :data-target=" `#usuario${crudCarpsDocs ? 'carpeta' : 'documento'}-crud-mdl`">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row justify-content-center">
                                <div class="col-md-11">
                                    <table class="table table-striped table-bordered table-sm">
                                        <thead>
                                            <tr class="table-info">
                                                <th class="text-center" colspan="2">Documento</th>
                                                <th class="text-center align-middle" rowspan="2">Ver</th>
                                            </tr>
                                            <tr class="table-info">
                                                <th class="text-center">Obligatorio</th>
                                                <th class="text-center">Subido</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="documento in todosArr" :key="documento.id">
                                                <td class="text-center" v-text="documento.obligatorio"></td>
                                                <td class="text-center" v-html="documento.subido? documento.subido : `<span class='badge docs-pends badge-warning'>Pendiente</span>`"></td>
                                                <td class="text-center"><a type="button" title="Visualizar" :href="documento.ruta" target="_blank" class="btn btn-info rounded btn-sm" v-if="documento.subido"><i class="fa fa-eye" aria-hidden="true"></i></a></td>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-pill btn-primary btn-sm" data-dismiss="modal" data-toggle="modal" :data-target=" `#usuario${crudCarpsDocs ? 'carpeta' : 'documento'}-crud-mdl`"><i aria-hidden="true" class="fa fa-reply"></i>&nbsp;Atrás</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
</template>
<script>
    export default {
        props : ['ruta', 'permisosUser'],
        data ()
        {
            return {
                userRuta : 'users'
                , cargoRuta : 'cargos'
                
                , cargoSimpleArr : []
                
                , personaInd : 0
                , persona_id : 0

                , nombre1  : ''
                , nombre2 : ''
                , apellido1 : ''
                , apellido2 : ''
                , tipo_documento : ''
                , num_documento : ''
                , fec_nac : ''
                , sexo : ''
                , direccion : ''
                , telefono : ''
                , email : ''
                , cargoId : ''
                , logueable : false
                , usuario : ''
                , password : ''

                , userOnVacios : false

                , idrol : ''
                , old_idrol : ''
                , personaCargo : ''
                , arrayPersona : []
                , arrayRol : []
                , arrRolesEmpresa : []
                , arrRolesPermisos : []
                , arrModulosPermisos : [] // Carga la tabla permisos
                , cargarSelectorRoles : true
                , modal : 0
                , modalPermisos : 0 // Para abrir y cerrar el modal permisos
                , tituloModal : ''
                , tipoAccion : 'Guardar'
                , errorPersona : 0
                , errorMostrarMsjPersona : []
                , pagination : {
                    total          : 0
                    , current_page : 0
                    , per_page     : 0
                    , last_page    : 0
                    , from         : 0
                    , to           : 0
                }
                , offset : 3
                , usuarioObjCons : {
                    '?page'       : 1
                    , criterio     : 'nombre'
                    , buscar : ''
                    , numRegs : 10 
                }
                , columna : 'nombre'
                , textoBuscar : ''

                , carpetaMdlMdlEnc : 'Registrar carpeta'
                , carpetaObjCons : {
                    '?page'   : 1
                    , carpeta : ''
                    , fk_id_users : 0
                }
                , carpetaArr : []
                , carpetaId : ''
                , carpetaBody : {
                    carpeta : ''
                    , descripcion : ''
                    , fk_id_users : ''
                }
                , carpetaOnVacios : false

                , carpetaPagin : {
                    total          : 0
                    , current_page : 0
                    , per_page     : 0
                    , last_page    : 0
                    , from         : 0
                    , to           : 0
                }
                , crudCarpsDocs : true

                , documentoMdlMdlEnc : 'Registrar documento'
                , documentoObjCons : {
                    '?page'   : 1
                    , documento : ''
                    , fk_id_usuarios_carpetas : 0
                }
                , documentoArr : []
                , documentoId : ''
                , documento : ''
                , documentoRuta : ''
                , fk_id_cargos_documentos : ''   // AL ACTUALIZAR, SI SU VALOR ES DIFERENTE A OLD SE ACTUALIZA LA COLUMNA Documentos pendientes
                , fk_id_cargos_documentos_old : ''
                , cargoConDocumentosArr : []
                , porSubirArr : []
                , todosArr : []    // PARA LISTAR LOS DOCUMENTOS SUBIDOS Y PENDIENTES EN EL MODAL
                , documentoOnVacios : false

                , documentoPagin : {
                    total          : 0
                    , current_page : 0
                    , per_page     : 0
                    , last_page    : 0
                    , from         : 0
                    , to           : 0
                }
            }
        }
        , computed :
        {
            // VALIDACIÓN DE CAMPOS VACÍOS EN FORMULARIOS
            // Campos usuario
            noHayNombre1 : function ()
            {
                return this.userOnVacios && !this.nombre1.trim();
            }
            , noHayApellido1 : function ()
            {
                return this.userOnVacios && !this.apellido1.trim();
            }
            , noHayTipoDocumento : function ()
            {
                return this.userOnVacios && !this.tipo_documento;
            }
            , noHayUsuarioDocumento : function ()
            {
                return this.userOnVacios && !this.num_documento.trim();
            }
            , noHayFecNac : function ()
            {
                return this.userOnVacios && !this.fec_nac;
            }
            , noHaySexo : function ()
            {
                return this.userOnVacios && !this.sexo;
            }
            , noHayDireccion : function ()
            {
                return this.userOnVacios && !this.direccion.trim();
            }
            , noHayTelefono : function ()
            {
                return this.userOnVacios && !this.telefono.trim();
            }
            , noHayEmail : function ()
            {
                return this.userOnVacios && !this.email.trim();
            }
            , noHayCargo : function ()
            {
                return this.userOnVacios && !this.cargoId;
            }
            // Campos carpeta
            , noHayCarpeta : function ()
            {
                return this.carpetaOnVacios && !this.carpetaBody.carpeta.trim();
            }
            // Campos documento
            , noHayDocumento : function ()
            {
                return this.documentoOnVacios && !this.documento;         
            }
            , noHayDocumentoRuta : function ()
            {
                return this.documentoOnVacios && !this.documentoRuta;
            }
            , documentoInvalido : function ()
            {
                let invalido = false;
                if (typeof this.documentoRuta == 'object') {
                    if (this.documentoRuta.type != 'application/pdf') {
                        invalido = true;
                    }
                }
                return this.documentoOnVacios && invalido;
            }
            , isActived: function ()
            {
                return this.pagination.current_page;
            }
            //Calcula los elementos de la paginación
            , pagesNumber: function ()
            {
                if (!this.pagination.to) return [];
                
                let from = this.pagination.current_page - this.offset; 
                if (from < 1) from = 1;

                let to = from + (this.offset * 2); 
                if (to >= this.pagination.last_page) to = this.pagination.last_page;  

                let pagesArray = [];
                while (from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;
            }
            , carpetaPagesNumber: function ()
            {
                if (!this.carpetaPagin.to) return [];

                let from = this.carpetaPagin.current_page - this.offset;
                if (from < 1) from = 1;

                let to = from + (this.offset * 2);
                if (to >= this.carpetaPagin.last_page) to = this.carpetaPagin.last_page;

                let pagesArray = [];
                while (from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;
            }
            , documentoPagesNumber: function ()
            {
                if (!this.documentoPagin.to) return [];

                let from = this.documentoPagin.current_page - this.offset;
                if (from < 1) from = 1;

                let to = from + (this.offset * 2);
                if (to >= this.documentoPagin.last_page) to = this.documentoPagin.last_page;

                let pagesArray = [];
                while (from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;
            }
        }
        , methods :
        {
            cargarSelCargoDocumentos ()
            {
                this.porSubirArr = [];
                this.todosArr = [];
                let cargoDocumentos = this.cargoConDocumentosArr[this.cargoId].cargo_documentos, carpetas = this.arrayPersona[this.personaInd].usuarios_carpetas, registro = {};
                cargoDocumentos.forEach((cargoDocumento) => {
                    registro = {id: cargoDocumento.id, obligatorio: cargoDocumento.documento, subido: '', ruta: ''};
                    carpetas.forEach((carpeta) => {
                        carpeta.usuarios_documentos.forEach ((documento) => {
                            if (documento.fk_id_cargos_documentos == cargoDocumento.id)
                                registro = {...registro, subido: documento.nombre_ruta, ruta: documento.ruta};
                        });
                    });
                    if (!registro.subido) {
                        this.porSubirArr.push(registro);
                    }
                    this.todosArr.push(registro);
                }, this);
            }
            , obtenerCadCons(objCons)
            {
                return Object.entries(objCons).map(filtro => `${filtro[0]}=${filtro[1]}`).join('&');
            }
            , basicoCargosConDocumentos ()
            {
                let url = this.ruta +'/user/basico_cargoscondocumentos', me = this;
                axios.get(url).then(function (response) {
                    me.cargoConDocumentosArr = response.data.cargosConDocumentos;
                })
                .catch(function (error) {
                    console.log(error);
                });
            }
            , listarPersona ()
            {
                let url = `${this.ruta}/user${this.obtenerCadCons(this.usuarioObjCons)}`, me = this;
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayPersona = respuesta.personas.data;
                    me.pagination= respuesta.pagination;
                    if (!me.crudCarpsDocs) me.cargarSelCargoDocumentos(); // PARA ACTUALIZAR LA LISTA DE DOCUMENTOS DEL SELECTOR EN usuariodocumentos Y LA LISTA DE documentosobligatorios DEL MODAL
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            // Obtiene los permisos del usuario por módulo
            listarPermisos (usuario_id)
            {
                let me = this;
                var url = `${this.ruta}/listar_permisos`;
                axios.post(url, {'usuario_id':usuario_id, 'cargarSelector':me.cargarSelectorRoles}).then(function (response) {
                    if (me.cargarSelectorRoles) {
                        me.arrRolesEmpresa = response.data.roles;
                        me.arrRolesPermisos = response.data.rolesPermisos;
                        me.cargarSelectorRoles = false;
                    }
                    me.arrModulosPermisos = response.data.permisos;
                    me.arrModulosPermisos.forEach((moduloPermisos) => {
                            moduloPermisos.usuarios_id = usuario_id;
                    });
                })
                .catch(function (error) {
                    console.log(error);
                });
            }
            , guardarPermisos ()
            {
                let urlRol = this.ruta +'/users/rol/actualizar/'+ this.persona_id, urlPermisos = this.ruta +'/permisos', me = this;
                if (this.idrol != this.old_idrol) {
                    axios.patch(urlRol, {rolId: this.idrol}).then(response => {
                        me.arrayPersona.forEach(persona => {
                            if (persona.id == me.persona_id) {
                                persona.idrol = me.idrol;
                            }
                        });
                    }).catch(error => console.log(error));
                }

                axios.post(urlPermisos, me.arrModulosPermisos).then(response => me.cerrarModalPermisos()).catch(error => console.log(error));
            }
            , marcarPermisosRol ()
            {
                this.arrModulosPermisos.forEach(moduloPermisos => {
                    moduloPermisos.crear = moduloPermisos.leer = moduloPermisos.actualizar = moduloPermisos.anular = moduloPermisos.imprimir = false;
                });
                
                let idPadre = 0;

                if (!this.idrol) return;

                this.arrRolesPermisos.forEach(rolPermisos => {
                    if(rolPermisos.id_rol == this.idrol) {
                        this.arrModulosPermisos.forEach((moduloPermisos, indice) => {
                            if (moduloPermisos.modulos_id == rolPermisos.id_modulo) {
                                if (moduloPermisos.padre != idPadre && moduloPermisos.padre != null) {
                                    idPadre = moduloPermisos.padre;
                                    if (rolPermisos.escritura == 1) this.marcarPadre(indice, 'crear');
                                    if (rolPermisos.lectura == 1) this.marcarPadre(indice, 'leer');
                                    if (rolPermisos.edicion == 1) this.marcarPadre(indice, 'actualizar');
                                    if (rolPermisos.anular == 1) this.marcarPadre(indice, 'anular');
                                    if (rolPermisos.imprimir == 1) this.marcarPadre(indice, 'imprimir');
                                }
                                moduloPermisos.crear = rolPermisos.escritura;
                                moduloPermisos.leer = rolPermisos.lectura;
                                moduloPermisos.actualizar = rolPermisos.edicion;
                                moduloPermisos.anular = rolPermisos.anular;
                                moduloPermisos.imprimir = rolPermisos.imprimir;
                            }
                        }, this);
                    }
                }, this);
            }
            , alternarMarcaHijos (claveModulo, accion)
            {
                var marcaHijos = this.arrModulosPermisos[claveModulo][accion] = !this.arrModulosPermisos[claveModulo][accion];
                for (var i = claveModulo + 1; (i < (this.arrModulosPermisos.length)) ? (this.arrModulosPermisos[i].padre != null) ? true : false : false; i++) {
                    this.arrModulosPermisos[i][accion] = marcaHijos;
                }
            }
            , marcarPadre (claveModulo, accion)
            {
                for (var i = claveModulo - 1; i >= 0; i--) {
                    if (this.arrModulosPermisos[i].padre == null) {
                        this.arrModulosPermisos[i][accion] = true;
                        break;
                    }
                }
            }
            , selectRol ()
            {
                let me=this;
                var url= this.ruta +'/rol/selectRol';
                axios.get(url).then(function (response) {
                    //console.log(response);
                    var respuesta= response.data;
                    me.arrayRol = respuesta.roles;
                })
                .catch(function (error) {
                    console.log(error);
                });
            }
            , cambiarPagina (page, refObjCons, refPagination, recursoIndice)
            {
                //Actualiza la página actual
                refObjCons['?page'] = refPagination.current_page = page;
                //Envia la petición para visualizar la data de esa página
                switch (recursoIndice) {
                    case 1: this.listarPersona();break;
                    case 2: this.carpetaListar();break;
                    case 3: this.documentoListar();break;
                }
            }
            , registrarPersona ()
            {
                this.userOnVacios = true;
                if (this.validarPersona()) return;
                
                let me = this;

                axios.post(this.ruta +'/user/registrar',{
                    nombre1: this.nombre1
                    , nombre2: this.nombre2
                    , apellido1: this.apellido1
                    , apellido2: this.apellido2
                    , tipo_documento: this.tipo_documento
                    , num_documento: this.num_documento
                    , fec_nac : this.fec_nac
                    , sexo : this.sexo
                    , direccion: this.direccion
                    , telefono: this.telefono
                    , email: this.email
                    , cargoId : this.cargoId
                    , logueable : this.logueable
                    , usuario: this.usuario
                    , password: this.password
                }).then(function (response) {
                    me.cerrarModal();
                    me.listarPersona();
                }).catch(function (error) {
                    console.log(error);
                });
            }
            , actualizarPersona ()
            {
                this.userOnVacios = true;
                if (this.validarPersona()) return;
                
                let me = this;

                axios.put(this.ruta +'/user/actualizar',{
                    nombre1: this.nombre1
                    , nombre2: this.nombre2
                    , apellido1: this.apellido1
                    , apellido2: this.apellido2
                    , tipo_documento: this.tipo_documento
                    , num_documento : this.num_documento
                    , fec_nac : this.fec_nac
                    , sexo : this.sexo
                    , direccion : this.direccion
                    , telefono : this.telefono
                    , email : this.email
                    , logueable : this.logueable
                    , usuario: this.usuario
                    , password: this.password
                    , id : this.persona_id
                }).then(function (response) {
                    me.cerrarModal();
                    me.listarPersona();
                }).catch(function (error) {
                    console.log(error);
                }); 
            }
            , validarPersona ()
            {
                return this.noHayNombre1 || this.noHayApellido1 || this.noHayTipoDocumento || this.noHayUsuarioDocumento || this.noHayFecNac || this.noHaySexo || this.noHayDireccion || this.noHayTelefono || this.noHayEmail || this.noHayCargo;
            }
            , cerrarModal ()
            {
                this.modal = 0;
                this.tituloModal='';
                this.nombre1 = '';
                this.nombre2 = '';
                this.apellido1 = '';
                this.apellido2 = '';
                this.tipo_documento = '';
                this.num_documento = '';
                this.fec_nac = '';
                this.sexo = '';
                this.direccion = '';
                this.telefono = '';
                this.email = '';
                this.cargoId = '';
                this.logueable = false;
                this.usuario = '';
                this.password = '';
                //this.idrol=0;
                this.userOnVacios = false;
                this.tipoAccion = 'Guardar';
            }
            , cerrarModalPermisos ()
            {
                this.modalPermisos = 0;
                this.tituloModal = '';
                this.arrModulosPermisos = [];
                this.idrol = this.old_idrol = '';
            }
            , carpetaListar ()
            {
                let url = `${this.ruta}/usuarios_carpetas${this.obtenerCadCons(this.carpetaObjCons)}`, me = this;
                axios.get(url).then(function (response) {
                    let respuesta = response.data;
                    me.carpetaArr = respuesta.carpetas.data;
                    me.carpetaPagin = respuesta.pagination;
                })
                .catch(function (error) {
                    console.log(error);
                });
            }
            , carpetaValidar () {
                return this.noHayCarpeta;
            }
            , carpetaRegistrar ()
            {
                this.carpetaOnVacios = true;
                if (this.carpetaValidar()) {
                    this.$refs.carpetacbCarpeta.focus();
                    return;
                }
                let url = `${this.ruta}/usuarios_carpetas/registrar`, me = this;
                axios.post(url, this.carpetaBody).then(function (response) {
                    me.carpetaModalModalCerrar();
                    me.carpetaListar();
                })
                .catch(function (error) {
                    console.log(error);
                });
            }
            , carpetaActualizar ()
            {
                this.carpetaOnVacios = true;
                if (this.carpetaValidar()) {
                    this.$refs.carpetacbCarpeta.focus();
                    return;
                }
                let url = `${this.ruta}/usuarios_carpetas/actualizar/${this.carpetaId}`, me = this;
                axios.put(url, this.carpetaBody).then(function (response) {
                    me.carpetaModalModalCerrar();
                    me.carpetaListar();
                })
                .catch(function (error) {
                    console.log(error);
                });
            }
            , carpetaModalCerrar ()
            {
                $('#usuariocarpeta-crud-mdl').modal('hide');
                this.carpetaObjCons.fk_id_users = this.cargoId = 0;
                this.carpetaBody.fk_id_users = this.idrol = this.personaCargo = '';
                this.carpetaArr = [];
                this.$refs.personaocTextoBuscar.focus();
            }
            , carpetaModalModalCerrar ()
            {
                this.carpetaOnVacios = false;
                $('#usuariocarpeta-mdl').modal('hide');
                this.carpetaMdlMdlEnc = 'Registrar carpeta';
                this.carpetaBody.carpeta = this.carpetaBody.descripcion = '';
                this.tipoAccion = 'Guardar';
                $('#usuariocarpeta-crud-mdl').modal('show');
            }
            , documentoListar ()
            {
                let url = `${this.ruta}/usuarios_documentos${this.obtenerCadCons(this.documentoObjCons)}`, me = this;
                axios.get(url).then(function (response) {
                    let respuesta = response.data;
                    me.documentoArr = respuesta.documentos.data;
                    me.documentoPagin = respuesta.pagination;
                }).catch(error => console.log(error));
            }
            /*, basicoRolDocumentos (rolId)
            {
                let url = `${this.ruta}/usuarios_documentos/basico_roldocumentos?rolId=${rolId}`, me = this;
                axios.get(url).then(function (response) {
                    me.roldocumentoArr = response.data.rolDocumentos;
                });
            }*/
            , obtenerDocumento (event)
            {
                this.documentoRuta = event.target.files[0];
            }
            , documentoRegistrar()
            {
                this.documentoOnVacios = true;
                if (this.documentoValidar()) return;
                let url = `${this.ruta}/usuarios_documentos/registrar?fk_id_users=${this.carpetaBody.fk_id_users}`, documentoBody = new FormData(), me = this;
                documentoBody.append('documento', this.documento);
                documentoBody.append('ruta', this.documentoRuta);
                documentoBody.append('fk_id_usuarios_carpetas', this.documentoObjCons.fk_id_usuarios_carpetas);
                documentoBody.append('fk_id_cargos_documentos', this.fk_id_cargos_documentos);
                axios.post(url, documentoBody, {headers:{'Content-Type':'multipart/form-data'}})
                .then(function (response) {
                    if (me.fk_id_cargos_documentos) me.listarPersona(); // PARA RECARGAR AL ATRIBUTO usuario_documentos DEL ARRAY PERSONAS
                    me.documentoModalModalCerrar();
                    me.documentoListar();
                }).catch(function (error) {
                    console.log(error);
                });
            }
            , documentoActualizar ()
            {
                this.documentoOnVacios = true;
                if (this.documentoValidar()) return;
                let url = `${this.ruta}/usuarios_documentos/actualizar/${this.documentoId}?fk_id_users=${this.carpetaBody.fk_id_users}`, documentoBody = new FormData(), me = this;
                documentoBody.append('documento', this.documento);
                documentoBody.append('ruta', this.documentoRuta);
                documentoBody.append('fk_id_cargos_documentos', this.fk_id_cargos_documentos);
                documentoBody.append('_method', 'PUT');
                axios.post(url, documentoBody, {headers:{'Content-Type':'multipart/form-data'}})
                .then(function (response) {
                    if ((me.fk_id_cargos_documentos != me.fk_id_cargos_documentos_old) || !me.documentoInvalido) me.listarPersona(); // PARA RECARGAR AL ATRIBUTO usuario_documentos DEL ARRAY PERSONAS
                    me.fk_id_cargos_documentos_old = '';
                    me.documentoModalModalCerrar();
                    me.documentoListar();
                }).catch(function (error) {
                    console.log(error);
                });
            }
            , documentoValidar ()
            {
                return this.noHayDocumento || this.noHayDocumentoRuta || this.documentoInvalido;
            }
            , documentoModalCerrar ()
            {
                //this.carpetaId = '';
                this.documentoObjCons.fk_id_usuarios_carpetas = 0;
                this.carpetaMdlMdlEnc = 'Registrar carpeta';
                $('#usuariodocumento-crud-mdl').modal('hide');
                this.documentoArr = [];
                $('#usuariocarpeta-crud-mdl').modal('show');
                this.crudCarpsDocs = true;
            }
            , documentoModalModalCerrar ()
            {
                $('#usuariodocumento-mdl').modal('hide');
                this.documentoMdlMdlEnc = 'Registrar documento';
                this.documentoId = this.documento = this.documentoRuta = this.fk_id_cargos_documentos = this.fk_id_cargos_documentos_old = '';
                $('#inputDocumento').val('');
                this.tipoAccion = 'Guardar';
                this.documentoOnVacios = false;
                $('#usuariodocumento-crud-mdl').modal('show');
            }  
            , abrirModal (modelo, accion, data = [])
            {
                switch(modelo){
                    case "persona":
                    {
                        this.selectRol();
                        switch(accion){
                            case 'registrar':
                            {
                                this.modal = 1;
                                this.tituloModal = 'Registrar usuario';
                                break;
                            }
                            case 'actualizar':
                            {
                                this.modal=1;
                                this.tituloModal='Editar usuario';
                                this.tipoAccion='Actualizar';
                                this.persona_id = data.id;
                                this.nombre1 = data.nombre1;
                                this.nombre2 = data.nombre2;
                                this.apellido1 = data.apellido1;
                                this.apellido2 = data.apellido2;
                                this.tipo_documento = data.tipo_documento;
                                this.num_documento = data.num_documento;
                                this.fec_nac = data.fec_nac;
                                this.sexo = data.sexo;
                                this.direccion = data.direccion;
                                this.telefono = data.telefono;
                                this.email = data.email;
                                this.logueable = data.logueable;
                                this.usuario = data.usuario;
                                this.cargoId = data.fk_id_cargos;
                                break;
                            }
                            case 'permisos':
                            {
                                this.modalPermisos = 1;
                                this.tituloModal = 'Permisos de ' + data.nombre_completo;
                                this.persona_id = data.personaId;
                                this.idrol = this.old_idrol = data.personaIdRol === null ? '': data.personaIdRol;
                                this.listarPermisos(data.personaId);
                                break;
                            }
                        }
                        break;
                    }
                    case 'usuarioCarpetaListar':
                        this.tituloModal = 'Usuario - '+ data.personaNombre;
                        this.personaCargo = data.personaCargo;
                        this.cargoId = data.cargoId;
                        this.personaInd = data.personaInd;
                        this.carpetaObjCons.fk_id_users = this.carpetaBody.fk_id_users = data.personaId;
                        this.carpetaListar();
                        this.cargarSelCargoDocumentos();//this.basicoRolDocumentos(data.rolId);
                        $('#usuariocarpeta-crud-mdl').modal('show');
                        break;
                    case 'usuariocarpetaRegistrar':
                        $('#usuariocarpeta-crud-mdl').modal('hide');
                        $('#usuariocarpeta-mdl').modal('show');
                        this.$refs.carpetacbCarpeta.focus();
                        break;
                    case 'usuariocarpetaEditar':
                        $('#usuariocarpeta-crud-mdl').modal('hide');
                        this.carpetaMdlMdlEnc = 'Editar carpeta';
                        this.carpetaId = data.id;
                        this.carpetaBody.carpeta = data.carpeta;
                        this.carpetaBody.descripcion = data.descripcion;
                        this.tipoAccion = 'Actualizar';
                        $('#usuariocarpeta-mdl').modal('show');
                        this.$refs.carpetacbCarpeta.focus();
                        break;
                    case 'usuariodocumentoListar':
                        this.carpetaMdlMdlEnc = 'Carpeta - '+ data.carpeta;
                        //this.carpetaId = data.carpetaId;
                        this.documentoObjCons.fk_id_usuarios_carpetas = data.carpetaId;
                        this.documentoListar();
                        $('#usuariocarpeta-crud-mdl').modal('hide');
                        $('#usuariodocumento-crud-mdl').modal('show');
                        this.crudCarpsDocs = false;
                        break;
                    case 'usuariodocumentoRegistrar':
                        $('#usuariodocumento-crud-mdl').modal('hide');
                        $('#usuariodocumento-mdl').modal('show');
                        break;
                    case 'usuariodocumentoEditar':
                        $('#usuariodocumento-crud-mdl').modal('hide');
                        this.documentoMdlMdlEnc = 'Editar documento';
                        this.documentoId = data.id;
                        this.documento = data.documento;
                        this.documentoRuta = data.ruta;
                        this.fk_id_cargos_documentos = this.fk_id_cargos_documentos_old = data.fk_id_cargos_documentos;
                        this.tipoAccion = 'Actualizar';
                        $('#usuariodocumento-mdl').modal('show');
                        break;
                }
            }
            , desactivarUsuario (id)
            {
               Swal.fire({
                title: 'Esta seguro de desactivar este usuario?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar!',
                cancelButtonText: 'Cancelar',
                confirmButtonClass: 'btn btn-pill btn-success',
                cancelButtonClass: 'btn btn-pill btn-danger',
                buttonsStyling: false,
                reverseButtons: true
                }).then((result) => {
                if (result.value) {
                    let me = this;

                    axios.put(this.ruta +'/user/desactivar',{
                        'id': id
                    }).then(function (response) {
                        me.listarPersona();
                        Swal.fire(
                        'Desactivado!',
                        'El registro ha sido desactivado con éxito.',
                        'success'
                        )
                    }).catch(function (error) {
                        console.log(error);
                    });
                    
                    
                } else if (
                    // Read more about handling dismissals
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    
                }
                }) 
            }
            , activarUsuario (id)
            {
               Swal.fire({
                title: 'Esta seguro de activar este usuario?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar!',
                cancelButtonText: 'Cancelar',
                confirmButtonClass: 'btn btn-pill btn-success',
                cancelButtonClass: 'btn btn-pill btn-danger',
                buttonsStyling: false,
                reverseButtons: true
                }).then((result) => {
                if (result.value) {
                    let me = this;

                    axios.put(this.ruta +'/user/activar',{
                        'id': id
                    }).then(function (response) {
                        me.listarPersona();
                        Swal.fire(
                        'Activado!',
                        'El registro ha sido activado con éxito.',
                        'success'
                        )
                    }).catch(function (error) {
                        console.log(error);
                    });
                    
                    
                } else if (
                    // Read more about handling dismissals
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    
                }
                });
            }
            , carpetaDescartar (recursoId)
            {
                Swal.fire({
                    title                : 'Esta seguro de descartar esta carpeta?'
                    , type               : 'warning'
                    , showCancelButton   : true
                    , confirmButtonColor : '#3085d6'
                    , cancelButtonColor  : '#d33'
                    , confirmButtonText  : 'Aceptar!'
                    , cancelButtonText   : 'Cancelar'
                    , confirmButtonClass : 'btn btn-pill btn-success'
                    , cancelButtonClass  : 'btn btn-pill btn-danger'
                    , buttonsStyling     : false
                    , reverseButtons     : true
                })
                .then((result) => {
                    if (result.value) {
                        let url = `${this.ruta}/usuarios_carpetas/${recursoId}`, me = this;
                        axios.delete(url).then(function (response) {
                            Swal.fire('Descartado!', 'El registro ha sido descartado con éxito.', 'success');
                            me.carpetaListar();
                        }).catch(function (error) {
                            console.log(error);
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {}
                });
            }
            , documentoDescartar (recursoId)
            {
                Swal.fire({
                    title                : '¿Esta seguro de descartar este documento?'
                    , type               : 'warning'
                    , showCancelButton   : true
                    , confirmButtonColor : '#3085d6'
                    , cancelButtonColor  : '#d33'
                    , confirmButtonText  : 'Aceptar!'
                    , cancelButtonText   : 'Cancelar'
                    , confirmButtonClass : 'btn btn-pill btn-success btn-lg'
                    , cancelButtonClass  : 'btn btn-pill btn-danger btn-lg'
                    , buttonsStyling     : false
                    , reverseButtons     : true
                })
                .then((result) => {
                    if (result.value) {
                        let url = `${this.ruta}/usuarios_documentos/${recursoId}`, me = this;
                        axios.delete(url).then(function (response) {
                            Swal.fire('Descartado!', 'El registro ha sido descartado con éxito.', 'success');
                            me.documentoListar();
                            me.listarPersona();
                        }).catch(function (error) {
                            console.log(error);
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {}
                });
            }
            , cargoSimple ()
            {
                let ruta = this.ruta +'/'+ this.cargoRuta +'/simple', me = this;
                axios.get(ruta).then(response => me.cargoSimpleArr = response.data.cargos).catch(error => console.log(error));
            }
        }
        , mounted ()
        {
            this.cargoSimple();
            this.basicoCargosConDocumentos();
            this.listarPersona();
            /*$('#usuariocarpeta-crud-mdl').on('hidden.bs.modal', (e) => {
                console.log(e.relatedTarget);
                this.$refs.personaocTextoBuscar.focus();
            });*/
            $('#usuariocarpeta-mdl').on('shown.bs.modal', () => this.$refs.carpetacbCarpeta.focus());
        }
    }
</script>
<style>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .mostrar{
        display: list-item !important;
        opacity: 1 !important;
        position: absolute !important;
        background-color: #3c29297a !important;
    }
    .div-error{
        display: flex;
        justify-content: center;
    }
    .text-error{
        color: red !important;
        font-weight: bold;
    }
    .docs-pends{
        font-size : .78rem;
    }
</style>