<template>
    <main class="main">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Escritorio</a></li>
        </ol>
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-auto">
                                    <i class="fa fa-bars fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;Cuadro de mando
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="accordion" role="tablist" aria-multiselectable="true">
                                <div class="card no-mb">
                                    <div class="card-header" role="tab" id="headingOne">
                                        <div class="row">
                                            <div class="col-12 col-sm-12 col-md-3 h5 my-1 pt-2 text-center">
                                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                    Actividades
                                                </a>
                                            </div>
                                            <div class="col">
                                                <div class="row">
                                                    <span class="col-12 col-sm-12 col-md-2 my-1 pr-1">Anual</span>
                                                    <div class="col-10 col-sm-11 col-md-6 pr-1 my-1">
                                                        <div class="progress bg-unfilled btn-pill">
                                                            <div class="progress-bar" :class="{'bg-danger': actsPorcentajeAnual <= 69, 'bg-warning': actsPorcentajeAnual >= 70 && actsPorcentajeAnual <= 89, 'bg-success': actsPorcentajeAnual >= 90}" role="progressbar" :style="{width: actsPorcentajeAnual +'%'}" :aria-valuenow="actsPorcentajeAnual" aria-valuemin="0" aria-valuemax="100" v-text="actsPorcentajeAnual +'%'"></div>
                                                        </div>
                                                    </div>
                                                    <span class="col-2 col-sm-1 col-md-1 my-1 px-1" v-text="actsCumplidas +'/'+ actsAnual"></span>
                                                </div>
                                                <div class="row">
                                                    <span class="col-12 col-sm-12 col-md-2 my-1 pr-1">A la fecha</span>
                                                    <div class="col-10 col-sm-11 col-md-6 my-1 pr-1">
                                                        <div class="progress bg-unfilled btn-pill">
                                                            <div class="progress-bar" :class="{'bg-danger': actsPorcentajeHastaHoy <= 69, 'bg-warning': actsPorcentajeHastaHoy >= 70 && actsPorcentajeHastaHoy <= 89, 'bg-success': actsPorcentajeHastaHoy >= 90}" role="progressbar" :style="{width: actsPorcentajeHastaHoy +'%'}" :aria-valuenow="actsPorcentajeHastaHoy" aria-valuemin="0" aria-valuemax="100" v-text="actsPorcentajeHastaHoy +'%'"></div>
                                                        </div>
                                                    </div>
                                                    <span class="col-2 col-sm-1 col-md-1 my-1 px-1" v-text="actsHastaHoyCumplidas +'/'+ actsHastaHoy"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="collapseOne" class="collapse" role="tabpanel" aria-labelledby="headingOne">
                                        <div class="card-block">
                                            <div class="row">
                                                <div class="col-12 el1-opciones mb-3">
                                                    <select ref="el2opciones" class="form-control" @change="actividadEnfoqueCambiar($event.target.value)">
                                                        <option value="fk_id_segmentos">Por segmento</option>
                                                        <option value="fk_id_planes_accion">Por plan de acción</option>
                                                        <option value="fk_id_proyectos">Por proyecto</option>
                                                    </select>
                                                </div>
                                                <div class="col-12 el2-opciones mb-3">
                                                    <ul class="nav nav-pills">
                                                        <li class="nav-item">
                                                            <a class="nav-link rounded text-center width-same" :class="{'active': actividadFkIdPadre == 'fk_id_segmentos'}" href="# " @click.prevent="actividadEnfoqueCambiar('fk_id_segmentos')">Por segmento</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link rounded text-center width-same" :class="{'active': actividadFkIdPadre == 'fk_id_planes_accion'}" href="#" @click.prevent="actividadEnfoqueCambiar('fk_id_planes_accion')">Por plan de acción</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link rounded text-center width-same" :class="{'active': actividadFkIdPadre == 'fk_id_proyectos'}" href="#" @click.prevent="actividadEnfoqueCambiar('fk_id_proyectos')">Por proyecto</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div id="recursoAccordion" role="tablist" aria-multiselectable="true">
                                                <div class="card no-mb" v-for="enfoque in enfoqueArr" :key="enfoque[actividadFkIdPadre]">
                                                    <div class="card-header" role="tab" :id="'headingRecurso'+ enfoque[actividadFkIdPadre]">
                                                        <div class="row">
                                                            <div class="col-12 col-sm-12 col-md-3 text-center">
                                                                <a data-toggle="collapse" data-parent="#recursoAccordion" :href="'#collapseRecurso'+ enfoque[actividadFkIdPadre]" aria-expanded="true" :aria-controls="'collapseRecurso'+ enfoque[actividadFkIdPadre]" v-text="enfoque[actividadNombrePadre]"></a>
                                                            </div>
                                                            <div class="col">
                                                                <div class="row">
                                                                    <span class="col-12 col-sm-12 col-md-2 pr-1"></span>
                                                                    <div class="col-10 col-sm-11 col-md-6 pr-1">
                                                                        <div class="progress bg-unfilled btn-pill">
                                                                            <div class="progress-bar" :class="{'bg-danger': enfoque.pctoActis <= 69, 'bg-warning': enfoque.pctoActis >= 70 && enfoque.pctoActis <= 89, 'bg-success': enfoque.pctoActis >= 90}" role="progressbar" :style="{width: enfoque.pctoActis +'%'}" :aria-valuenow="enfoque.pctoActis" aria-valuemin="0" aria-valuemax="100" v-text="enfoque.pctoActis +'%'"></div>
                                                                        </div>
                                                                    </div>
                                                                    <span class="col-2 col-sm-1 col-md-1 px-1" v-text="enfoque.nActisCump +'/'+ enfoque.nActis"></span>
                                                                    <span class="col-12 col-sm-12 col-md-3" v-text="'Próximas a vencer: '+ enfoque.porVencer"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div :id="'collapseRecurso'+ enfoque[actividadFkIdPadre]" class="collapse" role="tabpanel" :aria-labelledby="'headingRecurso'+ enfoque[actividadFkIdPadre]">
                                                        <div class="card-block">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="el1-tabla table-responsive">
                                                                        <table class="table table-bordered table-sm">
                                                                            <template v-if="tmpActividadArr.length == 0">
                                                                                <tr>
                                                                                    <th class="text-center">#</th>
                                                                                    <td class="align-middle text-center" rowspan="6">No hay registros</td>
                                                                                </tr>
                                                                                <tr><th class="text-center">Actividad</th></tr>
                                                                                <tr v-if="actividadFkIdPadre != 'fk_id_segmentos'"><th class="text-center">Segmento</th></tr>
                                                                                <tr v-if="actividadFkIdPadre != 'fk_id_proyectos'"><th class="text-center">Proyecto</th></tr>
                                                                                <tr v-if="actividadFkIdPadre != 'fk_id_planes_accion'"><th th class="text-center">Plan de acción</th></tr>
                                                                                <tr th class="text-center"><th>Responsables</th></tr>
                                                                                <tr th class="text-center"><th>Estado</th></tr>
                                                                            </template>
                                                                            <template v-else>
                                                                                <tr v-html="tmpActividadArr[enfoque[actividadFkIdPadre]].indices"></tr>
                                                                                <tr v-html="tmpActividadArr[enfoque[actividadFkIdPadre]].actividades"></tr>
                                                                                <tr v-if="actividadFkIdPadre != 'fk_id_segmentos'" v-html="tmpActividadArr[enfoque[actividadFkIdPadre]].segmentos"></tr>
                                                                                <tr v-if="actividadFkIdPadre != 'fk_id_proyectos'" v-html="tmpActividadArr[enfoque[actividadFkIdPadre]].proyectos"></tr>
                                                                                <tr v-if="actividadFkIdPadre != 'fk_id_planes_accion'" v-html="tmpActividadArr[enfoque[actividadFkIdPadre]].planesAccion"></tr>
                                                                                <tr v-html="tmpActividadArr[enfoque[actividadFkIdPadre]].responsables"></tr>
                                                                                <tr v-html="tmpActividadArr[enfoque[actividadFkIdPadre]].estados"></tr>
                                                                            </template>
                                                                        </table>
                                                                    </div>
                                                                    <div class="el2-tabla table-responsive">
                                                                        <table class="table table-bordered table-sm">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th class="text-center">#</th>
                                                                                    <th class="text-center">Actividad</th>
                                                                                    <th class="text-center" v-if="actividadFkIdPadre != 'fk_id_segmentos'">Segmento</th>
                                                                                    <th class="text-center" v-if="actividadFkIdPadre != 'fk_id_proyectos'">Proyecto</th>
                                                                                    <th class="text-center" v-if="actividadFkIdPadre != 'fk_id_planes_accion'">Plan de acción</th>
                                                                                    <th class="text-center">Responsable</th>
                                                                                    <th class="text-center">Estado</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <template v-if="actividadArr.length == 0">
                                                                                    <tr>
                                                                                        <td class="text-center" colspan="3">No hay registros</td>
                                                                                    </tr>
                                                                                </template>
                                                                                <template v-else>
                                                                                    <tr v-for="(actividad, index) in actividadArr.filter(actividad => actividad[actividadFkIdPadre] == enfoque[actividadFkIdPadre])" :key="actividad.id">
                                                                                        <td class="text-center" v-text="index + 1"></td>
                                                                                        <td v-text="actividad.actividad"></td>
                                                                                        <td v-if="actividadFkIdPadre != 'fk_id_segmentos'" v-text="actividad.segmento"></td>
                                                                                        <td v-if="actividadFkIdPadre != 'fk_id_proyectos'" v-text="actividad.proyecto"></td>
                                                                                        <td v-if="actividadFkIdPadre != 'fk_id_planes_accion'" v-text="actividad.planAccion"></td>
                                                                                        <td>
                                                                                            <ul class="list-group">
                                                                                                <li class="list-group-item py-0" v-for="responsable in actividad.responsables" :key="responsable.id" v-text="responsable.nombre_completo"></li>
                                                                                            </ul>
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            <span class="badge" :class="'badge-' + (actividad.fechaReporte ? 'success': actividad.diaMesLimite >= diaMes? 'warning' : 'danger')" v-text="actividad.fechaReporte ? 'Cumplida' : actividad.diaMesLimite >= diaMes? 'Pendiente' : 'Vencida'"></span>
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
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card no-mb">
                                    <div class="card-header" role="tab" id="headingTwo">
                                        <div class="row">
                                            <div class="col-12 col-sm-12 col-md-3 h5 my-1 text-center">
                                                <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                    Mesa de ayuda
                                                </a>
                                            </div>
                                            <div class="col mt-1">
                                                <div class="row">
                                                    <div class="col-12 col-sm-12 col-md-2 my-1 pr-1"></div>
                                                    <div class="col-10 col-sm-11 col-md-6 my-1 pr-1">
                                                        <div class="progress bg-unfilled btn-pill">
                                                            <div class="progress-bar" :class="{'bg-danger': asigsAnualPorcentaje <= 69, 'bg-warning': asigsAnualPorcentaje >= 70 && asigsAnualPorcentaje <= 89, 'bg-success': asigsAnualPorcentaje >= 90}" role="progressbar" :style="{width: asigsAnualPorcentaje +'%'}" :aria-valuenow="asigsAnualPorcentaje" aria-valuemin="0" aria-valuemax="100" v-text="asigsAnualPorcentaje +'%'"></div>
                                                        </div>
                                                    </div>
                                                    <span class="col-2 col-sm-1 col-md-1 my-1 px-1" v-text="asigsAnualCump +'/'+ asigsAnual"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="collapseTwo" class="collapse" role="tabpanel" aria-labelledby="headingTwo">
                                        <div class="card-block">
                                            <fieldset>
                                                <legend class="mb-0">
                                                    <div class="col pl-0 legends-center">
                                                        <h5>Proyectos</h5>
                                                    </div>
                                                </legend>
                                                <div id="mdaProyectoAccordion" role="tablist" aria-multiselectable="true">
                                                    <div class="card no-mb" v-for="mdaProyecto in mdaProyectosArr" :key="mdaProyecto.id">
                                                        <div class="card-header" role="tab" :id="'headingMdaProyecto'+ mdaProyecto.id">
                                                            <div class="row">
                                                                <div class="col-12 col-sm-12 col-md-3 text-center">
                                                                    <a data-toggle="collapse" data-parent="#mdaProyectoAccordion" :href="'#collapseMdaProyecto'+ mdaProyecto.id" aria-expanded="true" :aria-controls="'collapseMdaProyecto'+ mdaProyecto.id" v-text="mdaProyecto.proyecto"></a>
                                                                </div>
                                                                <div class="col">
                                                                    <div class="row">
                                                                        <span class="col-12 col-sm-12 col-md-2 pr-1"></span>
                                                                        <div class="col-10 col-sm-11 col-md-6 pr-1">
                                                                            <div class="progress bg-unfilled btn-pill">
                                                                                <div class="progress-bar" :class="{'bg-danger': mdaProyecto.pctoAsigs <= 69, 'bg-warning': mdaProyecto.pctoAsigs >= 70 && mdaProyecto.pctoAsigs <= 89, 'bg-success': mdaProyecto.pctoAsigs >= 90}" role="progressbar" :style="{width: mdaProyecto.pctoAsigs +'%'}" :aria-valuenow="mdaProyecto.pctoAsigs" aria-valuemin="0" aria-valuemax="100" v-text="mdaProyecto.pctoAsigs +'%'"></div>
                                                                            </div>
                                                                        </div>
                                                                        <span class="col-2 col-sm-1 col-md-1 px-1" v-text="mdaProyecto.nAsigsCump +'/'+ mdaProyecto.nAsigs"></span>
                                                                        <span class="col-12 col-sm-12 col-md-3" v-text="'Próximos a vencer: '+ mdaProyecto.porVencer"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div :id="'collapseMdaProyecto'+ mdaProyecto.id" class="collapse" role="tabpanel" :aria-labelledby="'headingMdaProyecto'+ mdaProyecto.id">
                                                            <div class="card-block">
                                                                <fieldset>
                                                                    <legend class="mb-0">
                                                                        <div class="col pl-0 legends-center">
                                                                            <h5>Pendientes</h5>
                                                                        </div>
                                                                    </legend>
                                                                    <div id="pendienteAccordion" role="tablist" aria-multiselectable="true">
                                                                        <div class="card no-mb" v-for="pendiente in mdaProyecto.pendientes" :key="pendiente.id">
                                                                            <div class="card-header" role="tab" :id="'headingPendiente'+ pendiente.id">
                                                                                <div class="row">
                                                                                    <div class="col-12 col-sm-12 col-md-3 text-center">
                                                                                        <a data-toggle="collapse" data-parent="#pendienteAccordion" :href="'#collapsePendiente'+ pendiente.id" aria-expanded="true" :aria-controls="'collapsePendiente'+ pendiente.id" v-text="pendiente.pendiente"></a>
                                                                                    </div>
                                                                                    <div class="col">
                                                                                        <div class="row">
                                                                                            <span class="col-12 col-sm-12 col-md-2 pr-1"></span>
                                                                                            <div class="col-10 col-sm-11 col-md-6 pr-1">
                                                                                                <div class="progress bg-unfilled btn-pill">
                                                                                                    <div class="progress-bar" :class="{'bg-danger': pendiente.pctoAsigs <= 69, 'bg-warning': pendiente.pctoAsigs >= 70 && pendiente.pctoAsigs <= 89, 'bg-success': pendiente.pctoAsigs >= 90}" role="progressbar" :style="{width: pendiente.pctoAsigs +'%'}" :aria-valuenow="pendiente.pctoAsigs" aria-valuemin="0" aria-valuemax="100" v-text="pendiente.pctoAsigs +'%'"></div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <span class="col-2 col-sm-1 col-md-1 px-1" v-text="pendiente.nAsigsCump +'/'+ pendiente.nAsigs"></span>
                                                                                            <span class="col-12 col-sm-12 col-md-3" v-text="'Próximas a vencer: '+ pendiente.porVencer"></span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div :id="'collapsePendiente'+ pendiente.id" class="collapse" role="tabpanel" :aria-labelledby="'headingPendiente'+ pendiente.id">
                                                                                <div class="card-block">
                                                                                    <div class="el1-tabla table-responsive">
                                                                                        <table class="table table-bordered table-sm">
                                                                                            <template v-if="pendiente.asignaciones.length == 0">
                                                                                                <tr>
                                                                                                    <th class="text-center">#</th>
                                                                                                    <td class="align-middle text-center" rowspan="4">No hay registros</td>
                                                                                                </tr>
                                                                                                <tr><th class="text-center">Asignación</th></tr>
                                                                                                <tr><th class="text-center">Responsable</th></tr>
                                                                                                <tr><th class="text-center">Estado</th></tr>
                                                                                            </template>
                                                                                            <template v-else>        
                                                                                                <tr v-html="tmpAsignacionesArr[pendiente.id].indices"></tr>
                                                                                                <tr v-html="tmpAsignacionesArr[pendiente.id].asignaciones"></tr>
                                                                                                <tr v-html="tmpAsignacionesArr[pendiente.id].responsables"></tr>
                                                                                                <tr v-html="tmpAsignacionesArr[pendiente.id].estados"></tr>
                                                                                            </template>
                                                                                        </table>
                                                                                    </div>
                                                                                    <div class="el2-tabla table-responsive">
                                                                                        <table class="table table-bordered table-striped table-sm">
                                                                                            <thead>
                                                                                                <tr>
                                                                                                    <th class="text-center">#</th>
                                                                                                    <th class="text-center">Asignación</th>
                                                                                                    <th class="text-center">Responsable</th>
                                                                                                    <th class="text-center">Estado</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody>
                                                                                                <template v-if="pendiente.asignaciones.length == 0">
                                                                                                    <tr>
                                                                                                        <td class="text-center" colspan="4">No hay registros</td>
                                                                                                    </tr>
                                                                                                </template>
                                                                                                <template v-else>
                                                                                                    <tr v-for="(asignacion, index) in pendiente.asignaciones" :key="asignacion.id">
                                                                                                        <td class="text-center" v-text="index + 1"></td>
                                                                                                        <td v-text="asignacion.asignacion"></td>
                                                                                                        <td v-text="asignacion.responsable.nombre_completo"></td>
                                                                                                        <td class="text-center">
                                                                                                            <span class="badge" :class="'badge-' + (asignacion.condicion == 'Cerrado'? 'success' : asignacion.fechaLimite >= fechaActual ? 'warning': 'danger')" v-text="asignacion.condicion == 'Cerrado'? 'Cumplida' : asignacion.fechaLimite >= fechaActual ? 'Pendiente': 'Vencida'"></span>
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
                                                                </fieldset>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>
                                <div class="card no-mb">
                                    <div class="card-header" role="tab" id="headingThree">
                                        <div class="row">
                                            <div class="col-12 col-sm-12 col-md-3 h5 my-1 text-center">
                                                <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                    Raic
                                                </a>
                                            </div>
                                            <div class="col mt-1">
                                                <div class="row">
                                                    <div class="col-12 col-sm-12 col-md-2 my-1 pr-1"></div>
                                                    <div class="col-10 col-sm-11 col-md-6 my-1 pr-1">
                                                        <div class="progress bg-unfilled btn-pill">
                                                            <div class="progress-bar bg-danger" :class="{'bg-danger': contsAnualPcto <= 69, 'bg-warning': contsAnualPcto >= 70 && contsAnualPcto <= 89, 'bg-success': contsAnualPcto >= 90}" role="progressbar" :style="{width: contsAnualPcto +'%'}" :aria-valuenow="contsAnualPcto +'%'" aria-valuemin="0" aria-valuemax="100" v-text="contsAnualPcto +'%'"></div>
                                                        </div>
                                                    </div>
                                                    <span class="col-2 col-sm-1 col-md-1 my-1 px-1" v-text="contsAnualCump +'/'+ contsAnual"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="collapseThree" class="collapse" role="tabpanel" aria-labelledby="headingThree">
                                        <div class="card-block">
                                            <fieldset>
                                                <legend class="mb-0">
                                                    <div class="col pl-0 legends-center">
                                                        <h5>Proyectos</h5>
                                                    </div>
                                                </legend>
                                                <div id="raicProyectoAccordion" role="tablist" aria-multiselectable="true">
                                                    <div class="card no-mb" v-for="raicProyecto in raicProyectosArr" :key="raicProyecto.id">
                                                        <div class="card-header" role="tab" :id="'headingProyecto'+ raicProyecto.id">
                                                            <div class="row">
                                                                <div class="col-12 col-sm-12 col-md-3 text-center">
                                                                    <a data-toggle="collapse" data-parent="#raicProyectoAccordion" :href="'#collapseRaicProyecto'+ raicProyecto.id" aria-expanded="true" :aria-controls="'collapseRaicProyecto'+ raicProyecto.id" v-text="raicProyecto.proyecto"></a>
                                                                </div>
                                                                <div class="col">
                                                                    <div class="row">
                                                                        <div class="col-12 col-sm-12 col-md-2 pr-1"></div>
                                                                        <div class="col-10 col-sm-11 col-md-6 pr-1">
                                                                            <div class="progress bg-unfilled btn-pill">
                                                                                <div class="progress-bar" :class="{'bg-danger': raicProyecto.pctoConts <= 69, 'bg-warning': raicProyecto.pctoConts >= 70 && raicProyecto.pctoConts <= 89, 'bg-success': raicProyecto.pctoConts >= 90}" role="progressbar" :style="{width: raicProyecto.pctoConts +'%'}" :aria-valuenow="raicProyecto.pctoConts" aria-valuemin="0" aria-valuemax="100" v-text="raicProyecto.pctoConts +'%'"></div>
                                                                            </div>
                                                                        </div>
                                                                        <span class="col-2 col-sm-1 col-md-1 px-1" v-text="raicProyecto.nContsCump +'/'+ raicProyecto.nConts"></span>
                                                                        <span class="col-12 col-sm-12 col-md-3" v-text="'Próximos a vencer: '+ raicProyecto.porVencer"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div :id="'collapseRaicProyecto'+ raicProyecto.id" class="collapse" role="tabpanel" :aria-labelledby="'headingProyecto'+ raicProyecto.id">
                                                            <div class="card-block">
                                                                <fieldset>
                                                                    <legend class="mb-0">
                                                                        <div class="col pl-0 legends-center">
                                                                            <h5>Raics</h5>
                                                                        </div>
                                                                    </legend>
                                                                    <div id="raicAccordion" role="tablist" aria-multiselectable="true">
                                                                        <div class="card no-mb" v-for="raic in raicProyecto.raics" :key="raic.id">
                                                                            <div class="card-header" role="tab" :id="'headingRaic'+ raic.id">
                                                                                <div class="row">
                                                                                    <div class="col-12 col-sm-12 col-md-3 text-center">
                                                                                        <a data-toggle="collapse" data-parent="#raicAccordion" :href="'#collapseRaic'+ raic.id" aria-expanded="true" :aria-controls="'collapseRaic'+ raic.id" v-text="raic.descripcionCorta"></a>
                                                                                    </div>
                                                                                    <div class="col">
                                                                                        <div class="row">
                                                                                            <span class="col-12 col-sm-12 col-md-2 pr-1"></span>
                                                                                            <div class="col-10 col-sm-11 col-md-6 pr-1">
                                                                                                <div class="progress bg-unfilled btn-pill">
                                                                                                    <div class="progress-bar" :class="{'bg-danger': raic.pctoConts <= 69, 'bg-warning': raic.pctoConts >= 70 && raic.pctoConts <= 89, 'bg-success': raic.pctoConts >= 90}" role="progressbar" :style="{width: raic.pctoConts +'%'}" :aria-valuenow="raic.pctoConts" aria-valuemin="0" aria-valuemax="100" v-text="raic.pctoConts +'%'"></div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <span class="col-2 col-sm-1 col-md-1 px-1" v-text="raic.nContsCump +'/'+ raic.nConts"></span>
                                                                                            <span class="col-12 col-sm-12 col-md-3" v-text="'Próximas a vencer: '+ raic.porVencer"></span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div :id="'collapseRaic'+ raic.id" class="collapse" role="tabpanel" :aria-labelledby="'headingRaic'+ raic.id">
                                                                                <div class="card-block">
                                                                                    <div class="el1-tabla table-responsive">
                                                                                        <table class="table table-bordered table-sm">
                                                                                            <template v-if="raic.contingencias.length == 0">
                                                                                                <tr>
                                                                                                    <th class="text-center">#</th>
                                                                                                    <td class="align-middle text-center" rowspan="5">No hay registros</td>
                                                                                                </tr>
                                                                                                <tr><th class="text-center">Contingencia</th></tr>
                                                                                                <tr><th class="text-center">Responsable</th></tr>
                                                                                                <tr><th class="text-center">Fecha límite</th></tr>
                                                                                                <tr><th class="text-center">Estado</th></tr>
                                                                                            </template>
                                                                                            <template v-else>        
                                                                                                <tr v-html="tmpContingenciasArr[raic.id].indices"></tr>
                                                                                                <tr v-html="tmpContingenciasArr[raic.id].contingencias"></tr>
                                                                                                <tr v-html="tmpContingenciasArr[raic.id].responsables"></tr>
                                                                                                <tr v-html="tmpContingenciasArr[raic.id].fechasLimite"></tr>
                                                                                                <tr v-html="tmpContingenciasArr[raic.id].estados"></tr>
                                                                                            </template>
                                                                                        </table>
                                                                                    </div>
                                                                                    <div class="el2-tabla table-responsive">
                                                                                        <table class="table table-bordered table-sm">
                                                                                            <thead>
                                                                                                <tr>
                                                                                                    <th class="text-center">#</th>
                                                                                                    <th class="text-center">Contingencia</th>
                                                                                                    <th class="text-center">Responsable</th>
                                                                                                    <th class="text-center">Fecha límite</th>
                                                                                                    <th class="text-center">Estado</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody>
                                                                                                <template v-if="raic.contingencias.length == 0">
                                                                                                    <tr>
                                                                                                        <td class="text-center" colspan="5">No hay registros</td>
                                                                                                    </tr>
                                                                                                </template>
                                                                                                <template v-else>
                                                                                                    <tr v-for="(contingencia, index) in raic.contingencias" :key="contingencia.id">
                                                                                                        <td class="text-center" v-text="index + 1"></td>
                                                                                                        <td v-text="contingencia.consecutivo"></td>
                                                                                                        <td v-text="contingencia.responsable.nombre_completo"></td>
                                                                                                        <td v-text="contingencia.fechaLimite"></td>
                                                                                                        <td class="text-center">
                                                                                                            <span class="badge" :class="'badge-' + (contingencia.condicion == 'Cerrado'? 'success' : contingencia.fechaLimite >= fechaActual ? 'warning': 'danger')" v-text="contingencia.condicion == 'Cerrado'? 'Cumplida' : contingencia.fechaLimite >= fechaActual ? 'Pendiente': 'Vencida'"></span>
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
                                                                </fieldset>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>
