<template>
    <main class="main">
        <!-- Breadcrumb -->
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Escritorio</a></li>
        </ol>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row justify-content-between">
                        <div class="col-md-auto">
                            <i class="fa fa-lg" :class="'fa-'+ (vista == 0? 'dot-circle-o' : vista == 1? 'line-chart': 'align-justify')" aria-hidden="true"></i>&nbsp;&nbsp;<span v-text="tituloVista"></span>&nbsp;&nbsp;
                            <button id="registrar" type="button" @click="abrirModal((vista == 0? objetivoRuta:indicadorRuta) +'Registrar')" class="btn btn-pill btn-primary btn-sm" v-if="permisosUser.crear" v-show="vista != 2">
                                <i class="icon-plus" aria-hidden="true"></i>&nbsp;Nuevo
                            </button>
                            <button type="button" class="btn btn-pill btn-secondary btn-sm" v-else v-show="vista != 2">
                                <i class="icon-plus" aria-hidden="true"></i>&nbsp;Nuevo
                            </button>
                        </div>
                        <div class="col-md-auto" v-show="vista != 0">
                            <button type="button" @click="abrirModal(vista == 1? objetivoRuta:indicadorRuta)" class="btn btn-pill btn-primary btn-sm">
                                <i class="fa fa-reply" aria-hidden="true"></i>&nbsp;Atrás
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div v-show="vista == 0">
                        <div class="form-group row justify-content-start">
                            <label class="col-md-auto my-1 pr-1">Objetivo</label>
                            <div class="col-md-2 pl-1">
                                <input type="text" ref="objetivoOCObjetivo" v-model="objetivoObjCons.objetivo" class="form-control">
                            </div>
                            <label class="col-md-auto my-1 pr-1">Descripción</label>
                            <div class="col-md-2 pl-1">
                                <input type="text" v-model="objetivoObjCons.descripcion" class="form-control">
                            </div>
                            <label class="col-md-auto my-1 pr-1">Estado</label>
                            <div class="col-md-auto pl-1">
                                <select class="form-control" v-model="objetivoObjCons.estado">
                                    <option value="">Todos</option>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                            <label class="col-md-auto my-1 pr-1">Ver</label>
                            <div class="col-md-auto pl-1">
                                <select class="form-control" @change="objetivoListar()" v-model="objetivoObjCons.numRegs">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <button type="button" @click="objetivoListar()" class="btn btn-pill btn-primary btn-sm ml-2"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;Buscar</button>
                        </div>
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Objetivo</th>
                                    <th class="text-center">Descripción</th>
                                    <th class="text-center">Responsable</th>
                                    <th class="text-center">Indicadores</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-if="objetivoArr.length == 0">
                                    <tr>
                                        <td class="text-center" colspan="7">No hay registros</td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr v-for="(objetivo, index) in objetivoArr" :key="objetivo.id">
                                        <td class="text-center" v-text="index + 1"></td>
                                        <td v-text="objetivo.objetivo"></td>
                                        <td v-text="objetivo.descripcion"></td>
                                        <td v-text="objetivo.nombre_completo"></td>
                                        <td class="text-center">
                                            <button type="button" class="btn rounded btn-primary btn-sm" @click="abrirModal(indicadorRuta +'Listar', {id:objetivo.id, objetivo:objetivo.objetivo})" v-if="objetivo.estado">
                                                <i class="fa fa-line-chart" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                <i class="fa fa-line-chart" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge" :class="'badge-'+ (objetivo.estado ? 'success':'danger')" v-text="objetivo.estado ? 'Activo':'Inactivo'"></span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" @click="abrirModal(objetivoRuta +'Actualizar', objetivo)" class="btn rounded btn-warning btn-sm" title="Actualizar" v-if="objetivo.estado && permisosUser.actualizar">
                                                <i class="icon-pencil" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                <i class="icon-pencil" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" @click="actualizarEstado(objetivo.estado, 'este objetivo', objetivoRuta, objetivo.id)" class="btn rounded btn-sm" :class="'btn-'+ (objetivo.estado ? 'danger':'success')" :title="objetivo.estado ? 'Anular':'Activar'" v-if="permisosUser.anular">
                                                <i :class="'icon-'+ (objetivo.estado ? 'trash':'check')" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                <i :class="'icon-'+ (objetivo.estado ? 'trash':'check')" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <nav>
                            <ul class="pagination">
                                <li class="page-item" v-if="objetivoPagin.current_page > 1">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(objetivoPagin.current_page - 1, objetivoObjCons, objetivoPagin, 1)">Ant</a>
                                </li>
                                <li class="page-item" v-for="page in objetivoPagesNumber" :key="page" :class="[page == objetivoPagin.current_page ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page, objetivoObjCons, objetivoPagin, 1)" v-text="page"></a>
                                </li>
                                <li class="page-item" v-if="objetivoPagin.current_page < objetivoPagin.last_page">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(objetivoPagin.current_page + 1, objetivoObjCons, objetivoPagin, 1)">Sig</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <div v-show="vista == 1">
                        <div class="form-group row">
                            <label class="col-md-auto my-1 pr-1">Estado</label>
                            <div class="col-md-auto pl-1">
                                <select class="form-control" v-model="indicadorObjCons.criValEstadoIndicador">
                                    <option value="">Todos</option>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                            <label class="col-md-auto my-1 pr-1">Ver</label>
                            <div class="col-md-auto pl-1">
                                <select class="form-control" @change="indicadorListar()" v-model="indicadorObjCons.numRegs">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <button type="submit" @click="indicadorListar()" class="btn btn-pill btn-primary btn-sm ml-2"><i class="fa fa-search" aria-hidden="true"></i> Buscar</button>
                        </div>
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Indicador</th>
                                    <th class="text-center">Descripción</th>
                                    <th class="text-center">Meta</th>
                                    <th class="text-center">Acumulativo</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Actividades</th>
                                    <th class="text-center">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-if="indicadorArr.length == 0">
                                    <tr>
                                        <td class="text-center" colspan="8">No hay registros</td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr v-for="(indicador, index) in indicadorArr" :key="indicador.id">
                                        <td class="text-center" v-text="index + 1"></td>
                                        <td v-text="indicador.indicador"></td>
                                        <td v-text="indicador.descripcion"></td>
                                        <td v-text="indicador.meta"></td>
                                        <td class="text-center" v-text="indicador.acumulativo ? 'Si': 'No'"></td>
                                        <td class="text-center">
                                            <span class="badge" :class="'badge-'+ (indicador.estado ? 'success':'danger')" v-text="indicador.estado ? 'Activo':'Inactivo'"></span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn rounded btn-primary btn-sm" @click="abrirModal(indicadorActividadRuta, {indicadorId : indicador.id, indicador : indicador.indicador})" v-if="indicador.estado">
                                                <i class="icon-list" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                <i class="icon-list" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" @click="abrirModal(indicadorRuta +'Actualizar', indicador)" class="btn rounded btn-warning btn-sm" title="Actualizar" v-if="indicador.estado && permisosUser.actualizar">
                                                <i class="icon-pencil" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                <i class="icon-pencil" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" @click="actualizarEstado(indicador.estado, 'este indicador', indicadorRuta, indicador.id)" class="btn rounded btn-sm" :class="'btn-'+ (indicador.estado ? 'danger':'success')" :title="indicador.estado ? 'Anular':'Activar'" v-if="permisosUser.anular">
                                                <i :class="'icon-'+ (indicador.estado ? 'trash':'check')" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                <i :class="'icon-'+ (indicador.estado ? 'trash':'check')" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <nav>
                            <ul class="pagination">
                                <li class="page-item" v-if="indicadorPagin.current_page > 1">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(indicadorPagin.current_page - 1, indicadorObjCons, indicadorPagin, 2)">Ant</a>
                                </li>
                                <li class="page-item" v-for="page in indicadorPagesNumber" :key="page" :class="[page == indicadorPagin.current_page ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page, indicadorObjCons, indicadorPagin, 2)" v-text="page"></a>
                                </li>
                                <li class="page-item" v-if="indicadorPagin.current_page < indicadorPagin.last_page">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(indicadorPagin.current_page + 1, indicadorObjCons, indicadorPagin, 2)">Sig</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <div v-show="vista == 2">
                        <div class="form-group row justify-content-center">
                            <label class="col-md-auto my-1 pr-1">Segmento (obligatorio)</label>
                            <div class="col-md-3 pl-1">
                                <select class="form-control" :class="{'is-invalid' : noHaySegmento}" @change="indicadorActividadObjCons.criValFkIdPlanesAccion = ''" v-model="criValFkIdSegmentos">
                                    <option value="" selected>Seleccione</option>
                                    <option v-for="segmento in segmentoArr" :key="segmento.id" :value="segmento.id" v-text="segmento.segmento"></option>
                                </select>
                            </div>
                            <label class="col-md-auto my-1 pr-1">Plan acción (obligatorio)</label>
                            <div class="col-md-3 pl-1">
                                <select class="form-control" :class="{'is-invalid' : noHayPlanAccion}" v-model="indicadorActividadObjCons.criValFkIdPlanesAccion">
                                    <option value="" selected>Seleccione</option>
                                    <option v-for="criArrPlanAccion in criArrPlanesAccion[criValFkIdSegmentos]" :key="criArrPlanAccion.id" :value="criArrPlanAccion.id" v-text="criArrPlanAccion.planAccion"></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <label class="col-md-auto my-1 pr-1">Proyecto</label>
                            <div class="col-md-3 pl-1">
                                <select id="criSelProyecto" class="form-control" v-model="indicadorActividadObjCons.criValFkIdProyectos">
                                    <option value="" selected>Todos</option>
                                    <option v-for="criArrProyecto in criArrProyectos" :key="criArrProyecto.id" :value="criArrProyecto.id" v-text="criArrProyecto.proyecto"></option>
                                </select>
                            </div>
                            <label class="col-md-auto my-1 pr-1">Ordenar por</label>
                            <div class="col-md-auto pl-1">
                                <select class="form-control" v-model="indicadorActividadObjCons.criValColumnaOrden">
                                    <option value="actividad">Actividad</option>
                                    <option value="proyecto">Proyecto</option>
                                    <option value="ponderacion">Ponderación</option>
                                </select>
                            </div>
                            <label class="col-md-auto my-1 pr-1">Dirección</label>
                            <div class="col-md-auto pl-1">
                                <select class="form-control" v-model="indicadorActividadObjCons.criValDireccion">
                                    <option value="asc">Ascendente</option>
                                    <option value="desc">Descendente</option>
                                </select>
                            </div>
                            <button type="button" @click="actividadListar()" class="btn btn-pill btn-primary btn-sm ml-2"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;Buscar</button>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-auto">
                                <table class="table table-bordered table-striped table-sm">
                                    <caption class="caption-on-top">ASIGNADAS</caption>
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Actividad</th>
                                            <th class="text-center">Proyecto</th>
                                            <th class="text-center">Ponderación</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template v-if="actividadAtadasArr.length == 0">
                                            <tr>
                                                <td class="text-center" colspan="5" v-text="(estaListado? 'No hay': 'Busca para listar') +' registros'"></td>
                                            </tr>
                                        </template>
                                        <template v-else>
                                            <tr v-for="(actividad, index) in actividadAtadasArr">
                                                <td class="text-center" v-text="index + 1"></td>
                                                <td v-text="actividad.actividad"></td>
                                                <td v-text="actividad.proyecto"></td>
                                                <td v-text="actividad.ponderacion"></td>
                                                <td class="text-center"><a class="btn rounded btn-danger btn-sm" @click="indReg = index;moverActividad(actividad.id, 'des');"><i class="fa fa-minus fa-lg" aria-hidden="true"></i></a></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-auto">
                            </div>
                            <div class="col-md-auto">
                                <table class="table table-bordered table-striped table-sm">
                                    <caption class="caption-on-top">SIN ASIGNAR</caption>
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Actividad</th>
                                            <th class="text-center">Proyecto</th>
                                            <th class="text-center">Ponderación</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template v-if="actividadDesatadasArr.length == 0">
                                            <tr>
                                                <td class="text-center" colspan="5" v-text="(estaListado? 'No hay': 'Busca para listar') +' registros'"></td>
                                            </tr>
                                        </template>
                                        <template v-else>
                                            <tr v-for="(actividad, index) in actividadDesatadasArr">
                                                <td class="text-center"><a class="btn rounded btn-primary btn-sm" @click="indReg = index;moverActividad(actividad.id);"><i class="fa fa-plus fa-lg" aria-hidden="true"></i></a></td>
                                                <td class="text-center" v-text="index + 1"></td>
                                                <td v-text="actividad.actividad"></td>
                                                <td v-text="actividad.proyecto"></td>
                                                <td v-text="actividad.ponderacion"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Inicio del modal agregar/actualizar-->
        <div id="objetivo-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-primary" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloMdl + ' objetivo'"></h4>
                        <button type="button" class="close" @click="objetivoCerrarMdl()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-row justify-content-start">
                                <div class="form-group col-md-9">
                                    <label>Objetivo</label>
                                    <input type="text" ref="objetivoBObjetivo" v-model="objetivoBody.objetivo" class="form-control" :class="{'is-invalid' : noHayObjetivo}">
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Clasificación</label>
                                    <select v-model="objetivoBody.clasificacion" class="form-control" :class="{'is-invalid' : noHayClasificacion}">
                                        <option value="">Seleccione</option>
                                        <option value="1">HSE</option>
                                        <option value="2">CALIDAD</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row justify-content-start">
                                <div class="form-group col-md-12">
                                    <label>Responsable</label>
                                    <multiselect :class="{'is-danger' : noHayResponsable}" v-model="responsableElegido" :options="responsableArr" :close-on-select="true" :clear-on-select="false" :searchable="true" placeholder="Seleccionar" label="nombre_completo" track-by="id" selectLabel="Presiona enter para marcar" selectedLabel="Marcado" deselectLabel="Presiona enter para desmarcar">
                                        <span slot="noResult">Sin resultados</span>
                                    </multiselect>
                                </div>
                            </div>
                            <div class="form-row justify-content-start">
                                <div class="form-group col-md-2">
                                    <label>Año</label>
                                    <select class="form-control" v-model="objetivoBody.anio">
                                        <option v-for="anioSelector in aniosSelector" :key="anioSelector" :value="anioSelector" v-text="anioSelector"></option>
                                    </select>
                                </div>
                                <div class="form-group col-md-10 mb-0">
                                    <label>Descripción</label>
                                    <textarea :class="{'is-invalid' : noHayDescripcion}" v-model="objetivoBody.descripcion" class="form-control" rows="1"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-pill btn-danger btn-sm" @click="objetivoCerrarMdl()">Cerrar</button>
                        <button type="button" class="btn btn-pill btn-success btn-sm" @click="tipoAccion == 'Guardar'? objetivoRegistrar():objetivoActualizar()" v-text="tipoAccion"></button>
                    </div>
                </div>
            </div>
        </div>
        <div id="indicador-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-primary" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="indicadorTituloMdl +' indicador'"></h4>
                        <button type="button" class="close" @click="indicadorobjetivoCerrarMdlMdl()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-row justify-content-center">
                                <div class="form-group col-md-10">
                                    <label>Indicador</label>
                                    <input type="text" ref="indicadorBIndicador" v-model="indicadorBody.indicador" class="form-control" :class="{'is-invalid' : noHayIndicador}">
                                </div>                                                                                                  
                            </div>
                            <div class="form-row justify-content-center">
                                <div class="form-group col-md-3">
                                    <label>Definición</label>
                                    <select v-model="indicadorBody.definicion" class="form-control" :class="{'is-invalid' : noHayDefinicion}">
                                        <option value="" selected>Seleccione</option>
                                        <option value="1">ESTRUCTURA</option>
                                        <option value="2">PROCESO</option>
                                        <option value="3">RESULTADO</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>&nbsp;</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-addon pb-1">
                                            <input id="chk_acum" type="checkbox" class="mx-1" v-model="indicadorBody.acumulativo">
                                        </span>
                                        <span class="input-group-addon px-0 py-0"><label class="mb-0 px-2" for="chk_acum">Es acumulativo</label></span>
                                    </div>
                                </div>
                                <div class="form-group col-md-3">                                                                                               
                                    <label>Meta</label>
                                    <div class="input-group">
                                    <input type="text" min="0" max="100" v-model="indicadorBody.meta" class="form-control" :class="{'is-invalid' : noHayMeta}">
                                    <span class="input-group-addon py-0">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row justify-content-center">
                                <label class="col-md-2">Descripción</label>
                                <div class="col-md-8">
                                    <textarea v-model="indicadorBody.descripcion" class="form-control" :class="{'is-invalid' : noHayIndicadorDescripcion}" rows="1"></textarea>
                                </div>
                            </div>
                            <div class="form-group row justify-content-center">
                                <label class="col-md-2">Observación (opcional)</label>
                                <div class="col-md-8">
                                    <textarea v-model="indicadorBody.observacion" class="form-control" rows="1"></textarea>
                                </div>
                            </div>
                        </form>                                            
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-pill btn-danger" @click="indicadorobjetivoCerrarMdlMdl()">Cerrar</button>
                        <button type="button" class="btn btn-pill btn-success" @click="tipoAccion == 'Guardar'? indicadorRegistrar():indicadorActualizar()" v-text="tipoAccion"></button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>
