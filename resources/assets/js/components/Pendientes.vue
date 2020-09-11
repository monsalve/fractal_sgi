<template>
    <main class="main">
        <!-- Breadcrumb -->
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Escritorio</a></li>
        </ol>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-auto">
                            <i class="fa fa-thumb-tack fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;Pendientes&nbsp;&nbsp;
                            <button type="button" @click="abrirModal(pendienteRuta +'Registrar')" class="btn btn-pill btn-primary btn-sm" v-if="permisosUser.crear">
                                <i class="icon-plus"></i>&nbsp;Nuevo
                            </button>
                            <button type="button" class="btn btn-pill btn-secondary btn-sm" v-else>
                                <i class="icon-plus"></i>&nbsp;Nuevo
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-auto my-1 pr-1">Pendiente</label>
                        <div class="col-md-2 pl-1">
                            <input type="text" class="form-control" v-model="pendienteObjCons.pendiente">
                        </div>
                        <label class="col-md-auto my-1 pr-1">Proyecto</label>
                        <div class="col-md-2 pl-1">
                            <select class="form-control" v-model="pendienteObjCons.fkIdProyectos">
                                <option value="">Todos</option>
                                <option :value="proyecto.id" v-text="proyecto.proyecto" v-for="proyecto in proyectoArr" :key="proyecto.id"></option>
                            </select>
                        </div>
                        <label class="col-md-auto my-1 pr-1">Condición</label>
                        <div class="col-md-auto pl-1">
                            <select class="form-control" v-model="pendienteObjCons.condicion">
                                <option value="">Todos</option>
                                <option value="1">Abierto</option>
                                <option value="2">Cerrado</option>
                            </select>
                        </div>
                        <label class="col-md-auto my-1 pr-1">Estado</label>
                        <div class="col-md-auto pl-1">
                            <select class="form-control" v-model="pendienteObjCons.estado">
                                <option value="">Todos</option>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                        <label class="col-md-auto my-1 pr-1">Ver</label>
                        <div class="col-md-auto pl-1">
                            <select class="form-control" @change="cambiarPagina(1, pendienteObjCons, pendientePagin, 1)" v-model="pendienteObjCons.numRegs">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <button type="button" @click="cambiarPagina(1, pendienteObjCons, pendientePagin, 1)" class="btn btn-pill btn-primary btn-sm">
                            <i class="fa fa-search"></i>&nbsp;Buscar
                        </button>
                    </div>
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <th class="text-center">#</th>
                            <th class="text-center">Fecha</th>
                            <th class="text-center">Pendiente</th>
                            <th class="text-center">Proyecto</th>
                            <th class="text-center">Condición</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Asignaciones</th>
                            <th class="text-center">Cierre</th>
                            <th class="text-center">Opciones</th>
                        </thead>
                        <tbody>
                            <template v-if="pendienteArr.length == 0">
                                <tr>
                                    <td class="text-center" colspan="9">No hay registros</td>
                                </tr>
                            </template>
                            <template v-else>
                                <tr v-for="(pendiente, index) in pendienteArr" :key="pendiente.id">
                                    <td class="text-center" v-text="index + 1"></td>
                                    <td class="text-center" v-text="pendiente.fecha.split('-').reverse().join('/')"></td>
                                    <td v-text="pendiente.pendiente"></td>
                                    <td v-text="pendiente.proyecto"></td>
                                    <td class="text-center">
                                        <span class="badge" :class="'badge-' + (pendiente.condicion == 'Abierto'? 'dark':'light')" v-text="pendiente.condicion"></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge" :class="'badge-' + (pendiente.estado ? 'success':'danger')" v-text="pendiente.estado ? 'Activo':'Inactivo'"></span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" @click="abrirModal(asignacionRuta +'Listar', {pendienteId: pendiente.id, pendiente: pendiente.pendiente, pendienteCondicion: pendiente.condicion, pendienteUsuCrea: pendiente.fk_usuCrea_users})" class="btn btn-info btn-sm rounded" title="Ver asignaciones" v-if="pendiente.estado">
                                            <i class="fa fa-files-o" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                            <i class="fa fa-files-o" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm rounded" @click="pendienteCierreValidar(pendiente.condicion, 'este pendiente', pendienteRuta, pendiente.id)" title="Cerrar pendiente" v-if="pendiente.condicion == 'Abierto' && pendiente.estado && (/*¿es super? ||*/ id == pendiente.fk_usuCrea_users)">
                                            <i :class="'icon-lock'+ (pendiente.condicion == 'Cerrado'? '' : '-open')" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm rounded" v-else>
                                            <i :class="'icon-lock'+ (pendiente.condicion == 'Cerrado'? '' : '-open')" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" @click="abrirModal(pendienteRuta +'Actualizar', pendiente)" class="btn rounded btn-warning btn-sm" title="Editar" v-if="(/*¿es super? ||*/ id == pendiente.fk_usuCrea_users) && pendiente.condicion == 'Abierto' && pendiente.estado && permisosUser.actualizar">
                                            <i class="icon-pencil" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                            <i class="icon-pencil" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" @click="actualizarEstado(pendiente.estado, 'este pendiente', pendienteRuta, pendiente.id)" class="btn rounded btn-sm" :class="'btn-'+ (pendiente.estado ? 'danger':'success')" :title="pendiente.estado ? 'Anular':'Activar'" v-if="(/*¿es super? ||*/ id == pendiente.fk_usuCrea_users) && pendiente.condicion == 'Abierto'">
                                            <i :class="'icon-'+ (pendiente.estado ? 'trash':'check')" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                            <i :class="'icon-'+ (pendiente.estado ? 'trash':'check')" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <nav>
                        <ul class="pagination">
                            <li class="page-item" v-if="pendientePagin.current_page > 1">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(pendientePagin.current_page - 1, pendienteObjCons, pendientePagin, 1)">Ant</a>
                            </li>
                            <li class="page-item" v-for="page in pendientePagesNumber" :key="page" :class="[page == pendientePagin.current_page ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(page, pendienteObjCons, pendientePagin, 1)" v-text="page"></a>
                            </li>
                            <li class="page-item" v-if="pendientePagin.current_page < pendientePagin.last_page">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(pendientePagin.current_page + 1, pendienteObjCons, pendientePagin, 1)">Sig</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        <div id="pendiente-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="pendienteTituloMdl+ ' pendiente'"></h4>
                        <button type="button" class="close" @click="pendienteCerrarMdl()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="row">
                                <div class="col">
                                    <div class="form-row justify-content-center">
                                        <div class="form-group col-md">
                                            <label>Fecha</label>
                                            <input type="date" class="form-control" :class="{'is-invalid' : noHayFecha}" v-model="pendienteBody.fecha">
                                        </div>
                                    </div>
                                    <div class="form-row justify-content-center">
                                        <div class="form-group col-md">
                                            <label>Pendiente</label>
                                            <input type="text" class="form-control" :class="{'is-invalid' : noHayPendiente}" v-model="pendienteBody.pendiente">
                                        </div>
                                    </div>
                                    <div class="form-row justify-content-center">
                                        <div class="form-group col-md">
                                            <label>Proyecto</label>
                                            <select class="form-control" :class="{'is-invalid' : noHayProyecto}" v-model="pendienteBody.fk_id_proyectos">
                                                <option value="">Seleccione</option>
                                                <option :value="proyecto.id" v-text="proyecto.proyecto" v-for="proyecto in proyectoArr" :key="proyecto.id"></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-row justify-content-center">
                                        <div class="form-group col-md">
                                            <label>Observación</label>
                                            <textarea class="form-control" :class="{'is-invalid' : noHayObservacion}" rows="4" v-model="pendienteBody.observacion"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-pill btn-danger btn-sm" @click="pendienteCerrarMdl()">Cerrar</button>
                        <button type="button" class="btn btn-pill btn-success btn-sm" @click="tipoAccion == 'Guardar' ? pendienteRegistrar():pendienteActualizar()" v-text="tipoAccion"></button>
                    </div>
                </div>
            </div>
        </div>
        <div id="asignacioncrud-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" style="max-width:900px;" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="'Pendiente - '+ pendienteTituloMdl"></h4>
                        <button type="button" class="close" @click="asignacionMdlCerrar()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-auto">
                                        <i class="fa fa-pencil-square-o fa-lg" aria-hidden="true"></i>&nbsp;Asignaciones&nbsp;
                                    </div>
                                    <button type="button" @click="abrirModal(asignacionRuta +'Registrar')" class="btn btn-pill btn-primary btn-sm" v-if="pendienteCondicion == 'Abierto' && id == pendienteUsuCrea">
                                        <i class="icon-plus"></i>&nbsp;Nuevo
                                    </button>
                                    <button type="button" class="btn btn-pill btn-secondary btn-sm" v-else>
                                        <i class="icon-plus"></i>&nbsp;Nuevo
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-md-auto my-1 pr-1">Responsable</label>
                                    <div class="col-md-3 pl-1">
                                        <select ref="asignacionOCDocumento" class="form-control" v-model="asignacionObjCons.fkResponsUsers">
                                            <option value="">Todos</option>
                                            <option :value="responsable.id" v-text="responsable.nombre_completo" v-for="responsable in responsableArr" :key="responsable.id"></option>
                                        </select>
                                    </div>
                                    <label class="col-md-auto my-1 pr-1">Condición</label>
                                    <div class="col-md-auto pl-1">
                                        <select class="form-control" v-model="asignacionObjCons.condicion">
                                            <option value="">Todos</option>
                                            <option value="1">Abierto</option>
                                            <option value="2">Cerrado</option>
                                        </select>
                                    </div>
                                    <label class="col-md-auto my-1 pr-1">Estado</label>
                                    <div class="col-md-auto pl-1">
                                        <select class="form-control" v-model="asignacionObjCons.estado">
                                            <option value="">Todos</option>
                                            <option value="1">Activo</option>
                                            <option value="0">Inactivo</option>
                                        </select>
                                    </div>
                                    <label class="col-md-auto my-1 pr-1">Ver</label>
                                    <div class="col-md-auto pl-1">
                                        <select class="form-control" @change="cambiarPagina(1, asignacionObjCons, asignacionPagin, 2)" v-model="asignacionObjCons.numRegs">
                                            <option value="5">5</option>
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                    </div>
                                    <button type="button" @click="cambiarPagina(1, asignacionObjCons, asignacionPagin, 2)" class="btn btn-pill btn-primary btn-sm">
                                        <i class="fa fa-search"></i>&nbsp;Buscar
                                    </button>
                                </div>
                                <table class="table table-bordered table-striped table-sm">
                                    <thead>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Fecha límite</th>
                                        <th class="text-center">Asignación</th>
                                        <th class="text-center">Responsable</th>
                                        <th class="text-center">Condición</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-center">Evidencias</th>
                                        <th class="text-center">Apertura</th>
                                        <th class="text-center">Opciones</th>
                                        <th class="text-center">Al cerrar</th>
                                        <th class="text-center">Al abrir</th>
                                    </thead>
                                    <tbody>
                                        <template v-if="asignacionArr.length == 0">
                                            <tr>
                                                <td class="text-center" colspan="11">No hay registros</td>
                                            </tr>
                                        </template>
                                        <template v-else>
                                            <tr v-for="(asignacion, index) in asignacionArr" :key="asignacion.id">
                                                <td class="text-center" v-text="index + 1"></td>
                                                <td class="text-center" v-text="asignacion.fechaLimite.split('-').reverse().join('/')"></td>
                                                <td v-text="asignacion.asignacion"></td>
                                                <td v-text="asignacion.nombre_completo"></td>
                                                <td class="text-center">
                                                    <span class="badge" :class="'badge-' + (asignacion.condicion == 'Abierto'? 'dark':'light')" v-text="asignacion.condicion"></span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge" :class="'badge-' + (asignacion.estado ? 'success':'danger')" v-text="asignacion.estado ? 'Activo':'Inactivo'"></span>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" @click="abrirModal(evidenciaRuta +'Listar', {asignacionId: asignacion.id, asignacion: asignacion.asignacion, condicion: asignacion.condicion, respId: asignacion.fk_respons_users, resp: asignacion.nombre_completo})" class="btn btn-info btn-sm rounded" title="Ver evidencias" v-if="asignacion.estado">
                                                        <i class="fa fa-files-o" aria-hidden="true"></i>
                                                    </button>
                                                    <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                        <i class="fa fa-files-o" aria-hidden="true"></i>
                                                    </button>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-info btn-sm rounded" @click="abrirModal(asignacionRuta +'Condicion', {asignacionId: asignacion.id, asignacion: asignacion.asignacion, condicion: asignacion.condicion})" title="Reabrir asignación" v-if="pendienteCondicion == 'Abierto' && asignacion.condicion == 'Cerrado' && asignacion.estado && id == pendienteUsuCrea">
                                                        <i :class="'icon-lock'+ (asignacion.condicion == 'Cerrado'? '' : '-open')" aria-hidden="true"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-secondary btn-sm rounded" v-else>
                                                        <i :class="'icon-lock'+ (asignacion.condicion == 'Cerrado'? '' : '-open')" aria-hidden="true"></i>
                                                    </button>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" @click="abrirModal(asignacionRuta +'Actualizar', asignacion)" class="btn rounded btn-warning btn-sm" title="Editar" v-if="asignacion.condicion == 'Abierto' && asignacion.estado && id == pendienteUsuCrea">
                                                        <i class="icon-pencil" aria-hidden="true"></i>
                                                    </button>
                                                    <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                        <i class="icon-pencil" aria-hidden="true"></i>
                                                    </button>
                                                    <button type="button" @click="actualizarEstado(asignacion.estado, 'esta asignacion', asignacionRuta, asignacion.id)" class="btn rounded btn-sm" :class="'btn-'+ (asignacion.estado ? 'danger':'success')" :title="asignacion.estado ? 'Anular':'Activar'" v-if="id == pendienteUsuCrea && asignacion.condicion == 'Abierto'">
                                                        <i :class="'icon-'+ (asignacion.estado ? 'trash':'check')" aria-hidden="true"></i>
                                                    </button>
                                                    <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                        <i :class="'icon-'+ (asignacion.estado ? 'trash':'check')" aria-hidden="true"></i>
                                                    </button>
                                                </td>
                                                <td v-html="asignacion.alCerrar"></td>
                                                <td v-html="asignacion.alAbrir"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                                <nav>
                                    <ul class="pagination">
                                        <li class="page-item" v-if="asignacionPagin.current_page > 1">
                                            <a class="page-link" href="#" @click.prevent="cambiarPagina(asignacionPagin.current_page - 1, asignacionObjCons, asignacionPagin, 2)">Ant</a>
                                        </li>
                                        <li class="page-item" v-for="page in asignacionPagesNumber" :key="page" :class="[page == asignacionPagin.current_page ? 'active' : '']">
                                            <a class="page-link" href="#" @click.prevent="cambiarPagina(page, asignacionObjCons, asignacionPagin, 2)" v-text="page"></a>
                                        </li>
                                        <li class="page-item" v-if="asignacionPagin.current_page < asignacionPagin.last_page">
                                            <a class="page-link" href="#" @click.prevent="cambiarPagina(asignacionPagin.current_page + 1, asignacionObjCons, asignacionPagin, 2)">Sig</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-pill btn-primary btn-sm" @click="asignacionMdlCerrar()">Atrás</button>  
                    </div>
                </div>
            </div>
        </div>
        <div id="asignacion-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="asignacionTituloMdl+ ' asignación'"></h4>
                        <button type="button" class="close" @click="asignacionMdlMdlCerrar()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-row justify-content-center">
                                <div class="form-group col-md-4">
                                    <label>Fecha límite</label>
                                    <input type="date" class="form-control" :class="{'is-invalid' : noHayFechaLimite}" v-model="asignacionBody.fechaLimite">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Responsable</label>
                                    <select class="form-control" :class="{'is-invalid' : noHayResponsable}" v-model="asignacionBody.fk_respons_users">
                                        <option value="">Seleccione</option>
                                        <option :value="responsable.id" v-text="responsable.nombre_completo" v-for="responsable in responsableArr" :key="responsable.id"></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row justify-content-center">
                                <div class="form-group col-md-4">
                                    <label>Asignación</label>
                                    <input type="text" class="form-control" :class="{'is-invalid' : noHayAsignacion}" v-model="asignacionBody.asignacion">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Observación</label>
                                    <textarea class="form-control" :class="{'is-invalid' : noHayObservacion2}" rows="4" v-model="asignacionBody.observacion"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-pill btn-danger btn-sm" @click="asignacionMdlMdlCerrar()">Cerrar</button>
                        <button type="button" class="btn btn-pill btn-success btn-sm" @click="tipoAccion == 'Guardar' ? asignacionRegistrar():asignacionActualizar()" v-text="tipoAccion"></button>
                    </div>
                </div>
            </div>
        </div>
        <div id="asignacioncond-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-primary" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="'Asignación - '+ asignacionTituloMdl"></h4>
                        <button type="button" class="close" @click="asignacionCondMdlCerrar()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-row justify-content-center">
                                <div class="form-group col-11">
                                    <label for="asignacionBAlAbrir">Observación</label>
                                    <textarea id="asignacionBAlAbrir" ref="asignacionBAlAbrir" class="form-control" :class="{'is-invalid': noHayAl}" rows="4" v-model="asignacionAl"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-pill btn-sm" @click="asignacionCondMdlCerrar()">Cancelar</button>
                        <button type="button" class="btn btn-success btn-pill btn-sm" @click="asignacionCondActualizar()">Reabrir</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="evidenciacrud-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-primary" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="'Asignación - '+ asignacionTituloMdl"></h4>
                        <button type="button" class="close" @click="evidenciaMdlCerrar()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row justify-content-center">
                            <div class="col-md-8 mb-3">
                                <label>Observación</label>
                                <textarea class="form-control" :class="{'is-invalid': noHayAl}" rows="2" v-model="asignacionAl"></textarea>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-11">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-md-auto">
                                                <i class="fa fa-files-o fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;Evidencias&nbsp;
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group row justify-content-start">
                                            <div class="col-md-4">
                                                <input type="text" ref="evidenciaBEvidencia" v-model="evidenciaBody.evidencia" class="form-control" :class="{'is-invalid': noHayEvidencia}" placeholder="Nombre de evidencia" maxlength="50">
                                            </div>
                                            <div class="col-md-5">
                                                <input type="file" ref="inputRuta" class="form-control form-control-file" :class="{'is-invalid' : noHayRuta}" @change="obtenerArchivo">
                                            </div>
                                            <button type="button" id="evidenciaguardar" class="btn btn-pill btn-info btn-sm" @click="evidenciaRegistrar();$refs.evidenciaBEvidencia.focus();" v-if="asignacionCondicion == 'Abierto' && id == asignacionRespId">
                                                <i class="fa fa-upload" aria-hidden="true"></i>&nbsp;Subir evidencia
                                            </button>
                                            <button type="button" class="btn btn-pill btn-secondary btn-sm" v-else>
                                                <i class="fa fa-upload" aria-hidden="true"></i>&nbsp;Subir evidencia
                                            </button>
                                        </div>
                                        <table class="table table-bordered table-striped table-sm">
                                            <thead>
                                                <th class="text-center">#</th>
                                                <th class="text-center">Evidencia</th>
                                                <th class="text-center">Estado</th>
                                                <th class="text-center">Opciones</th>
                                            </thead>
                                            <tbody>
                                                <template v-if="evidenciaArr.length == 0">
                                                    <tr>
                                                        <td class="text-center" colspan="4">No hay registros</td>
                                                    </tr>
                                                </template>
                                                <template v-else>
                                                    <tr v-for="(evidencia, index) in evidenciaArr" :key="evidencia.id">
                                                        <td class="text-center" v-text="index + 1"></td>
                                                        <td v-text="evidencia.evidencia"></td>
                                                        <td class="text-center">
                                                            <span class="badge" :class="'badge-' + (evidencia.estado ? 'success':'danger')" v-text="evidencia.estado ? 'Activo':'Inactivo'"></span>
                                                        </td>
                                                        <td class="text-center">
                                                            <a type="button" title="Visualizar" :href="evidencia.ruta" target="_blank" class="btn rounded btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                                            <button type="button" class="btn rounded btn-danger btn-sm" @click="descartar(index, evidencia.id)" v-if="asignacionCondicion == 'Abierto' && id == asignacionRespId">
                                                                <i class="fa fa-times" aria-hidden="true"></i>
                                                            </button>
                                                            <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                                <i class="fa fa-times" aria-hidden="true"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-pill btn-danger btn-sm" @click="evidenciaMdlCerrar()">Atrás</button>
                        <button type="button" class="btn btn-pill btn-success btn-sm" @click="asignacionCondActualizar()" v-if="asignacionCondicion == 'Abierto' && id == asignacionRespId">
                            <i class="fa fa-lock" aria-hidden="true"></i>&nbsp;Guardar
                        </button>
                        <button type="button" class="btn btn-secondary btn-pill btn-sm" v-else>
                            <i class="fa fa-lock" aria-hidden="true"></i>&nbsp;Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>
