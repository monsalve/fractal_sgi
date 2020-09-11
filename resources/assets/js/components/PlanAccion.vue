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
                    <div class="row justify-content-between">
                        <div class="col-md-auto">
                            <i class="fa-lg" :class="vista?'icon-note':'fa fa-sitemap'" aria-hidden="true"></i>&nbsp;&nbsp;<span v-text="tituloVista"></span>&nbsp;&nbsp;
                            <button type="button" @click="abrirModal((vista? planAccionRuta:actividadRuta) +'Registrar')" class="btn btn-pill btn-primary btn-sm" v-if="permisosUser.crear">
                                <i class="icon-plus" aria-hidden="true"></i>&nbsp;Nuevo
                            </button>                        
                            <button type="button" class="btn btn-pill btn-secondary btn-sm" v-else>
                                <i class="icon-plus" aria-hidden="true"></i>&nbsp;Nuevo
                            </button>
                        </div>
                        <div class="col-md-auto" v-show="!vista">
                            <button type="button" @click="abrirModal(planAccionRuta)" class="btn btn-pill btn-primary btn-sm">
                                <i class="fa fa-reply" aria-hidden="true"></i>&nbsp;Atrás
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div v-show="vista">
                        <div class="form-group row justify-content-start">
                            <label class="col-md-auto my-1 pr-1">Segmento (obligatorio)</label>
                            <div class="col-md-3 pl-1">
                                <select class="form-control" :class="{'is-invalid' : noHayFiltroSegmento}" v-model="planAccionObjCons.segmentoId">
                                    <option value="">Seleccione</option>
                                    <option v-for="segmento in segmentoArr" :key="segmento.id" :value="segmento.id" v-text="segmento.segmento"></option>
                                </select>
                            </div>
                            <label class="col-md-auto my-1 pr-1">Proyecto</label>
                            <div class="col-md-3 pl-1">
                                <select id="criSelProyecto" class="form-control" v-model="planAccionObjCons.proyectoId">
                                    <option value="" selected>Todos</option>
                                    <option v-for="criArrProyecto in proyectoArr" :key="criArrProyecto.id" :value="criArrProyecto.id" v-text="criArrProyecto.proyecto"></option>
                                </select>
                            </div>
                            <label class="col-md-auto my-1 pr-1">Estado</label>
                            <div class="col-md-2 pl-1">
                                <select class="form-control" v-model="planAccionObjCons.estado">
                                    <option value="" selected>Todos</option>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row justify-content-start">
                            <label class="col-md-auto my-1 pr-1">Plan acción</label>
                            <div class="col-md-3 pl-1">
                                <input type="text" ref="planAccionOCPlanAccion" v-model="planAccionObjCons.planAccion" class="form-control" placeholder="Texto a buscar">
                            </div>
                            <label class="col-md-auto my-1 pr-1">Responsable</label>
                            <div class="col-md-3 pl-1">
                                <select class="form-control" v-model="planAccionObjCons.userRespons">
                                    <option value="" selected>Todos</option>
                                    <option v-for="criArrUsuario in userArr" :key="criArrUsuario.id" :value="criArrUsuario.id" v-text="criArrUsuario.nombre_completo"></option>
                                </select>
                            </div>
                            <label class="col-md-auto my-1 pr-1">Año</label>
                            <div class="col-md-1 pl-1">
                                <select class="form-control" v-model="planAccionObjCons.anio">
                                    <option v-for="anioSelector in aniosSelector" :key="anioSelector" :value="anioSelector" v-text="anioSelector"></option>
                                </select>
                            </div>
                            <label class="col-md-auto my-1 pr-1">Ver</label>
                            <div class="col-md-auto pl-1">
                                <select class="form-control" @change="planAccionListar()" v-model="planAccionObjCons.numRegs">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <button type="button" @click="cambiarPagina(1, planAccionObjCons, planAccionPagin, 1)" class="btn btn-pill btn-primary btn-sm ml-2"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;Buscar</button>
                        </div>
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Plan acción</th>
                                    <th class="text-center">Año</th>
                                    <th class="text-center">Puntaje</th>
                                    <th class="text-center">Responsable</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-if="planAccionArr.length == 0">
                                    <tr>
                                        <td class="text-center" colspan="7" v-text="(planAccionEstaListado? 'No hay': 'Busca para listar') +' registros'"></td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr v-for="(planAccion, index) in planAccionArr" :key="planAccion.id">
                                        <td class="text-center" v-text="index + 1"></td>
                                        <td v-text="planAccion.planAccion"></td>
                                        <td v-text="planAccion.anio"></td>
                                        <td v-text="planAccion.puntaje"></td>
                                        <td v-text="planAccion.nombre_completo"></td>
                                        <td class="text-center">
                                            <span class="badge" :class="'badge-' + (planAccion.estado? 'success':'danger')" v-text="planAccion.estado? 'Activo':'Inactivo'"></span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" @click="abrirModal(planAccionRuta +'Proyectos', {'planAccionId': planAccion.id, 'planAccionPlanAccion': planAccion.planAccion, 'segmentoId': planAccion.fk_id_segmentos, 'segmentoSegmento': planAccion.segmento})" class="btn rounded btn-info btn-sm" title="Proyectos">
                                                <i class="icon-book-open" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" @click="abrirModal(planAccionRuta +'Actualizar', planAccion)" class="btn rounded btn-warning btn-sm" title="Editar" v-if="planAccion.estado && permisosUser.actualizar">
                                                <i class="icon-pencil" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                <i class="icon-pencil" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" @click="actualizarEstado(planAccion.estado, 'este plan de accion', planAccionRuta, planAccion.id)" class="btn rounded btn-sm" :class="'btn-'+ (planAccion.estado ? 'danger':'success')" :title="planAccion.estado ? 'Anular':'Activar'" v-if="permisosUser.anular">
                                                <i :class="'icon-'+ (planAccion.estado ? 'trash':'check')" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                <i :class="'icon-'+ (planAccion.estado ? 'trash':'check')" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                    </tr>                                
                                </template>
                            </tbody>
                        </table>
                        <nav>
                            <ul class="pagination">
                                <li class="page-item" v-if="planAccionPagin.current_page > 1">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(planAccionPagin.current_page - 1, planAccionObjCons, planAccionPagin, 1)">Ant</a>
                                </li>
                                <li class="page-item" v-for="page in planAccionPagesNumber" :key="page" :class="[page == planAccionPagin.current_page ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page, planAccionObjCons, planAccionPagin, 1)" v-text="page"></a>
                                </li>
                                <li class="page-item" v-if="planAccionPagin.current_page < planAccionPagin.last_page">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(planAccionPagin.current_page + 1, planAccionObjCons, planAccionPagin, 1)">Sig</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <div v-show="!vista">
                        <div class="row justify-content-between">
                            <div class="form-group col-md-4">
                                <h6 class="font-italic"><strong>Plan acción</strong></h6>
                                <label class="text-sm-left font-italic" v-text="planAccionPlanAccion"></label>
                            </div>
                            <div class="form-group col-md-4">
                                <h6 class="font-italic"><strong>Segmento</strong></h6>
                                <label class="text-sm-left font-italic" v-text="segmentoSegmento"></label>
                            </div>
                            <div class="form-group col-md-4">
                                <h6 class="font-italic"><strong>Proyecto</strong></h6>
                                <label class="text-sm-left font-italic" v-text="proyectoProyecto"></label>
                            </div>
                        </div>
                        <div class="form-group row justify-content-start">
                            <label class="col-md-auto my-1 pr-1">Ver</label>
                            <div class="col-md-auto pl-1">
                                <select class="form-control" @change="actividadListar()" v-model="actividadObjCons.numRegs">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Actividad</th>
                                    <th class="text-center">Acción</th>
                                    <th class="text-center">Responsables</th>
                                    <th class="text-center">Ponderación</th>
                                    <th class="text-center">Indicador asociado</th>
                                    <th class="text-center">Día reporte</th>
                                    <th class="text-center">Plan acción asociado</th>
                                    <th class="text-center">Ene</th><th class="text-center">Feb</th><th class="text-center">Mar</th><th class="text-center">Abr</th>
                                    <th class="text-center">May</th><th class="text-center">Jun</th><th class="text-center">Jul</th><th class="text-center">Ago</th>
                                    <th class="text-center">Sep</th><th class="text-center">Oct</th><th class="text-center">Nov</th><th class="text-center">Dic</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-if="actividadArr.length == 0">
                                    <tr>
                                        <td class="text-center" colspan="22">No hay registros</td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr v-for="(actividad, index) in actividadArr" :key="actividad.id">
                                        <td class="text-center" v-text="index + 1"></td>
                                        <td v-text="actividad.actividad"></td>
                                        <td v-text="actividad.tipoActividad"></td>
                                        <td>
                                            <ul class="list-group">
                                                <li class="list-group-item py-0" v-for="responsable in actividad.responsables" :key="responsable.id" v-text="responsable.nombre_completo"></li>
                                            </ul>
                                        </td>
                                        <td v-text="actividad.ponderacion"></td>
                                        <td v-text="actividad.fk_id_indicadores"></td>
                                        <td v-text="actividad.diaMesLimite"></td>
                                        <td v-text="actividad.planAccionAsociado"></td>
                                        <td class="text-center" v-for="mes in arrMeses[0].meses">
                                            <button type="button" v-if="actividad[mes.id] != 0" class="circulo border border-dark rounded-circle" :class="arrMesEstado[actividad[mes.id]]"></button>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge" :class="'badge-' + (actividad.estado? 'success':'danger')" v-text="actividad.estado? 'Activo':'Inactivo'"></span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" @click="abrirModal(actividadRuta +'Actualizar', actividad)" class="btn rounded btn-warning btn-sm" title="Actualizar" v-if="actividad.estado && permisosUser.actualizar">
                                                <i class="icon-pencil" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                <i class="icon-pencil" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" @click="actualizarEstado(actividad.estado, 'esta actividad', actividadRuta, actividad.id)" class="btn rounded btn-sm" :class="'btn-'+ (actividad.estado ? 'danger':'success')" :title="actividad.estado ? 'Anular':'Activar'" v-if="permisosUser.anular">
                                                <i :class="'icon-'+ (actividad.estado ? 'trash':'check')" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                <i :class="'icon-'+ (actividad.estado ? 'trash':'check')" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                    </tr>                                
                                </template>
                            </tbody>
                        </table>
                        <nav>
                            <ul class="pagination">
                                <li class="page-item" v-if="actividadPagin.current_page > 1">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(actividadPagin.current_page - 1, actividadObjCons, actividadPagin, 2)">Ant</a>
                                </li>
                                <li class="page-item" v-for="page in actividadPagesNumber" :key="page" :class="[page == actividadPagin.current_page ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page, actividadObjCons, actividadPagin, 2)" v-text="page"></a>
                                </li>
                                <li class="page-item" v-if="actividadPagin.current_page < actividadPagin.last_page">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(actividadPagin.current_page + 1, actividadObjCons, actividadPagin, 2)">Sig</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <!-- Fin ejemplo de tabla Listado -->
        </div>
        <!--Inicio del modal agregar/actualizar-->
        <div id="planaccion-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="planAcciontituloMdl +' plan de acción'"></h4>
                        <button type="button" class="close" @click="planAccionCerrarMdl()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-group row justify-content-center">
                                <label class="col-sm-2 col-form-label">Segmento</label>
                                <div class="col-md-8">
                                    <select class="form-control" :class="{'is-invalid' : noHaySegmento}" v-model="planAccionBody.fk_id_segmentos">
                                        <option value="">Seleccione</option>
                                        <option v-for="segmento in segmentoArr" :key="segmento.id" :value="segmento.id" v-text="segmento.segmento"></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row justify-content-center">
                                <label class="col-sm-2 col-form-label">Proyecto (opcional)</label>
                                <div class="col-md-8">
                                    <multiselect v-model="planAccionBody.proyectoArr" :options="proyectoArr" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Buscar y seleccionar" label="proyecto" track-by="proyecto" :preselect-first="true" selectLabel="Presiona enter para marcar" selectedLabel="Marcado" deselectLabel="Presiona enter para desmarcar">
                                        <span slot="noResult">Sin resultados</span>
                                        <template slot="selection" slot-scope="{ values, search, isOpen }"><span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ values.length }} Seleccionados</span></template>
                                    </multiselect>
                                </div>
                            </div>
                            <div class="form-group row justify-content-center">
                                <label class="col-sm-2 col-form-label">Plan de acción</label>
                                <div class="col-md-8">
                                    <input type="text" ref="planAccionBPlanAccion" v-model="planAccionBody.planAccion" class="form-control" :class="{'is-invalid' : noHayPlanAccion}" placeholder="">
                                </div>
                            </div>
                            <div class="form-group row justify-content-center">
                                <label class="col-sm-2 col-form-label">Responsable</label>
                                <div class="col-md-8">
                                    <select class="form-control" :class="{'is-invalid' : noHayResponsable}" v-model="planAccionBody.fk_respons_users">
                                        <option value="">Seleccione</option>
                                        <option v-for="user in userArr" :key="user.id" :value="user.id" v-text="user.nombre_completo"></option>
                                    </select>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="form-group col-md-2">
                                    <label>Año</label>
                                    <select class="form-control" v-model="planAccionBody.anio">
                                        <option v-for="anioSelector in aniosSelector" :key="anioSelector" :value="anioSelector" v-text="anioSelector"></option>
                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Puntaje</label>
                                    <input type="number" min="0" v-model="planAccionBody.puntaje" class="form-control" :class="{'is-invalid' : noHayPuntaje}">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-pill btn-danger btn-sm" @click="planAccionCerrarMdl()">Cerrar</button>
                        <button type="button" class="btn btn-pill btn-success btn-sm" @click="tipoAccion == 'Guardar' ? planAccionRegistrar() : planAccionActualizar()" v-text="tipoAccion"></button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!--Inicio modal lista proyectos-->
        <div v-show="vista">
            <div id="proyecto-mdl" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div id="modalDialogProyectos" class="modal-dialog modal-primary modal-lg modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="tituloModalProyectos"></h4>
                            <button type="button" class="close" @click="planAccionProyectosCerrarMdl()" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div id="modalBodyProyectos" class="modal-body">
                            <ul class="list-group list-group-flush">
                                <button type="button" @click="abrirModal(actividadRuta +'Listar', proyectoElegido)" class="list-group-item list-group-item-action list-group-item-info text-center rounded" v-for="proyectoElegido in planAccionBody.proyectoArr" :key="proyectoElegido.id" v-text="proyectoElegido.proyecto"></button>
                            </ul>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-pill btn-primary btn-sm" @click="planAccionProyectosCerrarMdl()">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-show="!vista">
            <div id="actividad-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-primary modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="actividadTituloMdl +' actividad'"></h4>
                            <button type="button" class="close" @click="actividadCerrarMdl()" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                                <div class="row justify-content-center">
                                    <div class="form-group col-md-2">
                                        <label>Tipo actividad</label>
                                        <select class="form-control" :class="{'is-invalid' : noHayTipoActividad}" v-model="actividadBody.tipoActividad">
                                            <option value="">Seleccione</option>
                                            <option value="1">Planear</option>
                                            <option value="2">Hacer</option>
                                            <option value="3">Verificar</option>
                                            <option value="4">Actuar</option>
                                            <option value="5">Corregir</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Dia de reporte</label>
                                        <select class="form-control" :class="{'is-invalid' : noHayDiaMesLimite}" v-model="actividadBody.diaMesLimite">
                                            <option value="">Seleccione</option>
                                            <option v-for="dia in 30" :value="dia" v-text="dia"></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="form-group col-md-8">
                                        <label>Responsables</label>
                                        <multiselect :class="{'is-danger' : noHayResponsables}" v-model="actividadBody.responsArr" :options="userArr" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Buscar y seleccionar" label="nombre_completo" track-by="id" selectLabel="Presiona enter para marcar" selectedLabel="Marcado" deselectLabel="Presiona enter para desmarcar">
                                            <span slot="noResult">Sin resultados</span>
                                            <template slot="selection" slot-scope="{ values, search, isOpen }"><span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ values.length }} Seleccionados</span></template>
                                        </multiselect>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="form-group col-md-4">
                                        <label>Meses de ejecución</label>
                                        <multiselect :class="{'is-danger' : noHayMesesRegistrados}" v-model="actividadBody.mesesArr" :options="arrMeses" :multiple="true" group-values="meses" group-label="todos" :group-select="true" :close-on-select="false" :clear-on-select="false" :searchable="false" placeholder="Seleccionar" label="mes" track-by="id" selectLabel="Presiona enter para marcar" selectGroupLabel="Presiona enter para marcar todo" selectedLabel="Marcado" deselectLabel="Presiona enter para desmarcar" deselectGroupLabel="Presiona enter para desmarcar todo">
                                            <template slot="selection" slot-scope="{ values, search, isOpen }"><span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ values.length }} Seleccionados</span></template>
                                        </multiselect>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="form-group col-md-8">
                                        <label>Plan acción asociado (opcional)</label>
                                        <select class="form-control" v-model="actividadBody.fk_planAsoc_planes_accion">
                                            <option value="">Seleccione</option>
                                            <option v-for="planAccionAsoc in planAccionAsocArr" :key="planAccionAsoc.id" :value="planAccionAsoc.id" v-text="planAccionAsoc.planAccion"></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="form-group col-md-8">
                                        <label>Actividad</label>
                                        <textarea ref="actividadBActividad" v-model="actividadBody.actividad" class="form-control" :class="{'is-invalid' : noHayActividad}" rows="2"></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-pill btn-danger btn-sm" @click="actividadCerrarMdl()">Cerrar</button>
                            <button type="button" class="btn btn-pill btn-success btn-sm" @click="tipoAccion == 'Guardar' ? actividadRegistrar() : actividadActualizar()" v-text="tipoAccion"></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>