<script>
    import Multiselect from 'vue-multiselect'
    export default {
        components: {
            Multiselect
        }
        , props : ['permisosUser' ,'ruta', 'axiosApp']
        , data ()
        {
            return {
                objetivoRuta : 'objetivos'
                , indicadorRuta : 'indicadores'
                , indicadorActividadRuta : 'indicadores_actividades'
                , segmentoRuta : 'segmentos'
                , proyectoRuta : 'conf_proyectos'
                , userRuta : 'users'
                
                , vista : 0
                , tituloVista : 'Objetivos'
                
                , objetivoObjCons : {
                    '?page' : 1
                    , leer : this.permisosUser.leer
                    , objetivo : ''
                    , descripcion : ''
                    , estado : ''
                    , numRegs : 10
                }

                , objetivoArr : []

                , criValEstadoIndicador : ''
                , indicadorArr : []

                , tituloMdl : 'Registrar'

                , objetivoBody : {
                    objetivo : ''
                    , clasificacion : ''
                    , fk_respons_users : ''
                    , anio : 0
                    , descripcion : ''
                }
                , responsableElegido : null
                , responsableArr : []
                , objetivoId : 0

                , anioActual : 0

                , tipoAccion : 'Guardar'
                , objetivoPagin : {
                    'total' : 0
                    , 'current_page' : 0
                    , 'per_page' : 0
                    , 'last_page' : 0
                    , 'from' : 0
                    , 'to' : 0
                }
                , offset : 3

                , indicadorTituloMdl : ''
                
                , indicadorObjCons : {
                    '?page' : 1
                    , leer : this.permisosUser.leer
                    , criValEstadoIndicador : ''
                    , numRegs : 10
                }
                
                , indicadorBody : {
                    indicador : ''
                    , definicion : ''
                    , acumulativo : false
                    , meta : ''
                    , descripcion : ''
                    , observacion : ''
                }
                , indicadorId : 0

                , indicadorPagin : {
                    'total' : 0
                    , 'current_page' : 0
                    , 'per_page' : 0
                    , 'last_page' : 0
                    , 'from' : 0
                    , 'to' : 0
                }

                , indicadorActividadObjCons : {
                    '?leer' : this.permisosUser.leer
                    , criValFkIdPlanesAccion : ''
                    , criValFkIdProyectos : ''
                    , criValColumnaOrden : 'actividad'
                    , criValDireccion : 'asc'
                }
                , segmentoArr : []
                , criValFkIdSegmentos : ''
                , criArrPlanesAccion : []
                , criArrProyectos : []

                , indReg : 0
                , actividadAtadasArr : []
                , actividadDesatadasArr : []

                , objetivoOnVacios : false
                , indicadorOnVacios : false
                , filtrosOnVacios : false

                , estaListado : false
                
            }
        }
        , computed :
        {
            // VALIDACIÓN FILTRO
            noHaySegmento : function ()
            {
                return this.filtrosOnVacios && !this.criValFkIdSegmentos;
            }
            , noHayPlanAccion : function ()
            {
                return this.filtrosOnVacios && !this.indicadorActividadObjCons.criValFkIdPlanesAccion;
            }
            // VALIDACIÓN DE CAMPOS VACÍOS EN FORMULARIOS
            // Campos recurso objetivo
            , noHayObjetivo : function ()
            {
                return this.objetivoOnVacios && !this.objetivoBody.objetivo.trim();
            }
            , noHayDescripcion : function ()
            {
                return this.objetivoOnVacios && !this.objetivoBody.descripcion.trim();
            }
            , noHayClasificacion : function ()
            {
                return this.objetivoOnVacios && !this.objetivoBody.clasificacion;
            }
            , noHayResponsable : function ()
            {
                return this.objetivoOnVacios && !this.responsableElegido;
            }
            // Campos recurso indicador
            , noHayIndicador : function ()
            {
                return this.indicadorOnVacios && !this.indicadorBody.indicador.trim();
            }
            , noHayIndicadorDescripcion : function ()
            {
                return this.indicadorOnVacios && !this.indicadorBody.descripcion.trim();
            }
            , noHayMeta : function ()
            {
                return this.indicadorOnVacios && (!this.indicadorBody.meta.trim() || this.indicadorBody.meta == '0');
            }
            , noHayDefinicion : function ()
            {
                return this.indicadorOnVacios && !this.indicadorBody.definicion;
            }
            , aniosSelector : function () {
                let fechaActual = new Date(), arrAnios = [];
                let anioIni = this.anioActual = fechaActual.getFullYear();
                for (let i = 0; i < 5; i++) {
                    arrAnios.push(anioIni + i);
                }
                return arrAnios;
            }

            , objetivoPagesNumber : function() {
                if (!this.objetivoPagin.to) {
                    return [];
                }
                let from = this.objetivoPagin.current_page - this.offset; 
                if (from < 1) {
                    from = 1;
                }
                let to = from + (this.offset * 2); 
                if (to >= this.objetivoPagin.last_page) {
                    to = this.objetivoPagin.last_page;
                }
                let pagesArray = [];
                while (from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;
            }
            , indicadorPagesNumber : function() {
                if (!this.indicadorPagin.to) {
                    return [];
                }
                let from = this.indicadorPagin.current_page - this.offset; 
                if (from < 1) {
                    from = 1;
                }
                let to = from + (this.offset * 2); 
                if (to >= this.indicadorPagin.last_page) {
                    to = this.indicadorPagin.last_page;
                }
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
            abrirModal (accion, data = {})
            {
                switch (accion) {
                    case this.objetivoRuta +'Registrar':
                        $('#objetivo-mdl').modal('show');
                        break;
                    case this.objetivoRuta +'Actualizar':
                        this.tituloMdl = 'Editar';
                        this.tipoAccion = 'Actualizar';
                        this.objetivoBody.objetivo = data.objetivo;
                        this.responsableElegido = {id : data.fk_respons_users, nombre_completo : data.nombre_completo};
                        this.objetivoBody.clasificacion = data.clasificacion == 'HSE' ? 1:2;
                        this.objetivoBody.anio = data.anio;
                        this.objetivoBody.descripcion = data.descripcion;
                        this.objetivoId = data.id;
                        $('#objetivo-mdl').modal('show');
                        break;
                    case this.indicadorRuta +'Listar':
                        this.objetivoId = data.id;
                        this.tituloVista = 'Indicadores';
                        this.indicadorListar();
                        this.vista = 1;
                        break;
                    case this.objetivoRuta:
                        this.objetivoId = this.vista = 0;
                        this.tituloVista = 'Objetivos';
                        this.indicadorArr = [];
                        this.indicadorObjCons.criValEstadoIndicador = '';
                        this.$refs.objetivoOCObjetivo.focus(); // ESTE FOCO NO FUNCIONA PORQUE SOLO SE ESTA CAMBIANDO LA VISIBILIDAD ENTRE OBJETIVOS E INDICADORES
                        break;
                    case this.indicadorRuta +'Registrar':
                        $('#indicador-mdl').modal('show');
                        break;
                    case this.indicadorRuta +'Actualizar':
                        this.indicadorTituloMdl = 'Editar';
                        this.indicadorBody.acumulativo = data.acumulativo;
                        this.indicadorBody.definicion = data.definicion == 'ESTRUCTURA' ? 1 : data.definicion == 'PROCESO' ? 2 : 3;
                        this.indicadorBody.descripcion = data.descripcion;
                        this.indicadorBody.indicador = data.indicador;
                        this.indicadorBody.meta = ''+ data.meta;
                        this.indicadorBody.observacion = data.observacion;
                        this.indicadorId = data.id;
                        this.tipoAccion = 'Actualizar';
                        $('#indicador-mdl').modal('show');
                        break;
                    case this.indicadorActividadRuta:
                        this.indicadorId = data.indicadorId;
                        this.tituloVista = `Gestión actividades - ${data.indicador}`;
                        this.vista = 2;
                        break;
                    case this.indicadorRuta:
                        this.vista = 1;
                        this.indicadorId = 0;
                        this.criValFkIdSegmentos = this.indicadorActividadObjCons.criValFkIdPlanesAccion = this.indicadorActividadObjCons.criValFkIdProyectos = '';
                        this.indicadorActividadObjCons.criValColumnaOrden = 'actividad';
                        this.indicadorActividadObjCons.criValDireccion = 'asc';
                        this.filtrosOnVacios = false;
                        this.actividadAtadasArr = [];
                        this.actividadDesatadasArr = [];
                        this.estaListado = false;
                        this.tituloVista = 'Indicadores';
                        break;
                }
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
                            me.criArrProyectos = respuesta.proyectos;
                            break;
                        case me.indicadorActividadRuta +'/basico_planes_accion':
                            me.criArrPlanesAccion = response.data.planesAccion;
                            break;
                        case me.userRuta +'/simple':
                            me.responsableArr = respuesta.usuarios;
                            break;
                        case me.objetivoRuta:
                            me.objetivoArr = respuesta.objetivos.data;
                            me.objetivoPagin = respuesta.pagination;
                            break;
                        case me.objetivoRuta +'/registrar':
                        case me.objetivoRuta +'/actualizar/':
                            $('#objetivo-mdl').modal('hide');
                            me.objetivoCerrarMdl();
                            me.objetivoListar();
                            break;
                        case me.objetivoRuta +'/desactivar/':
                        case me.objetivoRuta +'/activar/':
                            me.objetivoListar();
                            break;
                        case me.indicadorRuta:
                            me.indicadorArr = respuesta.indicadores.data;
                            me.indicadorPagin = respuesta.pagination;
                            break;
                        case me.indicadorRuta +'/registrar':
                        case me.indicadorRuta +'/actualizar/':
                            $('#indicador-mdl').modal('hide');
                            me.indicadorobjetivoCerrarMdlMdl();
                            me.indicadorListar();
                            break;
                        case me.indicadorRuta +'/desactivar/':
                        case me.indicadorRuta +'/activar/':
                            me.indicadorListar();
                            break;
                        case me.indicadorActividadRuta:
                            me.actividadAtadasArr = respuesta.actividades.atadas;
                            me.actividadDesatadasArr = respuesta.actividades.desatadas;
                            break;
                        case me.indicadorActividadRuta +'/desatar':
                            me.trasladarActividad(me.indReg, me.actividadAtadasArr, me.actividadDesatadasArr);
                            break;
                        case me.indicadorActividadRuta +'/atar':
                            me.trasladarActividad(me.indReg, me.actividadDesatadasArr, me.actividadAtadasArr);
                            break;
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
            }
            , esRecursoInvalido (validar) {
                switch (validar) {
                    case 1: if (this.validarObjetivo())return true;
                        break;
                    case 2: if (this.validarIndicador())return true;
                        break;
                }
                return false;
            }
            , peticionGet (recursoRuta, cadCons = '')
            {
                let peticion = this.axiosApp.get(recursoRuta + cadCons);
                this.obtenerRespuesta(recursoRuta, peticion);
            }
            , peticionPost (recursoRuta, recursoBody, validar = 0)
            {
                if (this.esRecursoInvalido(validar))return;
                let peticion = this.axiosApp.post(recursoRuta, recursoBody);
                this.obtenerRespuesta(recursoRuta, peticion);
            }
            , peticionPut (ruta, recursoId, recursoBody, validar)
            {
                if (this.esRecursoInvalido(validar))return;
                let peticion = this.axiosApp.put(ruta + recursoId, recursoBody);
                this.obtenerRespuesta(ruta, peticion);
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
            , obtenerCadCons (cadCons)
            {
                return Object.entries(cadCons).map(filtro => `${filtro[0]}=${filtro[1]}`).join('&');
            }
            , cambiarPagina (page, refObjCons, refPagin, listar) {
                refObjCons['?page'] = refPagin.current_page = page;
                switch (listar) {
                    case 1: this.objetivoListar();break;
                    case 2: this.indicadorListar();break;
                }
            }
            //-------------------------------------------------------------------------------------------------------
            , objetivoListar ()
            {
                this.peticionGet(this.objetivoRuta, this.obtenerCadCons(this.objetivoObjCons));
            }
            , validarObjetivo ()
            {
                return this.noHayObjetivo || this.noHayDescripcion || this.noHayClasificacion || this.noHayResponsable;
            }
            , objetivoRegistrar ()
            {
                this.objetivoBody.fk_respons_users = this.responsableElegido == null ? '' : this.responsableElegido.id;
                this.objetivoOnVacios = true;
                let url = `${this.objetivoRuta}/registrar`;
                this.peticionPost(url, this.objetivoBody, 1);
            }
            , objetivoActualizar ()
            {
                this.objetivoBody.fk_respons_users = this.responsableElegido == null ? '' : this.responsableElegido.id;
                this.objetivoOnVacios = true;
                let url = `${this.objetivoRuta}/actualizar/`;
                this.peticionPut(url, this.objetivoId, this.objetivoBody, 1);
            }
            , objetivoCerrarMdl ()
            {
                this.objetivoId = 0;
                this.tituloMdl = 'Registrar';
                this.objetivoBody.objetivo = this.objetivoBody.clasificacion = this.objetivoBody.fk_respons_users = this.objetivoBody.descripcion = '';
                this.responsableElegido = null;
                this.objetivoBody.anio =this.anioActual;
                this.tipoAccion = 'Guardar';
                this.objetivoOnVacios = false;
                $('#objetivo-mdl').modal('hide');
            }
            , indicadorListar ()
            {
                this.peticionGet(this.indicadorRuta, this.obtenerCadCons({...this.indicadorObjCons, fkIdObjetivos : this.objetivoId}));
            }
            , validarIndicador ()
            {
                return this.noHayIndicador || this.noHayIndicadorDescripcion || this.noHayMeta || this.noHayDefinicion;
            }
            , indicadorRegistrar ()
            {
                this.indicadorOnVacios = true;
                let ruta = this.indicadorRuta +'/registrar', body = {...this.indicadorBody, fk_id_objetivos : this.objetivoId};
                this.peticionPost(ruta, body, 2);
            }
            , indicadorActualizar ()
            {
                this.indicadorOnVacios = true;
                let ruta = this.indicadorRuta +'/actualizar/';
                this.peticionPut(ruta, this.indicadorId, this.indicadorBody, 2);
            }
            , indicadorobjetivoCerrarMdlMdl ()
            {
                this.indicadorTituloMdl = 'Registrar';
                this.indicadorBody.acumulativo = this.indicadorId = 0;
                this.indicadorBody.definicion = this.indicadorBody.descripcion = this.indicadorBody.indicador = this.indicadorBody.meta = this.indicadorBody.observacion = '';
                this.indicadorOnVacios = false;
                this.tipoAccion = 'Guardar';
                $('#indicador-mdl').modal('hide');
            }
            , actualizarEstado (recursoEstado, finalTitle, recursoRuta, recursoId)
            {
                let prefijo = recursoEstado?'des':'';
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
            , actividadListar ()
            {
                this.filtrosOnVacios = true;
                if (this.noHaySegmento || this.noHayPlanAccion) {
                    return;
                } else {
                    this.filtrosOnVacios = false;
                }
                this.estaListado = true;
                this.peticionGet(this.indicadorActividadRuta, this.obtenerCadCons(this.indicadorActividadObjCons));
            }
            , moverActividad (actividadId, prefijo = '')
            {
                let url = `${this.indicadorActividadRuta}/${prefijo}atar`, cuerpo = {indicadorId : this.indicadorId, fkIdActividades: actividadId};
                this.peticionPost(url, cuerpo);
            }
            , trasladarActividad (indReg, arrSalida, arrEntrada)
            {
                let actividad = arrSalida.splice(indReg, 1)[0], ultimo = arrEntrada.length - 1;
                if (ultimo > -1) {
                    for (let i = 0; i < arrEntrada.length; i++) {
                        if (actividad.indice < arrEntrada[i].indice) {
                            arrEntrada.splice(i, 0, actividad);
                            break;
                        } else if (i == ultimo) {
                            arrEntrada.push(actividad);
                        }
                    }
                } else {
                    arrEntrada.push(actividad);
                }
            }
            , segmentoSimple ()
            {
                let ruta = this.segmentoRuta +'/simple', cadCons = `?leer=${this.permisosUser.leer}`;
                this.peticionGet(ruta, cadCons);
            }
            , basicoPlanesAccion ()
            {
                let ruta = this.indicadorActividadRuta +'/basico_planes_accion', cadCons = `?leer=${this.permisosUser.leer}`;
                this.peticionGet(ruta, cadCons);
            }
            , proyectoSimple ()
            {
                let ruta = this.proyectoRuta +'/simple', cadCons = `?leer=${this.permisosUser.leer}`;
                this.peticionGet(ruta, cadCons);
            }
            , userSimple ()
            {
                let ruta = this.userRuta +'/simple';
                this.peticionGet(ruta);
            }
        }
        , mounted ()
        {
            this.objetivoListar();
            this.userSimple();
            this.segmentoSimple();
            this.basicoPlanesAccion();
            this.proyectoSimple();
            this.objetivoBody.anio = this.anioActual;
            this.$refs.objetivoOCObjetivo.focus();

            $('#objetivo-mdl').on('shown.bs.modal', () => this.$refs.objetivoBObjetivo.focus());
            $('#objetivo-mdl').on('hidden.bs.modal', () => this.$refs.objetivoOCObjetivo.focus());
            $('#indicador-mdl').on('shown.bs.modal', () => this.$refs.indicadorBIndicador.focus());
        }
    }
</script>
<style>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .caption-on-top{
        caption-side: top;
        text-align: center;
    }
    .is-danger .multiselect__tags {
        border-color: #f86c6b;
    }
</style>