<script>
export default {
    props: ['permisosUser', 'ruta', 'axiosApp']
    , data () {
        return {
            id : 0
            , offset : 3 
            , tipoAccion : 'Guardar'
            , userRuta : 'users'
            
            , pendienteRuta : 'pendientes'
            , proyectoArr : []
            , pendienteObjCons : {
                '?page' : 1
                , pendiente : ''
                , fkIdProyectos : ''
                , condicion : ''
                , estado : ''
                , numRegs : 10
                , leer : this.permisosUser.leer
            }
            , pendienteArr : []
            , pendienteTituloMdl : 'Nuevo'
            , pendienteOnVacios : false
            , pendienteId : ''
            , pendienteCondicion : ''
            , pendienteUsuCrea : ''
            , pendienteBody : {
                fecha : ''
                , pendiente : ''
                , observacion : ''
                , fk_id_proyectos : ''
            }
            , pendienteParamsCierre : {}
            , pendientePagin : {
                total : 0
                , current_page : 0
                , per_page : 0
                , last_page : 0
                , from : 0
                , to : 0
            }

            , asignacionRuta : 'pendiente_asignaciones'
            , responsableArr : []
            , asignacionObjCons : {
                '?page' : 1
                , fkResponsUsers : ''
                , condicion : ''
                , estado : ''
                , fkIdPendientes : ''
                , numRegs : 10
            }
            , asignacionArr : []
            , asignacionTituloMdl : 'Nueva'
            , asignacionOnVacios : false
            , asignacionId : ''
            , asignacionAl : ''
            , asignacionCondicion : ''
            , asignacionRespId : ''
            , asignacionResp : ''
            , asignacionBody : {
                fechaLimite : ''
                , asignacion : ''
                , observacion : ''
                , fk_id_pendientes : ''
                , fk_respons_users : ''
            }
            , asignacionPagin : {
                total : 0
                , current_page : 0
                , per_page : 0
                , last_page : 0
                , from : 0
                , to : 0
            }

            , evidenciaRuta : 'pendiente_evidencias'
            , evidenciaArr : []
            , evidenciaOnVacios : false
            , evidenciaId : ''
            , evidenciaBody : {
                evidencia : ''
                , ruta : ''
                , fk_id_asignaciones : ''
            }
            , recursoIdx : 0
        }
    }
    , computed :
    {
        pendientePagesNumber : function ()
        {
            if (!this.pendientePagin.to) {
                return [];
            }
            let from = this.pendientePagin.current_page - this.offset; 
            if (from < 1) {
                from = 1;
            }
            let to = from + (this.offset * 2); 
            if (to >= this.pendientePagin.last_page) {
                to = this.pendientePagin.last_page;
            }  
            let pagesArray = [];
            while (from <= to) {
                pagesArray.push(from);
                from++;
            }
            return pagesArray;
        }
        , asignacionPagesNumber : function ()
        {
            if (!this.asignacionPagin.to) {
                return [];
            }
            let from = this.asignacionPagin.current_page - this.offset; 
            if (from < 1) {
                from = 1;
            }
            let to = from + (this.offset * 2); 
            if (to >= this.asignacionPagin.last_page) {
                to = this.asignacionPagin.last_page;
            }  
            let pagesArray = [];
            while (from <= to) {
                pagesArray.push(from);
                from++;
            }
            return pagesArray;
        }
        // VALIDAR CAMPOS FORMULARIO
        // Campos recurso pendiente
        , noHayFecha : function ()
        {
            return this.pendienteOnVacios && !this.pendienteBody.fecha;
        }
        , noHayPendiente : function ()
        {
            return this.pendienteOnVacios && !this.pendienteBody.pendiente.trim();
        }
        , noHayObservacion : function ()
        {
            return this.pendienteOnVacios && !this.pendienteBody.observacion.trim();
        }
        , noHayProyecto : function ()
        {
            return this.pendienteOnVacios && !this.pendienteBody.fk_id_proyectos;
        }
        // Campos recurso asignacion
        , noHayFechaLimite : function ()
        {
            return this.asignacionOnVacios && !this.asignacionBody.fechaLimite;
        }
        , noHayResponsable : function ()
        {
            return this.asignacionOnVacios && !this.asignacionBody.fk_respons_users;
        }
        , noHayAsignacion : function ()
        {
            return this.asignacionOnVacios && !this.asignacionBody.asignacion.trim();
        }
        , noHayObservacion2 : function ()
        {
            return this.asignacionOnVacios && !this.asignacionBody.observacion.trim();
        }
        , noHayAl : function ()
        {
            return this.asignacionOnVacios && !this.asignacionAl.trim();
        }
        // Campos recurso asignacion
        , noHayEvidencia : function ()
        {
            return this.evidenciaOnVacios && !this.evidenciaBody.evidencia.trim();
        }
        , noHayRuta : function ()
        {
            return this.evidenciaOnVacios && !this.evidenciaBody.ruta;
        }
    }
    , methods :
    {
        abrirModal (accion, data = {}) {
            switch (accion) {
                case this.pendienteRuta +'Registrar':
                    $('#pendiente-mdl').modal('show');
                    break;
                case this.pendienteRuta +'Actualizar':
                    this.pendienteTituloMdl = 'Editar';
                    this.pendienteId = data.id;
                    this.pendienteBody.fecha = data.fecha;
                    this.pendienteBody.pendiente = data.pendiente;
                    this.pendienteBody.observacion = data.observacion;
                    this.pendienteBody.fk_id_proyectos = data.fk_id_proyectos;
                    this.tipoAccion = 'Actualizar';
                    $('#pendiente-mdl').modal('show');
                    break;
                case this.asignacionRuta +'Listar':
                    this.pendienteTituloMdl = data.pendiente;
                    this.asignacionObjCons.fkIdPendientes = this.asignacionBody.fk_id_pendientes = data.pendienteId;
                    this.pendienteCondicion = data.pendienteCondicion;
                    this.pendienteUsuCrea = data.pendienteUsuCrea;
                    this.asignacionListar();
                    $('#asignacioncrud-mdl').modal('show');
                    break;
                case this.asignacionRuta +'Registrar':
                    $('#asignacioncrud-mdl').modal('hide');
                    $('#asignacion-mdl').modal('show');
                    break;
                case this.asignacionRuta +'Actualizar':
                    $('#asignacioncrud-mdl').modal('hide');
                    this.asignacionTituloMdl = 'Editar';
                    this.asignacionId = data.id;
                    this.asignacionBody.fechaLimite = data.fechaLimite;
                    this.asignacionBody.fk_respons_users = data.fk_respons_users;
                    this.asignacionBody.asignacion = data.asignacion;
                    this.asignacionBody.observacion = data.observacion;
                    this.tipoAccion = 'Actualizar';
                    $('#asignacion-mdl').modal('show');
                    break;
                case this.asignacionRuta +'Condicion':
                    this.asignacionTituloMdl = data.asignacion;
                    this.asignacionId = data.asignacionId;
                    this.asignacionCondicion = data.condicion;
                    $('#asignacioncrud-mdl').modal('hide');
                    $('#asignacioncond-mdl').modal('show');
                    break;
                case this.evidenciaRuta +'Listar':
                    $('#asignacioncrud-mdl').modal('hide');
                    this.asignacionTituloMdl = data.asignacion;
                    this.evidenciaBody.fk_id_asignaciones = this.asignacionId = data.asignacionId;
                    this.asignacionCondicion = data.condicion;
                    this.asignacionRespId = data.respId;
                    this.asignacionResp = data.resp;
                    this.evidenciaListar();
                    $('#evidenciacrud-mdl').modal('show');
                    break;
                /*case this.evidenciaRuta +'Actualizar':
                    $('#evidenciacrud-mdl').modal('hide');
                    this.evidenciaId = data.evidenciaId;
                    this.evidenciaBody.evidencia = data.evidencia;
                    $('#evidencia-mdl').modal('show');*/
            }
        }
        , cambiarPagina (page, refObjCons, refPagin, listar)
        {
            refObjCons['?page'] = refPagin.current_page = page;
            switch (listar) {
                case 1: this.pendienteListar();break;
                case 2: this.asignacionListar();break;
            }
        }
        , obtenerRespuesta(uri, peticion)
        {
            let me = this;
            peticion.then(function (response) {
                let respuesta = response.data;
                if (uri.endsWith('desactivar/') || uri.endsWith('activar/')) Swal.fire(me.tituloSwal, me.descripcionSwal, 'success');
                switch (uri) {
                    case me.userRuta +'/id':
                        me.id = respuesta.id;
                        break;
                    case me.pendienteRuta +'/proyectos':
                        me.proyectoArr = respuesta.proyectos;
                        break;
                    case me.pendienteRuta:
                        me.pendienteArr = respuesta.pendientes.data;
                        me.pendientePagin = respuesta.pagination;
                        break;
                    case me.pendienteRuta +'/registrar':
                    case me.pendienteRuta +'/actualizar/':
                        me.pendienteCerrarMdl();
                        me.pendienteListar();
                        break;
                    case me.pendienteRuta +'/valida_cierre/':
                        switch (respuesta.indicaCierre) {
                            case -1: Swal.fire('Sin asignaciones!', 'Este pendiente requiere al menos una asignación', 'info');break;
                            case 0: me.actualizarCondicion(me.pendienteParamsCierre.recursoCondicion
                                , me.pendienteParamsCierre.finalTitle
                                , me.pendienteParamsCierre.recursoRuta
                                , me.pendienteParamsCierre.pendienteId);break;
                            case 1: Swal.fire('Asignaciones abiertas!', 'Hay asignaciones por cerrar en este pendiente', 'info');break;
                        }
                        break;
                    case me.pendienteRuta +'/desactivar/':
                    case me.pendienteRuta +'/activar/':
                        me.pendienteListar();
                        break;
                    case me.asignacionRuta +'/users':
                        me.responsableArr = respuesta.users;
                        break;
                    case me.asignacionRuta:
                        me.asignacionArr = respuesta.asignaciones.data;
                        me.asignacionPagin = respuesta.pagination;
                        break;
                    case me.asignacionRuta +'/registrar':
                    case me.asignacionRuta +'/actualizar/':
                        me.asignacionMdlMdlCerrar();
                        me.asignacionListar();
                        break;
                    case me.asignacionRuta +'/cerrar/':
                        me.evidenciaMdlCerrar();
                        me.asignacionListar();
                        break;
                    case me.asignacionRuta +'/abrir/':
                        me.asignacionCondMdlCerrar();
                        me.asignacionListar();
                        break;
                    case me.asignacionRuta +'/desactivar/':
                    case me.asignacionRuta +'/activar/':
                        me.asignacionListar();
                        break;
                    case me.evidenciaRuta:
                        me.evidenciaArr = respuesta.evidencias;
                        break;
                    /*case me.evidenciaRuta +'/desactivar/':
                    case me.evidenciaRuta +'/activar/': 
                        me.evidenciaListar();
                        break;*/
                    case me.evidenciaRuta +'/descartar/':
                        Swal.fire('Registro descartado!', 'La evidencia ha sido descartada con éxito', 'success');
                        me.evidenciaArr.splice(me.recursoIdx, 1);
                        break;
                }
            })
            .catch(function (error) {
                console.log(error);
            });
        }
        , esRecursoInvalido (validar)
        {
            switch (validar)
            {
                case 1: if (this.pendienteValidar()) return true;
                    break;
                case 2: if (this.asignacionValidar()) return true;
                    break;
                case 3: if (this.evidenciaValidar()) return true;
                    break;
            }
            return false;
        }
        , peticionGet (recursoRuta, cadCons = '')
        {
            let peticion = this.axiosApp.get(recursoRuta + cadCons);
            this.obtenerRespuesta(recursoRuta, peticion);
        }
        , peticionPost (recursoRuta, recursoBody, validar)
        {
            if (this.esRecursoInvalido(validar))return;
            let peticion = this.axiosApp.post(recursoRuta, recursoBody);
            this.obtenerRespuesta(recursoRuta, peticion);
        }
        , peticionPut (recursoRuta, recursoId, recursoBody, validar)
        {
            if (this.esRecursoInvalido(validar))return;
            let peticion = this.axiosApp.put(recursoRuta + recursoId, recursoBody);
            this.obtenerRespuesta(recursoRuta, peticion);
        }
        , estadoPatch (recursoRuta, recursoId, recursoEstado)
        {
            this.tituloSwal = 'Activado';
            this.descripcionSwal = 'Registro activado con éxito.';
            if (recursoEstado) {
                this.tituloSwal = 'Desactivado';
                this.descripcionSwal = 'Registro desactivado con éxito.';
            }
            let peticion = this.axiosApp.patch(recursoRuta + recursoId);
            this.obtenerRespuesta(recursoRuta, peticion);
        }
        , condPatch (ruta, recursoId, condicion, recursoBody)
        {
            this.tituloSwal = 'Reanudada';
            this.descripcionSwal = 'Asignación reanudada con éxito.';
            if (condicion == 'Abierto') {
                this.tituloSwal = 'Finalizada';
                this.descripcionSwal = 'Asignación finalizada con éxito.';
            }
            let peticion = this.axiosApp.patch(ruta + recursoId, recursoBody);
            this.obtenerRespuesta(ruta, peticion);
        }
        , peticionDelete (ruta, recursoId)
        {
            let peticion = this.axiosApp.delete(ruta + recursoId);
            this.obtenerRespuesta(ruta, peticion);
        }
        , actualizarEstado (recursoEstado, finalTitle, recursoRuta, recursoId)
        {
            let prefijo = recursoEstado ? 'des' : '';
            Swal.fire({
                title: `Esta seguro de ${prefijo}activar ${finalTitle}?`
                , type: 'warning'
                , showCancelButton: true
                , confirmButtonColor: '#3085d6'
                , cancelButtonColor: '#d33'
                , confirmButtonText: 'Aceptar!'
                , cancelButtonText: 'Cancelar'
                , confirmButtonClass: 'btn btn-pill btn-success btn-lg'
                , cancelButtonClass: 'btn btn-pill btn-danger btn-lg'
                , buttonsStyling: false
                , reverseButtons: true
                }).then((result) => {
                if (result.value) {
                    let ruta = `${recursoRuta}/${prefijo}activar/`;
                    this.estadoPatch(ruta, recursoId, recursoEstado);
                } else if (result.dismiss === Swal.DismissReason.cancel /* Read more about handling dismissals */) {}
            });
        }
        // EL ARGUMENTO recursoCondicion ESTA POR SI SE PLANEA PERMITIR ALTERNAR LA CONDICION DEL PENDIENTE EN EL FUTURO
        , actualizarCondicion (recursoCondicion, finalTitle, recursoRuta, recursoId)
        {
            let accion = 'cerrar';
            Swal.fire({
                title: `Esta seguro de ${accion} ${finalTitle}?`
                , text: 'No podrás gestionar más asignaciones'
                , type: 'warning'
                , showCancelButton: true
                , confirmButtonText: 'Aceptar!'
                , cancelButtonText: 'Cancelar'
                , confirmButtonClass: 'btn btn-pill btn-success btn-lg'
                , cancelButtonClass: 'btn btn-pill btn-danger btn-lg'
                , buttonsStyling: false
                , reverseButtons: true
                }).then((result) => {
                if (result.value) {
                    let ruta = `${recursoRuta}/${accion}/${recursoId}`, me = this;
                    axios.patch(ruta).then(response => {
                        //Swal.fire(accion == 'cerrar'? 'Cerrado' : 'Abierto', `Pendiente ${accion == 'cerrar'? 'cerrado' : 'abierto'} con éxito`, 'success');
                        Swal.fire('Cerrado', `Pendiente cerrado con éxito`, 'success');
                        me.pendienteListar();
                    })
                    .catch(error => console.log(error));
                } else if (result.dismiss === Swal.DismissReason.cancel /* Read more about handling dismissals */) {}
            });
        }
        , descartar(recursoIdx, recursoId)
            {
                Swal.fire({
                    title: `¿Esta seguro de descartar esta evidencia?`,
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Aceptar!',
                    cancelButtonText: 'Cancelar',
                    confirmButtonClass: 'btn btn-pill btn-success btn-lg',
                    cancelButtonClass: 'btn btn-pill btn-danger btn-lg',
                    buttonsStyling: false,
                    reverseButtons: true
                    }).then((result) => {
                    if (result.value) {
                        this.recursoIdx = recursoIdx;
                        let ruta = this.evidenciaRuta +'/descartar/';
                        this.peticionDelete(ruta, recursoId);
                    } else if (result.dismiss === Swal.DismissReason.cancel /* Read more about handling dismissals */) {}
                });
            }
        , cadConsObtener (objCons)
        {
            return Object.entries(objCons).map(filtro => `${filtro[0]}=${filtro[1]}`).join('&');            
        }

        // MÉTODOS PROPIOS DEL RECURSO PENDIENTE
        , pendienteCerrarMdl ()
        {
            this.pendienteTituloMdl = 'Nuevo';
            this.pendienteId = this.pendienteBody.fecha = this.pendienteBody.pendiente = this.pendienteBody.observacion = this.pendienteBody.fk_id_proyectos = '';
            this.pendienteOnVacios = false;
            this.tipoAccion = 'Guardar';
            $('#pendiente-mdl').modal('hide');
        }
        , pendienteValidar ()
        {
            return this.noHayFecha || this.noHayPendiente || this.noHayObservacion || this.noHayProyecto;
        }
        , pendienteListar ()
        {
            this.peticionGet(this.pendienteRuta, this.cadConsObtener(this.pendienteObjCons));
        }
        , pendienteRegistrar ()
        {
            this.pendienteOnVacios = true;
            let ruta = this.pendienteRuta +'/registrar';
            this.peticionPost(ruta, this.pendienteBody, 1);
        }
        , pendienteActualizar ()
        {
            this.pendienteOnVacios = true;
            let ruta = this.pendienteRuta +'/actualizar/';
            this.peticionPut(ruta, this.pendienteId, this.pendienteBody, 1);
        }
        , pendienteCierreValidar (recursoCondicion, finalTitle, recursoRuta, pendienteId)
        {
            this.pendienteParamsCierre = {recursoCondicion: recursoCondicion, finalTitle: finalTitle, recursoRuta: recursoRuta, pendienteId: pendienteId};
            let ruta = this.pendienteRuta +'/valida_cierre/';
            this.peticionGet(ruta, pendienteId);
        }

        // MÉTODOS PROPIOS DEL RECURSO ASIGNACIÓN
        , asignacionMdlCerrar ()
        {
            $('#asignacioncrud-mdl').modal('hide');
            this.pendienteTituloMdl = 'Registrar';
            this.pendienteCondicion = this.asignacionObjCons.fkIdPendientes = this.asignacionBody.fk_id_pendientes = this.pendienteUsuCrea = '';
            this.asignacionArr = [];
        }
        , asignacionMdlMdlCerrar ()
        {
            $('#asignacion-mdl').modal('hide');
            this.asignacionTituloMdl = 'Registrar';
            this.asignacionId = this.asignacionBody.fechaLimite = this.asignacionBody.fk_respons_users = this.asignacionBody.asignacion = this.asignacionBody.observacion = '';
            this.asignacionOnVacios = false;
            this.tipoAccion = 'Guardar';
            $('#asignacioncrud-mdl').modal('show');
        }
        , asignacionCondMdlCerrar ()
        {
            $('#asignacioncond-mdl').modal('hide');
            this.asignacionTituloMdl = 'Registrar';
            this.asignacionId = this.asignacionAl = this.asignacionCondicion = '';
            this.asignacionOnVacios = false;
            $('#asignacioncrud-mdl').modal('show');
        }
        , asignacionValidar ()
        {
            return this.noHayFechaLimite || this.noHayResponsable || this.noHayAsignacion || this.noHayObservacion2;
        }
        , asignacionListar ()
        {
            this.peticionGet(this.asignacionRuta, this.cadConsObtener(this.asignacionObjCons));
        }
        , asignacionRegistrar ()
        {
            this.asignacionOnVacios = true;
            let ruta = this.asignacionRuta +'/registrar';
            this.peticionPost(ruta, this.asignacionBody, 2);
        }
        , asignacionActualizar ()
        {
            this.asignacionOnVacios = true;
            let ruta = this.asignacionRuta +'/actualizar/';
            this.peticionPut(ruta, this.asignacionId, this.asignacionBody, 2);
        }
        , asignacionCondActualizar ()
        {
            this.asignacionOnVacios = true;
            if (this.noHayAl) return;
            this.asignacionOnVacios = false;
            let ruta = `${this.asignacionRuta}/${this.asignacionCondicion == 'Abierto'?'cerrar':'abrir'}/`, d = new Date(), body, me = this;
            body = {
                fecha: (d.getDate() < 10 ? '0' : '') + d.getDate()
                +'/'+ (d.getMonth() < 9 ? '0' : '') + (d.getMonth() + 1)
                +'/'+ d.getFullYear()
                , observacionAl: this.asignacionAl
            };
            // AL CERRAR LA ASIGNACIÓN SE TOMA EL NOMBRE DEL RESPONSABLE PARA EL ENCABEZADO DE LA BITACORA DE LA COLUMNA 'Al Cerrar'
            if (this.asignacionResp) body.responsable = this.asignacionResp;

            this.condPatch(ruta, this.asignacionId, this.asignacionCondicion, body);

            /*axios.patch(ruta, body).then((response) => {
                Swal.fire(me.swalTitulo, me.swalBody, 'success');
                me.asignacionListar();
            }).catch(error => console.log(error));*/
        }

        // MÉTODOS PROPIOS DEL RECURSO EVIDENCIA
        , evidenciaMdlCerrar ()
        {
            $('#evidenciacrud-mdl').modal('hide');
            this.asignacionTituloMdl = 'Registrar';
            this.asignacionId = this.asignacionRespId = this.asignacionResp = this.evidenciaBody.fk_id_asignaciones = this.asignacionAl = this.asignacionCondicion = this.evidenciaBody.evidencia = this.evidenciaBody.ruta = '';
            this.$refs.inputRuta.value = '';
            this.asignacionOnVacios = this.evidenciaOnVacios = false;
            this.evidenciaArr = [];
            $('#asignacioncrud-mdl').modal('show');
        }
        /*, evidenciaMdlMdlCerrar ()
        {
            $('#evidencia-mdl').modal('hide');
            this.evidenciaId = this.evidenciaBody.evidencia = this.evidenciaBody.ruta = '';
            this.evidenciaOnVacios = false;
            $('#asignacioncrud-mdl').modal('show');
        }*/
        , evidenciaValidar ()
        {
            return this.noHayEvidencia || this.noHayRuta;
        }
        , evidenciaListar ()
        {
            this.peticionGet(this.evidenciaRuta, '?fkIdAsignaciones='+ this.asignacionId);
        }
        , evidenciaRegistrar ()
        {
            this.evidenciaOnVacios = true;
            if (this.evidenciaValidar()) return;
            let ruta = this.evidenciaRuta +'/registrar', evidenciaBody = new FormData(), me = this;
            evidenciaBody.append('evidencia', this.evidenciaBody.evidencia);
            evidenciaBody.append('ruta', this.evidenciaBody.ruta);
            evidenciaBody.append('fk_id_asignaciones', this.evidenciaBody.fk_id_asignaciones);
            axios.post(ruta, evidenciaBody, {headers:{'Content-Type':'multipart/form-data'}})
            .then((response) => {
                me.evidenciaBody.evidencia = me.evidenciaBody.ruta = '';
                me.$refs.inputRuta.value = '';
                me.evidenciaOnVacios = false;
                me.evidenciaListar();
            }).catch(error => console.log(error));
        }
        , obtenerArchivo (event)
        {
            this.evidenciaBody.ruta = event.target.files[0];
        }

        , proyectoSelector ()
        {
            let ruta = this.pendienteRuta +'/proyectos';
            this.peticionGet(ruta);
        }
        , userSelector ()
        {
            let ruta = this.asignacionRuta +'/users';
            this.peticionGet(ruta);
        }
        , actorId ()
        {
            let ruta = this.userRuta +'/id';
            this.peticionGet(ruta);
        } 
    }
    , mounted ()
    {
        this.proyectoSelector();
        this.userSelector();
        this.pendienteListar();
        this.actorId();
    }
}
</script>