<script>
    export default {
        props : ['axiosApp', 'ruta', 'permisosUser']
        , data ()
        {
            return {
                cuadroMandoRuta : 'cuadro_mando'
                // Barra Actividades anual
                , actsAnual : 0
                , actsCumplidas : 0
                , actsPorcentajeAnual : 0
                // Barra Actividades A la fecha
                , actsHastaHoy : 0
                , actsHastaHoyCumplidas : 0
                , actsPorcentajeHastaHoy : 0

                , actividadFkIdPadre : 'fk_id_segmentos'
                , actividadNombrePadre : 'segmento'
                , segmentoArr : []
                , planAccionArr : []
                , proyectoArr : []
                , enfoqueArr : []
                , actividadArr : []
                , tmpActividadArr : []

                // Barra Mesa de ayuda
                , asigsAnual : 0
                , asigsAnualCump : 0
                , asigsAnualPorcentaje : 0

                , mdaProyectosArr : []
                , tmpAsignacionesArr : []

                , contsAnual : 0
                , contsAnualCump : 0
                , contsAnualPcto : 0
                
                , raicProyectosArr : []
                , tmpContingenciasArr : []
            };
        }
        , computed:
        {
            diaMes : function ()
            {
                return new Date().getDate();
            }
            , fechaActual : function ()
            {
                let fecha = new Date();
                return  fecha.getFullYear() +'-'+ (fecha.getMonth() < 9 ? '0':'') + (fecha.getMonth() + 1)  +'-'+  (fecha.getDate() < 10 ? '0':'') + fecha.getDate();
            }
        }
        , methods:
        {
            porcentajeObtener (cumplido, programado)
            {
                if (!programado) return 0;
                return (cumplido / programado * 100).toFixed(1);
            }
            , actividadCardDatos ()
            {
                let ruta = this.cuadroMandoRuta +'/actividades';
                this.peticionGet(ruta);
            }
            , actividadEnfoqueCambiar (recursoId)
            {
                if (recursoId == 'fk_id_segmentos') {
                    this.actividadNombrePadre = 'segmento';
                    this.enfoqueArr = [...this.segmentoArr];                
                } else if (recursoId == 'fk_id_planes_accion') {
                    this.actividadNombrePadre = 'planAccion';
                    this.enfoqueArr = [...this.planAccionArr];
                } else {
                    this.actividadNombrePadre = 'proyecto';
                    this.enfoqueArr = [...this.proyectoArr];
                }
                this.$refs.el2opciones.value = this.actividadFkIdPadre = recursoId;
                this.enfoqueArr.forEach((enfoque) => {
                    let enfoqueId = enfoque[this.actividadFkIdPadre], enfoqueIndice = 1;
                    this.tmpActividadArr[enfoqueId] = Object.assign(
                        {
                            indices:        '<th class="text-center">#</th>'
                            , actividades:  '<th class="align-middle text-center">Actividad</th>'
                            , responsables: '<th class="align-middle text-center">Responsables</th>'
                            , estados:      '<th class="text-center">Estado</th>'
                        }
                        , this.actividadFkIdPadre == 'fk_id_segmentos'? {} : {segmentos: '<th class="align-middle text-center">Segmento</th>'}
                        , this.actividadFkIdPadre == 'fk_id_planes_accion'? {} : {planesAccion: '<th class="align-middle text-center">Plan de acción</th>'}
                        , this.actividadFkIdPadre == 'fk_id_proyectos'? {} : {proyectos: '<th class="align-middle text-center">Proyecto</th>'}
                    );
                    enfoque.nActisCump = enfoque.nActis = enfoque.pctoActis = enfoque.porVencer = 0;
                    this.actividadArr.forEach((actividad) => {
                        if (actividad[this.actividadFkIdPadre] == enfoqueId) {
                            if (actividad.fechaReporte) {
                                if (actividad.diaMesLimite <= this.diaMes) {
                                    enfoque.nActisCump++;
                                    enfoque.nActis++;
                                }
                            } else {
                                if (actividad.diaMesLimite >= this.diaMes) {
                                    enfoque.porVencer++;
                                } else { // Incrementa las vencidas
                                    enfoque.nActis++;
                                }
                            }

                            this.tmpActividadArr[enfoqueId].indices += `<td class="text-center">${enfoqueIndice++}</td>`;
                            this.tmpActividadArr[enfoqueId].actividades += `<td>${actividad.actividad}</td>`;

                            if (this.tmpActividadArr[enfoqueId].hasOwnProperty('segmentos'))
                                this.tmpActividadArr[enfoqueId].segmentos += `<td>${actividad.segmento}</td>`;
                            if (this.tmpActividadArr[enfoqueId].hasOwnProperty('planesAccion'))
                                this.tmpActividadArr[enfoqueId].planesAccion += `<td>${actividad.planAccion}</td>`;
                            if (this.tmpActividadArr[enfoqueId].hasOwnProperty('proyectos'))
                                this.tmpActividadArr[enfoqueId].proyectos += `<td>${actividad.proyecto}</td>`;

                            this.tmpActividadArr[enfoqueId].responsables += '<td><ul class="list-group">';
                            actividad.responsables.forEach((responsable) => {
                                this.tmpActividadArr[enfoqueId].responsables += `<li class="list-group-item py-0 rayado">${responsable.nombre_completo}</li>`;
                            }, this);
                            this.tmpActividadArr[enfoqueId].responsables += '</ul></td>';

                            let color, mensaje;
                            if (actividad.fechaReporte) {
                                color = 'success';
                                mensaje = 'Cumplida';
                            } else if (actividad.diaMesLimite >= this.diaMes) {
                                color = 'warning';
                                mensaje = 'Pendiente';
                            } else {
                                color = 'danger';
                                mensaje = 'Vencida';
                            }
                            this.tmpActividadArr[enfoqueId].estados += `<td class="text-center"><span class="badge badge-${color}">${mensaje}</span></td>`;
                        }
                    }, this);
                    enfoque.pctoActis = this.porcentajeObtener(enfoque.nActisCump, enfoque.nActis);
                }, this);
            }
            , cargarTmpAsignaciones ()
            {
                this.mdaProyectosArr.forEach((mdaProyecto) => {
                    mdaProyecto.pendientes.forEach((pendiente) => {
                        let indice = 1;
                        this.tmpAsignacionesArr[pendiente.id] = {
                            indices : '<th class="text-center">#</th>'
                            , asignaciones : '<th class="align-middle text-center">Asignación</th>'
                            , responsables : '<th class="align-middle text-center">Responsable</th>'
                            , estados : '<th class="text-center">Estado</th>'
                        }
                        pendiente.asignaciones.forEach((asignacion) => {
                            this.tmpAsignacionesArr[pendiente.id].indices += `<td class="text-center">${indice++}</td>`;
                            this.tmpAsignacionesArr[pendiente.id].asignaciones += `<td>${asignacion.asignacion}</td>`;
                            this.tmpAsignacionesArr[pendiente.id].responsables += `<td>${asignacion.responsable.nombre_completo}</td>`;
                            this.tmpAsignacionesArr[pendiente.id].responsables += `<td>${asignacion.responsable.nombre_completo}</td>`;
                            let color, mensaje;
                            if (asignacion.condicion == 'Cerrado') {
                                color = 'success';
                                mensaje = 'Cumplida';
                            } else if (asignacion.fechaLimite >= this.fechaActual) {
                                color = 'warning';
                                mensaje = 'Pendiente';
                            } else {
                                color = 'danger';
                                mensaje = 'Vencida';
                            }
                            this.tmpAsignacionesArr[pendiente.id].estados += `<td class="text-center"><span class="badge badge-${color}">${mensaje}</span></td>`;
                        }, this);
                    }, this);
                }, this);
            }
            , cargarTmpContingencias ()
            {
                this.raicProyectosArr.forEach((raicProyecto) => {
                    raicProyecto.raics.forEach((raic) => {
                        let indice = 1;
                        this.tmpContingenciasArr[raic.id] = {
                            indices : '<th class="text-center">#</th>'
                            , consecutivos : '<th class="align-middle text-center">Consecutivo</th>'
                            , responsables : '<th class="align-middle text-center">Responsable</th>'
                            , fechasLimite : '<th class="align-middle text-center">Fecha límite</th>'
                            , estados : '<th class="text-center">Estado</th>'
                        };
                        raic.contingencias.forEach((contingencia) => {
                            this.tmpContingenciasArr[raic.id].indices += `<td class="text-center">${indice++}</td>`;
                            this.tmpContingenciasArr[raic.id].consecutivos += `<td>${contingencia.consecutivo}</td>`;
                            this.tmpContingenciasArr[raic.id].responsables += `<td>${contingencia.responsable.nombre_completo}</td>`;
                            this.tmpContingenciasArr[raic.id].fechasLimite += `<td class="text-center">${contingencia.fechaLimite.split('-').reverse().join('/')}</td>`;
                            let color, mensaje;
                            if (contingencia.condicion == 'Cerrado') {
                                color = 'success';
                                mensaje = 'Cumplida';
                            } else if (contingencia.fechaLimite >= this.fechaActual) {
                                color = 'warning';
                                mensaje = 'Pendiente';
                            } else {
                                color = 'danger';
                                mensaje = 'Vencida';
                            }
                            this.tmpContingenciasArr[raic.id].estados += `<td class="text-center"><span class="badge badge-${color}">${mensaje}</span></td>`;
                        }, this);
                    }, this);
                }, this);
            }
            , peticionGet (recursoRuta, cadCons = '')
            {
                let peticion = this.axiosApp.get(recursoRuta + cadCons);
                this.obtenerRespuesta(recursoRuta, peticion);
            }
            , obtenerRespuesta(recursoRuta, peticion)
            {
                let me = this;
                peticion.then(function (response) {
                    let respuesta = response.data;
                    switch (recursoRuta) {
                        case me.cuadroMandoRuta +'/actividades':
                            me.actsAnual = respuesta.actsAnual;
                            me.actsCumplidas = respuesta.actsCump;
                            me.actsPorcentajeAnual = me.porcentajeObtener(me.actsCumplidas, me.actsAnual);

                            me.actsHastaHoy = respuesta.actsHastaHoy;
                            me.actsHastaHoyCumplidas = respuesta.actsHastaHoyCump;
                            me.actsPorcentajeHastaHoy = me.porcentajeObtener(me.actsHastaHoyCumplidas, me.actsHastaHoy);

                            me.actividadArr = respuesta.actividades;
                            me.segmentoArr = respuesta.segmentos;
                            me.planAccionArr = respuesta.planesAccion;
                            me.proyectoArr = respuesta.proyectos;
                            me.actividadEnfoqueCambiar('fk_id_segmentos');

                            me.asigsAnualCump = respuesta.asigsAnualCump;
                            me.asigsAnual = respuesta.asigsAnual;
                            me.asigsAnualPorcentaje = me.porcentajeObtener(me.asigsAnualCump, me.asigsAnual);
                            me.mdaProyectosArr = respuesta.mdaProyectos;

                            me.mdaProyectosArr.forEach((mdaProyecto) => {
                                mdaProyecto.nAsigsCump = mdaProyecto.nAsigs = mdaProyecto.pctoAsigs = mdaProyecto.porVencer = 0;
                                mdaProyecto.pendientes.forEach((pendiente) => {
                                    mdaProyecto.nAsigsCump += pendiente.nAsigsCump = pendiente.asignaciones.filter(asignacion => asignacion.condicion == 'Cerrado').length;
                                    mdaProyecto.nAsigs += pendiente.nAsigs = pendiente.asignaciones.filter((asignacion) => {
                                        return asignacion.fechaLimite < me.fechaActual || asignacion.condicion == 'Cerrado';
                                    }).length;
                                    pendiente.pctoAsigs = me.porcentajeObtener(pendiente.nAsigsCump, pendiente.nAsigs);
                                    mdaProyecto.porVencer += pendiente.porVencer = pendiente.asignaciones.filter((asignacion) => {
                                        return asignacion.fechaLimite >= me.fechaActual && asignacion.condicion == 'Abierto';
                                    }).length;
                                });
                                mdaProyecto.pctoAsigs = me.porcentajeObtener(mdaProyecto.nAsigsCump, mdaProyecto.nAsigs);
                            });
                            me.cargarTmpAsignaciones();

                            me.contsAnual = respuesta.contsAnual;
                            me.contsAnualCump = respuesta.contsAnualCump;
                            me.contsAnualPcto = me.porcentajeObtener(me.contsAnualCump, me.contsAnual);
                            me.raicProyectosArr = respuesta.raicProyectos;

                            me.raicProyectosArr.forEach((raicProyecto) => {
                                raicProyecto.nContsCump = raicProyecto.nConts = raicProyecto.pctoConts = raicProyecto.porVencer = 0;
                                raicProyecto.raics.forEach((raic) => {
                                    raicProyecto.nContsCump += raic.nContsCump = raic.contingencias.filter((contingencia) => contingencia.condicion == 'Cerrado').length;
                                    raicProyecto.nConts += raic.nConts = raic.contingencias.filter((contingencia) => {
                                        return contingencia.fechaLimite < me.fechaActual || contingencia.condicion == 'Cerrado';
                                    }).length;
                                    raic.pctoConts = me.porcentajeObtener(raic.nContsCump, raic.nConts);
                                    raicProyecto.porVencer += raic.porVencer = raic.contingencias.filter((contingencia) => {
                                        return contingencia.fechaLimite >= me.fechaActual && contingencia.condicion == 'Abierto';
                                    }).length;
                                });
                                raicProyecto.pctoConts = me.porcentajeObtener(raicProyecto.nContsCump, raicProyecto.nConts);
                            });
                            me.cargarTmpContingencias();
                            break;
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
            }
        }
        , mounted ()
        {
            this.actividadCardDatos();
        }
    }
</script>
<style scoped>
    .no-mb {
        margin-bottom : 0rem !important;
    }
    .bg-unfilled {
        background-color: #e8e8e8;
    }
    .width-same {
        width: 8.2rem;
    }
    .h5, .h6 {
        margin-bottom: 0rem;
    }
    @media (max-width: 575px) {
        .el1-opciones{display: block;}
        .el2-opciones{display: none;}
        .legends-center {text-align: center !important;}
    }
    @media (min-width: 576px) {
        .el1-opciones{display: none;}
        .el2-opciones{display: block;}
        .legends-center {}
    }
    @media (max-width: 767px) {
        .el1-tabla {display: block;}
        .el2-tabla {display: none;}
    }
    @media (min-width: 768px) {
        .el1-tabla {display: none;}
        .el2-tabla {display: block;}
    }
    tr:nth-child(even) {
        background-color: #E4F8FF/*#DDF5FF*/;
    }
    th {
       background-color: #ffeed9/*#edecf2*/; 
    }
</style>