<script>
    import Multiselect from 'vue-multiselect'
    export default {
        components : {
            Multiselect
        }
        , props : ['permisosUser' ,'ruta', 'axiosApp']
        , data ()
        {
            return {
                planAccionRuta : 'planes_accion'
                , actividadRuta : 'actividades'
                , segmentoRuta : 'segmentos'
                , proyectoRuta : 'conf_proyectos'
                , userRuta : 'users'

                , vista : 1
                , tituloVista : 'Planes de Acción'

                , planAccionObjCons : {
                    '?page' : 1
                    , anio : 0
                    , estado : ''
                    , leer : this.permisosUser.leer
                    , planAccion : ''
                    , segmentoId : ''
                    , proyectoId : ''
                    , userRespons : ''
                    , numRegs : 10
                }
                , segmentoArr : []
                , segmentoId : ''
                , segmentoSegmento : ''
                , proyectoArr : []
                , proyectoId : ''
                , proyectoProyecto : ''
                , userArr : []
                
                , planAccionEstaListado : false
                , planAccionArr : []
                , planAccionId : 0
                , planAccionPlanAccion : ''
                , planAccionBody : {
                    anio : ''
                    , fk_id_segmentos : ''
                    , fk_respons_users : ''
                    , planAccion : ''
                    , proyectoArr : []
                    , puntaje : '0'
                }
                , tempFkIdResponsUsers : 0
                , planAccionPagin : {
                    total : 0
                    , current_page : 0
                    , per_page : 0
                    , last_page : 0
                    , from : 0
                    , to : 0
                }
                , offset : 3
                , planAccionOnVacios : false

                , planAccionFiltroVacio : false
                , actividadOnVacios : false
                , planAcciontituloMdl : 'Registrar'
                , tituloModalProyectos : ''
                , tipoAccion : 'Guardar'

                , actividadObjCons : {
                    '?page' : 1
                    , leer : this.permisosUser.leer
                    , planAccionId : 0
                    , segmentoId : 0
                    , proyectoId : 0
                    , numRegs : 10
                }
                , actividadArr : []
                
                , actividadId : 0
                , actividadBody : {
                    actividad : ''
                    , diaMesLimite : ''
                    , fk_id_planes_accion : ''
                    , fk_id_segmentos : ''
                    , fk_id_proyectos : ''
                    , fk_planAsoc_planes_accion : ''
                    , mesesArr : []
                    , ponderacion : 0
                    , responsArr : []
                    , tipoActividad : ''
                }
                , tempResponsArr : []
                , actividadPagin : {
                    total : 0
                    , current_page : 0
                    , per_page : 0
                    , last_page : 0
                    , from : 0
                    , to : 0
                }

                , planAccionAsocArr : []
                //, fkIdIndicadores : 0 //DESPUES SE CAMBIA A COMILLAS SIMPLES, CUANDO LOS DATOS PARA EL SELECTOR ESTEN DISPONIBLES

                , fkPlanAsocPlanesAccion : '' // PARA UTILIZAR EN EL FORMULARO DE REGISTRO

                , arrMeses : [{todos: 'Todos', meses: [{id : 'ene', mes : 'Enero'}, {id : 'feb', mes : 'Febrero'}, {id : 'mar', mes : 'Marzo'}, {id : 'abr', mes : 'Abril'}, {id : 'may', mes : 'Mayo'}, {id : 'jun', mes : 'Junio'}, {id : 'jul', mes : 'Julio'}, {id : 'ago', mes : 'Agosto'}, {id : 'sep', mes : 'Septiembre'}, {id : 'oct', mes : 'Octubre'}, {id : 'nov', mes : 'Noviembre'}, {id : 'dic', mes : 'Diciembre'}]}]
                , arrMesEstado : []

                , actividadTituloMdl : 'Registrar'
            }
        }
        , computed :
        {
            // VALIDACIÓN FILTRO
            noHayFiltroSegmento : function ()
            {
                return this.planAccionFiltroVacio && !this.planAccionObjCons.segmentoId;
            }
            // VALIDACIÓN DE CAMPOS VACÍOS EN FORMULARIOS
            //Campos plan accion
            , noHayPlanAccion : function ()
            {
                return this.planAccionOnVacios && !this.planAccionBody.planAccion.trim();
            }
            , noHayPuntaje : function ()
            {
                return this.planAccionOnVacios && !this.planAccionBody.puntaje.trim();
            }
            , noHaySegmento : function ()
            {
                return this.planAccionOnVacios && !this.planAccionBody.fk_id_segmentos;
            }
            , noHayResponsable : function ()
            {
                return this.planAccionOnVacios && !this.planAccionBody.fk_respons_users;
            }
            // Campos actividad
            , noHayActividad : function ()
            {
                return this.actividadOnVacios && !this.actividadBody.actividad.trim();
            }
            , noHayTipoActividad : function ()
            {
                return this.actividadOnVacios && !this.actividadBody.tipoActividad;
            }
            , noHayMesesRegistrados : function ()
            {
                return this.actividadOnVacios && !this.actividadBody.mesesArr.length;
            }
            , noHayDiaMesLimite : function ()
            {
                return this.actividadOnVacios && !this.actividadBody.diaMesLimite;
            }
            /*, noHayPonderacion : function ()
            {
                return this.actividadOnVacios && !this.actividadBody.ponderacion.trim();
            }*/
            , noHayResponsables : function ()
            {
                return this.actividadOnVacios && !this.actividadBody.responsArr.length;
            }
            , aniosSelector : function () {
                let fechaActual = new Date(), arrAnios = [];
                let anioIni = this.planAccionObjCons.anio = fechaActual.getFullYear();
                for (let i = -4; i < 5; i++) {
                    arrAnios.push(anioIni + i);
                }
                return arrAnios;
            }
            //Calcula los elementos de la paginación
            , planAccionPagesNumber: function () {
                if(!this.planAccionPagin.to) {
                    return [];
                }
                let from = this.planAccionPagin.current_page - this.offset; 
                if(from < 1) {
                    from = 1;
                }
                let to = from + (this.offset * 2); 
                if(to >= this.planAccionPagin.last_page){
                    to = this.planAccionPagin.last_page;
                }
                let pagesArray = [];
                while(from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;
            }
            , actividadPagesNumber: function () {
                if(!this.actividadPagin.to) {
                    return [];
                }
                let from = this.actividadPagin.current_page - this.offset; 
                if(from < 1) {
                    from = 1;
                }
                let to = from + (this.offset * 2); 
                if(to >= this.actividadPagin.last_page){
                    to = this.actividadPagin.last_page;
                }
                let pagesArray = [];
                while(from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;
            }
        }
        , methods :
        {
            abrirModal(accion, data = {})
            {
                switch (accion) {
                    case this.planAccionRuta +'Registrar':
                        this.planAccionBody.fk_id_segmentos = this.planAccionObjCons.segmentoId;
                        this.planAccionBody.proyectoArr = this.planAccionObjCons.proyectoId ? [{ id : this.planAccionObjCons.proyectoId , proyecto : $('#criSelProyecto option:selected').text()}]:[];
                        this.planAccionBody.planAccion = this.planAccionObjCons.planAccion;
                        this.planAccionBody.fk_respons_users = this.planAccionObjCons.userRespons;
                        this.planAccionBody.anio = this.planAccionObjCons.anio;
                        $('#planaccion-mdl').modal('show');
                        break;
                    case this.planAccionRuta +'Actualizar':
                        this.planAcciontituloMdl = 'Editar';
                        this.planAccionId = data.id;
                        this.planAccionBody.planAccion = data.planAccion;
                        this.planAccionBody.anio = data.anio;
                        this.planAccionBody.puntaje = ""+ data.puntaje;
                        this.planAccionBody.fk_id_segmentos = data.fk_id_segmentos;
                        this.planAccionBody.fk_respons_users = data.fk_respons_users;
                        this.tempFkIdResponsUsers = data.fk_respons_users;
                        this.proyectosRegistrados(data.id);
                        this.tipoAccion = 'Actualizar';
                        $('#planaccion-mdl').modal('show');
                        break;
                    case this.planAccionRuta +'Proyectos':
                        this.tituloModalProyectos = `Proyectos de ${data.planAccionPlanAccion}`;
                        this.actividadObjCons.planAccionId = this.actividadBody.fk_id_planes_accion = data.planAccionId;
                        this.actividadObjCons.segmentoId = this.actividadBody.fk_id_segmentos = data.segmentoId;
                        this.planAccionPlanAccion = data.planAccionPlanAccion;
                        this.segmentoSegmento = data.segmentoSegmento;
                        this.proyectosRegistrados(data.planAccionId);
                        $('#proyecto-mdl').modal('show');
                        break;
                    case this.actividadRuta +'Listar':
                        this.tituloVista = 'Actividades';
                        this.actividadObjCons.proyectoId = this.actividadBody.fk_id_proyectos = data.id;
                        this.proyectoProyecto = data.proyecto;
                        this.actividadListar();
                        this.basicoPlanesAccionAsociables();
                        this.vista = 0;
                        $('#proyecto-mdl').modal('hide');
                        break;
                    case this.planAccionRuta:
                        this.tituloVista = 'Planes de acción';
                        this.proyectoProyecto = '';
                        this.actividadObjCons.proyectoId = this.actividadBody.fk_id_proyectos = 0;
                        this.actividadArr = [];
                        this.planAccionAsocArr = [];
                        this.vista = 1;
                        $('#proyecto-mdl').modal('show');
                        break;
                    case this.actividadRuta +'Registrar':
                        $('#actividad-mdl').modal('show');
                        break;
                    case this.actividadRuta +'Actualizar':
                        this.actividadTituloMdl = 'Editar';
                        this.actividadId = data.id;
                        this.actividadBody.actividad = data.actividad;
                        this.actividadBody.tipoActividad = data.tipoActividad == 'Planear'?1 :data.tipoActividad == 'Hacer'?2 :data.tipoActividad == 'Verificar'?3 :data.tipoActividad == 'Actuar'?4 :5;
                        this.actividadBody.responsArr = data.responsables;
                        this.tempResponsArr = JSON.parse(JSON.stringify(data.responsables));
                        this.actividadBody.ponderacion = data.ponderacion;
                        //this.fkIdIndicadores = data.fkIdIndicadores; // CUANDO SE PUEDAN VER LOS INDICADORES POR ACTIVIDAD, DESCOMENTA ESTA LÍNEA
                        this.actividadBody.diaMesLimite = data.diaMesLimite;
                        this.actividadBody.fk_planAsoc_planes_accion = data.id_plan_asoc;
                        this.arrMeses[0].meses.forEach((mes) => {
                            if (data[mes.id] != 0) this.actividadBody.mesesArr.push(mes);
                        }, this);
                        this.tipoAccion = 'Actualizar';
                        $('#actividad-mdl').modal('show');
                        break;
                }
            }
            , actividadActualizar ()
            {
                this.actividadOnVacios = true;
                let ruta = this.actividadRuta +'/actualizar/';
                this.peticionPut(ruta, this.actividadId, this.actividadBody, 2);
            }
            , actividadCerrarMdl ()
            {
                $('#actividad-mdl').modal('hide');
                this.actividadTituloMdl = 'Registrar';
                this.actividadId = 0;
                this.actividadBody.actividad = this.actividadBody.diaMesLimite = this.actividadBody.fk_planAsoc_planes_accion = this.actividadBody.tipoActividad = '';
                this.actividadBody.mesesArr = [];
                this.actividadBody.responsArr = [];
                this.tempResponsArr = [];
                this.tipoAccion = 'Guardar';
                this.actividadOnVacios = false;
            }
            , actividadListar ()
            {
                this.peticionGet(this.actividadRuta, this.obtenerCadCons(this.actividadObjCons));
            }
            , actividadRegistrar ()
            {
                this.actividadOnVacios = true;
                let ruta = this.actividadRuta +'/registrar';
                this.peticionPost(ruta, this.actividadBody, 2);
            }
            , actividadValidar ()
            {
                return this.noHayActividad || this.noHayTipoActividad || this.noHayMesesRegistrados || this.noHayDiaMesLimite /*|| this.noHayPonderacion*/ || this.noHayResponsables;
            }
            , actualizarEstado (recursoEstado, finalTitle, recursoRuta, recursoId)
            {
                let prefijo = recursoEstado ? 'des':'';
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
                    } else if (
                        // Read more about handling dismissals
                        result.dismiss === Swal.DismissReason.cancel
                    ) {}
                });
            }
            , basicoPlanesAccionAsociables ()
            {
                let ruta = this.actividadRuta +'/planes_accion_asociables', objCons = {'?planAccionId' : this.planAccionId};
                this.peticionGet(ruta, this.obtenerCadCons(objCons));
            }
            , segmentoSimple ()
            {
                let ruta = this.segmentoRuta +'/simple', cadCons = '?leer='+ this.permisosUser.leer;
                this.peticionGet(ruta, cadCons);
            }
            , proyectoSimple ()
            {
                let ruta = this.proyectoRuta +'/simple', cadCons = '?leer='+ this.permisosUser.leer;
                this.peticionGet(ruta, cadCons);
            }
            , userSimple ()
            {
                let ruta = this.userRuta +'/simple';
                this.peticionGet(ruta);
            }
            , cambiarPagina (page, refObjCons, refPagin, listar)
            {
                refObjCons['?page'] = refPagin.current_page = page;
                switch (listar) {
                    case 1: this.planAccionListar();break;
                    case 2: this.actividadListar();break;
                }
            }
            , esRecursoInvalido (validar)
            {
                switch (validar) {
                    case 1: if (this.planAccionValidar())return true;
                        break;
                    case 2: if (this.actividadValidar())return true;
                        break;
                }
                return false;
            }
            , obtenerCadCons(objCons)
            {
                return Object.entries(objCons).map(filtro => `${filtro[0]}=${filtro[1]}`).join('&');
            }
            , obtenerRespuesta(ruta, peticion)
            {
                let me = this;
                peticion.then(function (response) {
                    let respuesta = response.data;
                    if (ruta.endsWith('desactivar/') || ruta.endsWith('activar/'))Swal.fire(me.tituloSwal, me.descripcionSwal, 'success');
                    switch (ruta) {
                        case me.segmentoRuta +'/simple':
                            me.segmentoArr = respuesta.segmentos;
                            break;
                        case me.proyectoRuta +'/simple':
                            me.proyectoArr = respuesta.proyectos;
                            break;
                        case me.userRuta +'/simple':
                            me.userArr = respuesta.usuarios;
                            break;
                        case me.planAccionRuta +'/proyectos_registrados':
                            me.planAccionBody.proyectoArr = respuesta.proyectosRegistrados;
                            break;
                        case me.actividadRuta +'/planes_accion_asociables':
                            me.planAccionAsocArr = respuesta.planesAccionAsoc;
                            break;
                        case me.planAccionRuta:
                            me.planAccionArr = respuesta.planesAccion.data;
                            me.planAccionPagin = respuesta.pagination;
                            break;
                        case me.planAccionRuta +'/registrar':
                            me.axiosApp.post(me.planAccionRuta +'/notificar', {planAccionId: respuesta.planAccionId, destinatarioId: me.planAccionBody.fk_respons_users}).catch(error => console.log(error));
                            me.planAccionCerrarMdl();
                            me.planAccionListar();
                            break;
                        case me.planAccionRuta +'/actualizar/':
                            if (me.planAccionBody.fk_respons_users != me.tempFkIdResponsUsers) me.axiosApp.post(me.planAccionRuta +'/notificar', {planAccionId: me.planAccionId, destinatarioId: me.planAccionBody.fk_respons_users}).catch(error => console.log(error));
                            me.planAccionCerrarMdl();
                            me.planAccionListar();
                            break;
                        case me.planAccionRuta +'/desactivar/':
                        case me.planAccionRuta +'/activar/':
                            me.planAccionListar();
                            break;
                        case me.actividadRuta:
                            me.actividadArr = respuesta.actividades.data;
                            me.actividadPagin = respuesta.pagination;
                            break;
                        case me.actividadRuta +'/registrar':
                            me.axiosApp.post(me.actividadRuta +'/notificar', {actividadId: respuesta.actividadId, responsArr: me.actividadBody.responsArr}).catch(error => console.log(error));
                            $('#actividad-mdl').modal('hide');
                            me.actividadCerrarMdl();
                            me.actividadListar();
                            break;
                        case me.actividadRuta +'/actualizar/':
                            // DETERMINA SI HUBO CAMBIOS EN LOS RESPONSABLES ASIGNADOS
                            let destinatarios = [], notificar;
                            me.actividadBody.responsArr.forEach((respons) => {
                                notificar = true;
                                me.tempResponsArr.forEach((tempRespons) => {
                                    if (respons.id == tempRespons.id) notificar = false;
                                });
                                if (notificar) destinatarios.push(respons);
                            });
                            // DE HABER RESPONSABLES NUEVOS SE LES ENVIA LA NOTIFICACION
                            if (destinatarios.length) me.axiosApp.post(me.actividadRuta +'/notificar', {actividadId: me.actividadId, responsArr: me.actividadBody.responsArr}).catch(error => console.log(error));
                            $('#actividad-mdl').modal('hide');
                            me.actividadCerrarMdl();
                            me.actividadListar();
                            break;
                        case me.actividadRuta +'/desactivar/':
                        case me.actividadRuta +'/activar/':
                            me.actividadListar();
                            break;
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
            }
            , peticionGet (ruta, cadCons = '')
            {
                let peticion = this.axiosApp.get(ruta + cadCons);
                this.obtenerRespuesta(ruta, peticion);
            }
            , estadoPatch (ruta, recursoId ,estado)
            {
                this.tituloSwal = 'Activado';
                this.descripcionSwal = 'Registro activado con éxito.';
                if (estado) {
                    this.tituloSwal = 'Desactivado';
                    this.descripcionSwal = 'Registro desactivado con éxito.';
                }
                let peticion = this.axiosApp.patch(ruta + recursoId);
                this.obtenerRespuesta(ruta, peticion);
            }
            , peticionPost (ruta, cuerpo, validar = 0)
            {
                if (this.esRecursoInvalido(validar))return;
                let peticion = this.axiosApp.post(ruta, cuerpo);
                this.obtenerRespuesta(ruta, peticion);
            }
            , peticionPut (ruta, recursoId, cuerpo, validar)
            {
                if (this.esRecursoInvalido(validar))return;
                let peticion = this.axiosApp.put(ruta + recursoId, cuerpo);
                this.obtenerRespuesta(ruta, peticion);
            }
            , planAccionActualizar ()
            {
                this.planAccionOnVacios = true;
                let ruta = this.planAccionRuta +'/actualizar/';
                this.peticionPut(ruta, this.planAccionId, this.planAccionBody, 1);
            }
            , planAccionCerrarMdl ()
            {
                this.planAcciontituloMdl = 'Registrar';
                this.planAccionId = this.tempFkIdResponsUsers = 0;
                this.planAccionBody.anio = this.planAccionBody.fk_id_segmentos = this.planAccionBody.fk_respons_users = this.planAccionBody.planAccion = '';
                this.planAccionBody.puntaje = '0';
                this.planAccionBody.proyectoArr = [];
                this.planAccionOnVacios = false;
                this.tipoAccion = 'Guardar';
                $('#planaccion-mdl').modal('hide');
            }
            , planAccionProyectosCerrarMdl ()
            {
                this.tituloModalProyectos = '';
                this.actividadObjCons.planAccionId = this.actividadObjCons.segmentoId = this.actividadBody.fk_id_planes_accion = this.actividadBody.fk_id_segmentos = 0;
                this.planAccionPlanAccion = '';
                this.segmentoSegmento = '';
                this.planAccionBody.proyectoArr = [];
                $('#proyecto-mdl').modal('hide');
            }
            , planAccionListar ()
            {
                // Ejecuta la validacion del filtro
                this.planAccionFiltroVacio = true;
                if (this.noHayFiltroSegmento) return;
                this.planAccionFiltroVacio = false;
                this.planAccionEstaListado = true;
                this.peticionGet(this.planAccionRuta, this.obtenerCadCons(this.planAccionObjCons));
            }
            , planAccionRegistrar ()
            {
                this.planAccionOnVacios = true;
                let ruta = this.planAccionRuta +'/registrar';
                this.peticionPost(ruta, this.planAccionBody, 1);
            }
            , planAccionValidar ()
            {
                return this.noHayPlanAccion || this.noHayPuntaje || this.noHaySegmento || this.noHayResponsable;;
            }
            , proyectosRegistrados (planAccionId)
            {
                let ruta = this.planAccionRuta +'/proyectos_registrados', cadCons = `?leer=${this.permisosUser.leer}&planAccionId=${planAccionId}`;
                this.peticionGet(ruta, cadCons);
            }
        }
        , mounted ()
        {
            this.segmentoSimple();
            this.proyectoSimple();
            this.userSimple();
            this.arrMesEstado[1] = 'btn btn-warning btn-sm';
            this.arrMesEstado[2] = 'btn btn-success btn-sm';
            this.arrMesEstado[3] = 'btn btn-danger btn-sm';

            $('#planaccion-mdl').on('shown.bs.modal', () => this.$refs.planAccionBPlanAccion.focus());
            $('#planaccion-mdl, #proyecto-mdl').on('hidden.bs.modal', () => this.$refs.planAccionOCPlanAccion.focus());
            $('#actividad-mdl').on('shown.bs.modal', () => this.$refs.actividadBActividad.focus());
        }
    }
</script>
<style>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    #modalDialogProyectos{
    overflow-y: initial !important
    }
    #modalBodyProyectos{
    height: 400px;
    overflow-y: auto;
    }
    .circulo{
        width: 1.5em;
        height: 1.5em;
    }
    .is-danger .multiselect__tags {
        border-color: #f86c6b;
    }
</style>