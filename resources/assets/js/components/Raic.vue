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
                            <i class="fa fa-ambulance fa-lg"></i>&nbsp;&nbsp;RAIC&nbsp;&nbsp;
                            <button type="button" @click="abrirModal('registrar')" class="btn btn-pill btn-primary btn-sm" v-if="permisosUser.crear">
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
                        <label class="col-md-auto pr-1 my-1">Descripción corta</label>
                        <div class="col-md-2 pl-1">
                            <input type="text" class="form-control" v-model="raicObjCons.descripcionCorta">
                        </div>
                        <label class="col-md-auto pr-1 my-1">Fecha reporte</label>
                        <div class="col-md-2 pl-1">
                            <input type="date" class="form-control" v-model="raicObjCons.fechaReporte">
                        </div>
                        <label class="col-md-auto pr-1 my-1">Proyecto</label>
                        <div class="col-md-3 pl-1">
                            <select class="form-control" v-model="raicObjCons.fkIdProyecto">
                                <option value="">Todos</option>
                                <option v-for="proyecto in proyectoArr" :key="proyecto.id" :value="proyecto.id" v-text="proyecto.proyecto"></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-auto pr-1 my-1">Reportado por</label>
                        <div class="col-md-3 pl-1">
                            <select class="form-control" v-model="raicObjCons.reportadoPor">
                                <option value="">Todos</option>
                                <option v-for="usuarioSel in usuariosSelArr" :key="usuarioSel.numId" :value="usuarioSel.numId" v-text="usuarioSel.nombre_completo"></option>
                            </select>
                        </div>
                        <label class="col-md-auto pr-1 my-1">Ver</label>
                        <div class="col-md-auto pl-1">
                            <select class="form-control" @change="cambiarPagina(1, raicObjCons, raicPagin, 1)" v-model="raicObjCons.numRegs">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <button type="button" @click="cambiarPagina(1, raicObjCons, raicPagin, 1)" class="btn btn-pill btn-primary btn-sm ml-2"><i class="fa fa-search"></i>&nbsp;Buscar</button>
                    </div>
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th class="align-middle text-center">#</th>
                                <th class="align-middle text-center">Descripción corta</th>
                                <th class="align-middle text-center">Fecha reporte</th>
                                <th class="align-middle text-center">Proyecto</th>
                                <th class="align-middle text-center">Fuente</th>
                                <th class="align-middle text-center">Fecha evento</th>
                                <th class="align-middle text-center">Reportado por</th>
                                <th class="align-middle text-center">Area de trabajo</th>
                                <th class="align-middle text-center">Supervisor</th>
                                <th class="align-middle text-center">Contingencias</th>
                                <th class="align-middle text-center">Condición</th>
                                <th class="align-middle text-center">Cierre</th>
                                <th class="align-middle text-center">Estado</th>
                                <th class="align-middle text-center">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-if="raicArr.length == 0">
                                <tr>
                                    <td class="text-center" colspan="14">No hay registros</td>
                                </tr>
                            </template>
                            <template v-else>
                                <tr v-for="(raic, index) in raicArr" :key="raic.id">
                                    <td v-text="index + 1"></td>
                                    <td v-text="raic.descripcionCorta"></td>
                                    <td class="text-center" v-text="raic.fechaReporte.split('-').reverse().join('/')"></td>
                                    <td v-text="raic.proyecto"></td>
                                    <td v-text="raic.raicFuente"></td>
                                    <td class="text-center" v-text="`${raic.fechaEvento.split(' ')[0].split('-').reverse().join('/')} ${raic.fechaEvento.slice(-8, -3)}`">
                                    </td>
                                    
                                    <td v-html="
                                        raic.fk_piRepPor_users ?
                                            `${raic.pi_rep_por.persona.num_documento} <br> ${raic.pi_rep_por.nombre_completo}` :
                                            `${raic.rpDocumento} <br> ${raic.rpNombresCompletos}`">
                                    </td>
                                    <td v-text="raic.areaTrabajo"></td>
                                    <td v-html="
                                        raic.fk_piSuperv_users ?
                                            `${raic.pi_superv.persona.num_documento} <br> ${raic.pi_superv.nombre_completo}` :
                                            `${raic.supDocumento} <br> ${raic.supNombresCompletos}`">
                                    </td>
                                    <td class="text-center">
                                        <template v-if="raic.haySeguimiento == 'Si'">
                                            <button type="button" @click="abrirModal(contingenciaRuta +'Listar', {raicId : raic.id, raicDescripcionCorta: raic.descripcionCorta, raicCondicion : raic.condicion, raicUsuCrea: raic.fk_usuCrea_users})" class="btn rounded btn-primary btn-sm" v-if="raic.estado">
                                                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                            </button>
                                        </template>
                                        <template v-else>NA</template>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge" :class="'badge-' + (raic.condicion == 'Abierto'? 'dark':'light')" v-text="raic.condicion"></span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" @click="raicEsCerrable(raic.condicion, 'este RAIC', raicRuta, raic.id, raic.haySeguimiento)" class="btn rounded btn-sm" :class="'btn-'+ (raic.condicion == 'Cerrado'? 'success':'danger')" :title="raic.condicion == 'Cerrado'? 'Abrir' : 'Cerrar'" v-if="(/*¿esSuper? || */id == raic.fk_usuCrea_users) && raic.condicion == 'Abierto' && raic.estado">
                                            <i :class="'icon-lock'+ (raic.condicion == 'Cerrado'? '' : '-open')" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" class="btn btn-secondary rounded btn-sm" v-else>
                                            <i :class="'icon-lock'+ (raic.condicion == 'Cerrado'? '' : '-open')" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                    <td class="text-center">                                        
                                        <span class="badge" :class="'badge-' + (raic.estado? 'success':'danger')" v-text="raic.estado? 'Activo':'Inactivo'"></span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" @click="abrirModal('actualizar', raic)" class="btn rounded btn-warning btn-sm" title="Editar" v-if="(/*¿esSuper? ||*/ id == raic.fk_usuCrea_users) && raic.condicion == 'Abierto' && raic.estado">
                                            <i class="icon-pencil"></i>
                                        </button>
                                        <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                            <i class="icon-pencil"></i>
                                        </button>
                                        <button type="button" @click="actualizarEstado(raic.estado, 'este RAIC', raicRuta, raic.id)" class="btn rounded btn-sm" :class="'btn-'+ (raic.estado?'danger': 'success')" :title="raic.estado?'Anular': 'Activar'" v-if="/*¿esSuper? ||*/ id == raic.fk_usuCrea_users && raic.condicion == 'Abierto'">
                                            <i :class="'icon-'+ (raic.estado?'trash': 'check')" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                            <i :class="'icon-'+ (raic.estado?'trash': 'check')" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <nav>
                        <ul class="pagination">
                            <li class="page-item" v-if="raicPagin.current_page > 1">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(raicPagin.current_page - 1, raicObjCons, raicPagin, 1)">Ant</a>
                            </li>
                            <li class="page-item" v-for="page in raicPagesNumber" :key="page" :class="[page == raicPagin.current_page ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(page, raicObjCons, raicPagin, 1)" v-text="page"></a>
                            </li>
                            <li class="page-item" v-if="raicPagin.current_page < raicPagin.last_page">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(raicPagin.current_page + 1, raicObjCons, raicPagin, 1)">Sig</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        <div id="raic-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div id="modalDialogRaic" class="modal-dialog modal-primary" style="max-width:1100px;" role="document">
                <div class="modal-content" style="min-height:43em;">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="'RAIC - '+ tituloModal"></h4>
                        <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div id="modalBodyRaic" class="modal-body">
                        <div class="row">
                            <ul class="list-group list-group-horizontal-md menu-h">
                                <li class="list-group-item">Cras justo odio</li>
                                <li class="list-group-item">Dapibus ac facilisis in</li>
                                <li class="list-group-item">Morbi leo risus</li>
                            </ul>
                        </div>
                        <div class="row">
                            <div class="col-3 menu-v">
                                <div class="list-group list-group-horizontal-sm" id="raic-tab" role="tablist">
                                    <a class="list-group-item list-group-item-action text-left active" id="implicados-list" data-toggle="list" href="#implicados" role="tab" aria-controls="home">
                                        Personal implicado
                                    </a>
                                    <a class="list-group-item list-group-item-action text-left" id="solicitud-list" data-toggle="list" href="#solicitud" role="tab" aria-controls="home">
                                        Información de la solicitud
                                    </a>
                                    <a class="list-group-item list-group-item-action text-left" id="detalles-list" data-toggle="list" href="#detalles" role="tab" aria-controls="profile">
                                        Detalles del caso
                                    </a>
                                    <a class="list-group-item list-group-item-action text-left" id="cbi-list" data-toggle="list" href="#cbi" role="tab" aria-controls="messages">
                                        Causas básicas e inmediatas
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-9 col-sm-12">
                                <div class="tab-content" id="nav-tabContent" style="height: 320px;overflow-y: auto;">
                                    <div class="tab-pane fade show active" id="implicados" role="tabpanel" aria-labelledby="implicados-list">
                                        <div class="form-row">
                                            <div class="col-md-4">
                                                <h5 class="text-primary font-italic">Reportado por</h5>
                                                <div class="form-row justify-content-center">
                                                    <div class="form-group col-md-12">
                                                        <label>Documento</label>
                                                        <div class="input-group">
                                                            <input v-if="raicBody.fk_piRepPor_users" type="text" class="form-control" v-model="piLbl.rpDocumento" readonly>
                                                            <input v-else type="text" class="form-control" :class="{'is-invalid' : noHayRPDocumento}" v-model="raicBody.rpDocumento">
                                                            <span class="input-group-btn btn-pi">
                                                                <button type="button" class="btn btn-outline-primary" :class="[raicBody.fk_piRepPor_users? '' : 'rounded-right']" @click="abrirModal('implicados', {tipoUsuario: 1})">
                                                                    <i class="fa fa-search" aria-hidden="true"></i>
                                                                </button>
                                                                <button v-if="raicBody.fk_piRepPor_users" type="button" class="btn btn-outline-danger rounded-right" @click="labelsLimpiar('RepPor', 'rp')">
                                                                    <i class="fa fa-user-times" aria-hidden="true"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-row justify-content-center">
                                                    <div class="form-group col-md-12">
                                                        <label>Nombres completos</label>
                                                        <input v-if="raicBody.fk_piRepPor_users" type="text" class="form-control" v-model="piLbl.rpNombresCompletos" readonly>
                                                        <input v-else type="text" class="form-control inputs-raic" :class="{'is-invalid' : noHayRPNombresCompletos}" v-model="raicBody.rpNombresCompletos">
                                                    </div>
                                                </div>
                                                <div class="form-row justify-content-center">
                                                    <div class="form-group col-md-12">
                                                        <label>Cargo</label>
                                                        <input v-if="raicBody.fk_piRepPor_users" type="text" class="form-control" v-model="piLbl.rpCargo" readonly>
                                                        <input v-else type="text" class="form-control inputs-raic" :class="{'is-invalid' : noHayRPCargo}" v-model="raicBody.rpCargo">
                                                    </div>
                                                </div>
                                                <div class="form-row justify-content-center">
                                                    <div class="form-group col-md-12">
                                                        <label>Área de trabajo</label>
                                                        <input type="text" class="form-control inputs-raic" :class="{'is-invalid' : noHayRPAreaTrabajo}" v-model="raicBody.rpAreaTrabajo">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <h5 class="text-primary font-italic">Jefe inmediato</h5>
                                                <div class="form-row justify-content-center">
                                                    <div class="form-group col-md-12">
                                                        <label>Documento</label>
                                                        <div class="input-group">
                                                            <input v-if="raicBody.fk_piJefInmed_users" type="text" class="form-control" v-model="piLbl.jiDocumento" readonly>
                                                            <input v-else type="text" class="form-control" :class="{'is-invalid' : noHayJIDocumento}" v-model="raicBody.jiDocumento">
                                                            <span class="input-group-btn btn-pi">
                                                                <button type="button" class="btn btn-outline-primary" :class="[raicBody.fk_piJefInmed_users? '' : 'rounded-right']" @click="abrirModal('implicados', {tipoUsuario: 2})">
                                                                    <i class="fa fa-search" aria-hidden="true"></i>
                                                                </button>
                                                                <button v-if="raicBody.fk_piJefInmed_users" type="button" class="btn btn-outline-danger rounded-right" @click="labelsLimpiar('JefInmed', 'ji')">
                                                                    <i class="fa fa-user-times" aria-hidden="true"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                                <div class="form-row justify-content-center">
                                                    <div class="form-group col-md-12">
                                                        <label>Nombres completos</label>
                                                        <input v-if="raicBody.fk_piJefInmed_users" type="text" class="form-control" v-model="piLbl.jiNombresCompletos" readonly>
                                                        <input v-else type="text" class="form-control inputs-raic" :class="{'is-invalid' : noHayJINombresCompletos}" v-model="raicBody.jiNombresCompletos">
                                                    </div>
                                                </div>
                                                <div class="form-row justify-content-center">
                                                    <div class="form-group col-md-12">
                                                        <label>Cargo</label>
                                                        <input v-if="raicBody.fk_piJefInmed_users" type="text" class="form-control" v-model="piLbl.jiCargo" readonly>
                                                        <input v-else type="text" class="form-control inputs-raic" :class="{'is-invalid' : noHayJICargo}" v-model="raicBody.jiCargo">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <h5 class="text-primary font-italic">Reportado al supervisor</h5>
                                                <div class="form-row justify-content-center">
                                                    <div class="form-group col-md-12">
                                                        <label>Documento</label>
                                                        <div class="input-group">
                                                            <input v-if="raicBody.fk_piSuperv_users" type="text" class="form-control" v-model="piLbl.supDocumento" readonly>
                                                            <input v-else type="text" class="form-control" :class="{'is-invalid' : noHaySupDocumento}" v-model="raicBody.supDocumento">
                                                            <span class="input-group-btn btn-pi">
                                                                <button type="button" class="btn btn-outline-primary" :class="[raicBody.fk_piSuperv_users? '' : 'rounded-right']" @click="abrirModal('implicados', {tipoUsuario: 3})">
                                                                    <i class="fa fa-search" aria-hidden="true"></i>
                                                                </button>
                                                                <button v-if="raicBody.fk_piSuperv_users" type="button" class="btn btn-outline-danger rounded-right" @click="labelsLimpiar('Superv', 'sup')">
                                                                    <i class="fa fa-user-times" aria-hidden="true"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        <label>Nombres completos</label>
                                                        <input v-if="raicBody.fk_piSuperv_users" type="text" class="form-control" v-model="piLbl.supNombresCompletos" readonly>
                                                        <input v-else type="text" class="form-control inputs-raic" :class="{'is-invalid' : noHaySupNombresCompletos}" v-model="raicBody.supNombresCompletos">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="solicitud" role="tabpanel" aria-labelledby="solicitud-list">
                                        <div class="form-row justify-content-start">
                                            <!--<div class="form-group col-md-auto">                                            
                                                <label>Tipo solicitud</label>
                                                <select class="form-control">
                                                    <option selected>Seleccione</option>
                                                    <option value="1">Tipo 1 SOF06V2</option>
                                                    <option value="2">Tipo 2 SO5HF06V1</option>
                                                </select>
                                            </div>-->
                                            <div class="form-group col-md-2">
                                                <label>Fecha reporte</label>
                                                <input type="date" class="form-control" :class="{'is-invalid' : noHayFechaReporte}" v-model="raicBody.fechaReporte">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label>Fuente reporte</label>
                                                <select class="form-control" :class="{'is-invalid' : noHayFuenteReporte}" v-model="raicBody.fk_id_raics_fuentes">
                                                    <option value="">Seleccione</option>
                                                    <option v-for="fuenteRep in fuentesRepsSelectArr" :key="fuenteRep.id" :value="fuenteRep.id" v-text="fuenteRep.raicFuente"></option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label>Tipo evento</label>
                                                <select class="form-control" :class="{'is-invalid' : noHayTipoEvento}" v-model="raicBody.tipoEvento">
                                                    <option value="">Seleccione</option>
                                                    <option value="1">Accidente</option>
                                                    <option value="2">Acto inseguro</option>
                                                    <option value="3">Casi accidente</option>
                                                    <option value="4">Condición insegura</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-row justify-content-start">
                                            <div class="form-group col-md-5">
                                                <label>Proyecto</label>
                                                <multiselect :class="{'is-danger' : noHayProyecto}" v-model="proyectoElegido" :options="proyectoArr" placeholder="Seleccione" label="proyecto" track-by="id" selectLabel="Presiona enter para marcar" selectedLabel="Marcado" deselectLabel="Presiona enter para desmarcar">
                                                    <span slot="noResult">Sin resultados</span>
                                                </multiselect>
                                            </div>
                                            <div class="form-group col-md-7">
                                                <label>Tipo de desviación de Protocolo TAR</label>
                                                <select class="form-control" :class="{'is-invalid' : noHayTipoDesviacion}" v-model="raicBody.fk_id_raics_tipos_desviaciones">
                                                    <option value="">Seleccione</option>
                                                    <option v-for="tipoDesvi in tiposDesviSelectArr" :key="tipoDesvi.id" :value="tipoDesvi.id" v-text="tipoDesvi.raicTipoDesviacion"></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="detalles" role="tabpanel" aria-labelledby="detalles-list">
                                        <div class="form-row justify-content-start">
                                            <div class="form-group col-md-3">
                                                <label>Área de trabajo</label>
                                                <input type="text" class="form-control" :class="{'is-invalid' : noHayAreaTrabajo}" v-model="raicBody.areaTrabajo">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label>Fecha del evento</label>
                                                <input type="date" class="form-control" :class="{'is-invalid' : noHayFechaEvt}" v-model="fechaEvt">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label>Hora del evento</label>
                                                <div class="input-group">
                                                    <span class="input-group-btn">
                                                        <select class="form-control" :class="{'is-invalid' : noHayHoraEvt}" v-model="horaEvt">
                                                            <option value="">HH</option>
                                                            <option v-for="hh in 24" :value="((hh - 1) < 10 ? '0' : '') + (hh - 1)" v-text="((hh - 1) < 10 ? '0' : '') + (hh - 1)"></option>
                                                        </select>
                                                    </span>
                                                    <span class="input-group-btn">
                                                        <select class="form-control" :class="{'is-invalid' : noHayMinutoEvt}" v-model="minutoEvt">
                                                            <option value="">MM</option>
                                                            <option v-for="mm in 60" :value="((mm - 1) < 10 ? '0' : '') + (mm - 1)" v-text="((mm - 1) < 10 ? '0' : '') + (mm - 1)"></option>
                                                        </select>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <label>Sitio específico</label>
                                                <input type="text" class="form-control" :class="{'is-invalid' : noHaySitioEspecifico}" v-model="raicBody.sitioEspecifico">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label>Prioridad</label>
                                                <button id="mtx" type="button" class="btn btn-pill btn-sm form-control" :class="[`btn-${raicColor}`, {'is-invalid' : noHayPrioridad}]" @click="abrirModal('matriz')" v-text="raicMensaje"></button>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-5">
                                                <label>Descripción corta del evento</label>
                                                <input type="text" class="form-control" :class="{'is-invalid' : noHayDescripcionCorta}" v-model="raicBody.descripcionCorta">
                                            </div>
                                            <div class="form-group col-md-7">
                                                <label>Descripción del evento</label>
                                                <textarea class="form-control" :class="{'is-invalid' : noHayDescripcionEvento}" rows="3" v-model="raicBody.descripcionEvento"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="cbi" role="tabpanel" aria-labelledby="cbi-list">
                                        <div id="accordion" role="tablist" aria-multiselectable="true">
                                            <div class="card cbi" v-for="(cbiNiv1, index) in cbiArr">
                                                <div class="card-header" role="tab" :id="'heading'+ index">
                                                    <h4 class="mb-0">
                                                        <a class="text-uppercase" style="text-decoration: none;" v-text="cbiNiv1.causa" data-toggle="collapse" data-parent="#accordion" :href="'#collapse'+ index" aria-expanded="true" :aria-controls="'collapse'+ index"></a>
                                                    </h4>
                                                </div>
                                                <div :id="'collapse'+ index" class="collapse" role="tabpanel" :aria-labelledby="'heading'+ index">
                                                    <div class="card-block">
                                                        <template v-for="cbiNiv2 in cbiNiv1.subcausas">
                                                            <p class="h5 text-uppercase" v-text="cbiNiv2.causa"></p>
                                                            <template v-for="cbiNiv3 in cbiNiv2.subcausas">
                                                                <p class="h6 pl-3 text-uppercase" v-text="cbiNiv3.causa"></p>
                                                                <div class="text-muted form-check pl-4" style="background-color: lightcyan;" v-for="cbiitem in cbiNiv3.items">
                                                                    <label class="form-check-label">
                                                                        <input class='form-check-input mr-2' type='checkbox' v-model='cbiitem.marcado'>{{cbiitem.item}}
                                                                    </label>
                                                                </div>
                                                            </template>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <label class="col-md-2 my-1 pr-1">Acción a seguir con el RAIC</label>
                        <div class="col-md-2 pl-1">
                            <select class="form-control" :class="{'is-invalid' : noHayRaicAccion}" v-model="raicBody.haySeguimiento">
                                <option value="Si">Hacer seguimiento</option>
                                <option value="No">Finalizar RAIC</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-pill btn-danger btn-sm" @click="cerrarModal()">Cerrar</button>
                        <button type="button" class="btn btn-pill btn-success btn-sm" @click="tipoAccion == 'Guardar' ? raicRegistrar() : raicActualizarAntes()" v-text="tipoAccion"></button>
                    </div>
                </div>
            </div>
        </div>
        <div id="implicados-mdl" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-primary" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel" v-text="'Buscar usuario - '+ piTituloMdl"></h4>
                        <button type="button" class="close" @click="implicadosCerrarMdl()" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <div class="col-md-4">
                                <input type="text" class="form-control" placeholder="Texto a buscar" @keyup.enter="usuariosListar()" v-model="perImpliFiltro">
                            </div>
                        </div>
                        <table class="table table-bordered table-hover table-striped table-sm">
                            <thead>
                                <th class="text-center">Documento</th>
                                <th class="text-center">Nombres completos</th>
                                <th class="text-center" v-if="noEsSupervisor">Cargo</th>
                            </thead>
                            <tbody>
                                <template v-if="usuariosArr.length == 0">
                                    <tr>
                                        <td class="text-center" :colspan="noEsSupervisor? 3 : 2">No hay registros</td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <template v-if="noEsSupervisor">
                                        <tr @click="labelsCargar(usuario)" v-for="usuario in usuariosArr" :key="usuario.id">
                                            <td class="text-center" v-text="usuario.num_documento"></td>
                                            <td class="text-center" v-text="usuario.nombre_completo"></td>
                                            <td class="text-center" v-text="usuario.cargo"></td>
                                        </tr>
                                    </template>
                                    <template v-else>
                                        <tr @click="labelsCargar(usuario)" v-for="usuario in usuariosArr" :key="usuario.id">
                                            <td class="text-center" v-text="usuario.num_documento"></td>
                                            <td class="text-center" v-text="usuario.nombre_completo"></td>
                                        </tr>
                                    </template>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-pill btn-primary btn-sm" @click="implicadosCerrarMdl()">Atrás</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="contingenciacrud-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-primary" style="max-width:1200px;" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="'RAIC - '+tituloModal"></h4>
                        <button type="button" class="close" @click="contingenciaMdlCerrar()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-auto">
                                        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>&nbsp;Contingencias&nbsp;
                                        <button type="button" @click="abrirModal(contingenciaRuta +'Registrar')" class="btn btn-pill btn-primary btn-sm" v-if="raicCondicion == 'Abierto' && id == raicUsuCrea">
                                            <i class="icon-plus"></i>&nbsp;Nuevo
                                        </button>
                                        <button type="button" class="btn btn-pill btn-secondary btn-sm" v-else>
                                            <i class="icon-plus"></i>&nbsp;Nuevo
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Consecutivo</th>
                                        <th class="text-center">Acción</th>
                                        <th class="text-center">Fecha límite</th>
                                        <th class="text-center">Responsable</th>
                                        <th class="text-center">Condición</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-center">Respuesta</th>
                                        <th class="text-center">Reabrir</th>
                                        <th class="text-center">Opciones</th>
                                        <th class="text-center">Al abrir</th>
                                        <th class="text-center">Al cerrar</th>
                                    </thead>
                                    <tbody>
                                        <template v-if="contingenciaArr.length == 0">
                                            <tr>
                                                <td class="text-center" colspan="12">No hay registros</td>
                                            </tr>
                                        </template>
                                        <template v-else>
                                            <tr v-for="(contingencia, index) in contingenciaArr" :key="contingencia.id">
                                                <td class="text-center" v-text="index + 1"></td>
                                                <td v-text="contingencia.consecutivo"></td>
                                                <td v-text="contingencia.accion"></td>
                                                <td class="text-center" v-text="contingencia.fechaLimite.split('-').reverse().join('/')"></td>
                                                <td v-text="contingencia.nombre_completo"></td>
                                                <td class="text-center">
                                                    <span class="badge" :class="'badge-'+ (contingencia.condicion == 'Abierto' ? 'dark' : 'light')" v-text="contingencia.condicion"></span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge" :class="'badge-' + (contingencia.estado? 'success':'danger')" v-text="contingencia.estado? 'Activo':'Inactivo'"></span>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" @click="abrirModal(evidenciaRuta +'Listar', {contingenciaId : contingencia.id, consecutivo: contingencia.consecutivo, condicion : contingencia.condicion, respId: contingencia.fk_respons_users, resp: contingencia.nombre_completo})" class="btn rounded btn-info btn-sm" v-if="contingencia.estado">
                                                        <i class="fa fa-files-o" aria-hidden="true"></i>
                                                    </button>
                                                    <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                        <i class="fa fa-files-o" aria-hidden="true"></i>
                                                    </button>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" @click="abrirModal(contingenciaRuta +'Condicion', {contingenciaId: contingencia.id, contingenciaConsecutivo: contingencia.consecutivo, contingenciaCondicion: contingencia.condicion})" class="btn btn-primary btn-sm rounded" v-if="raicCondicion == 'Abierto' && contingencia.condicion == 'Cerrado' && contingencia.estado && id == raicUsuCrea">
                                                        <i class="fa fa-unlock-alt" aria-hidden="true"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-secondary rounded btn-sm" v-else>
                                                        <i class="fa fa-unlock-alt" aria-hidden="true"></i>
                                                    </button>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" @click="abrirModal(contingenciaRuta +'Actualizar', contingencia)" class="btn rounded btn-warning btn-sm" title="Editar" v-if="contingencia.condicion == 'Abierto' && contingencia.estado && id == raicUsuCrea">
                                                        <i class="icon-pencil" aria-hidden="true"></i>
                                                    </button>
                                                    <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                        <i class="icon-pencil" aria-hidden="true"></i>
                                                    </button>
                                                    <button type="button" @click="actualizarEstado(contingencia.estado, 'esta contingencia', contingenciaRuta, contingencia.id)" class="btn rounded btn-sm" :class="'btn-'+ (contingencia.estado? 'danger':'success')" :title="contingencia.estado? 'Anular': 'Activar'" v-if="id == raicUsuCrea && contingencia.condicion == 'Abierto'">
                                                        <i :class="'icon-'+ (contingencia.estado? 'trash':'check')" aria-hidden="true"></i>
                                                    </button>
                                                    <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                        <i :class="'icon-' +(contingencia.estado? 'trash':'check')"></i>
                                                    </button>
                                                </td>
                                                <td v-html="contingencia.observacionAlAbrir"></td>
                                                <td v-html="contingencia.observacionAlCerrar"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-pill btn-primary btn-sm" @click="contingenciaMdlCerrar()">Atrás</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="contingencia-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-primary" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="contingenciaTituloMdl +' contingencia'"></h4>
                        <button type="button" class="close" @click="contingenciaMdlMdlCerrar()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group row justify-content-center">
                                <label class="col-md-3 pr-1">Consecutivo</label>
                                <div class="col-md-6 pl-1">
                                    <input type="text" class="form-control" :class="{'is-invalid' : noHayConsecutivo}" v-model="contingenciaBody.consecutivo">
                                </div>
                            </div>
                            <div class="form-group row justify-content-center">
                                <label class="col-md-3 pr-1">Acción</label>
                                <div class="col-md-6 pl-1">
                                    <input type="text" class="form-control" :class="{'is-invalid' : noHayAccion}" v-model="contingenciaBody.accion">
                                </div>
                            </div>
                            <div class="form-group row justify-content-center">
                                <label class="col-md-3 pr-1">Fecha límite</label>
                                <div class="col-md-6 pl-1">
                                    <input type="date" class="form-control" :class="{'is-invalid' : noHayFechaLimite}" v-model="contingenciaBody.fechaLimite">
                                </div>
                            </div>
                            <div class="form-group row justify-content-center">
                                <label class="col-md-3 pr-1">Responsable</label>
                                <div class="col-md-6 pl-1">
                                    <multiselect :class="{'is-danger' : noHayResponsable}" v-model="contingenciaResponsElegido" :options="responsablesArr" placeholder="Seleccione" label="nombre_completo" track-by="id" selectLabel="Presiona enter para marcar" selectedLabel="Marcado" deselectLabel="Presiona enter para desmarcar">
                                        <span slot="noResult">Sin resultados</span>
                                    </multiselect>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-pill btn-danger btn-sm" @click="contingenciaMdlMdlCerrar()">Cerrar</button>
                        <button type="button" class="btn btn-pill btn-success btn-sm" @click="tipoAccion == 'Guardar' ? contingenciaRegistrar() : contingenciaActualizar()" v-text="tipoAccion"></button>
                    </div>
                </div>
            </div>
        </div>
        <div id="contingenciacond-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-primary" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="'Contingencia - '+ contingenciaTituloMdl"></h4>
                        <button type="button" class="close" @click="contingenciaCondMdlCerrar()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-row justify-content-center">
                                <div class="form-group col-11">
                                    <label for="contingenciaBObs">Observación</label>
                                    <textarea id="contingenciaBObs" ref="contingenciaBObservacion" class="form-control" :class="{'is-invalid': noHayObservacion}" rows="4" v-model="contingenciaObservacion"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-pill btn-sm" @click="contingenciaCondMdlCerrar()">Cancelar</button>
                        <button type="button" class="btn btn-success btn-pill btn-sm" @click="contingenciaCondActualizar()">Reabrir</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="evidenciacrud-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-primary" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="'Contingencia - '+ contingenciaTituloMdl"></h4>
                        <button type="button" class="close" @click="evidenciaMdlCerrar()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row justify-content-center">
                            <div class="col-md-8 mb-3">
                                <label>Observación</label>
                                <textarea class="form-control" :class="{'is-invalid': noHayObservacion}" rows="2" v-model="contingenciaObservacion"></textarea>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-11">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-md-auto">
                                                <i class="fa fa-files-o" aria-hidden="true"></i>&nbsp;Evidencias&nbsp;
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
                                            <button type="button" id="evidenciaguardar" class="btn btn-pill btn-info btn-sm" @click="evidenciaRegistrar();$refs.evidenciaBEvidencia.focus();" v-if="contingenciaCondicion == 'Abierto' && id == contingenciaRespId">
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
                                                <th class="text-center">Opciones</th>
                                            </thead>
                                            <tbody>
                                                <template v-if="evidenciaArr.length == 0">
                                                    <tr>
                                                        <td class="text-center" colspan="3">No hay registros</td>
                                                    </tr>
                                                </template>
                                                <template v-else>
                                                    <tr v-for="(evidencia, index) in evidenciaArr" :key="evidencia.id">
                                                        <td class="text-center" v-text="index + 1"></td>
                                                        <td v-text="evidencia.evidencia"></td>
                                                        <td class="text-center">
                                                            <a type="button" title="Visualizar" :href="evidencia.ruta" target="_blank" class="btn rounded btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                                            <button type="button" class="btn rounded btn-danger btn-sm" @click="descartar(index, evidencia.id)" v-if="contingenciaCondicion == 'Abierto' && id == contingenciaRespId">
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
                        <button type="button" class="btn btn-pill btn-success btn-sm" @click="contingenciaCondActualizar()" v-if="contingenciaCondicion == 'Abierto' && id == contingenciaRespId">
                            <i class="fa fa-lock" aria-hidden="true"></i>&nbsp;Guardar
                        </button>
                        <button type="button" class="btn btn-secondary btn-pill btn-sm" v-else>
                            <i class="fa fa-lock" aria-hidden="true"></i>&nbsp;Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!--<div id="evidencia-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-primary" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="evidenciaTituloMdl +' evidencia'"></h4>
                        <button type="button" class="close" @click="evidenciaMdlMdlCerrar()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group row justify-content-center">
                                <label class="col-md-3 my-1 pr-1">Evidencia</label>
                                <div class="col-md-8 pl-1">
                                    <input type="text" class="form-control" :class="{'is-invalid': noHayEvidencia}" v-model="evidenciaBody.evidencia">
                                </div>
                            </div>
                            <div class="form-group row justify-content-center">
                                <label class="col-md-3 my-1 pr-1">Subir evidencia</label>
                                <div class="col-md-8 pl-1">
                                    <input type="file" id="archivoEvidencia" class="form-control form-control-file" :class="{'is-invalid': noHayRuta}" @change="obtenerArchivo">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-pill btn-danger btn-sm" @click="evidenciaMdlMdlCerrar()">Cerrar</button>
                        <button type="button" class="btn btn-pill btn-success btn-sm" @click="tipoAccion == 'Guardar' ? evidenciaRegistrar() : evidenciaActualizar()" v-text="tipoAccion"></button>
                    </div>
                </div>
            </div>
        </div>-->
    </main>
</template>

<script>
    import Multiselect from 'vue-multiselect'
    export default {
        props : ['permisosUser', 'ruta', 'axiosApp']
        , components : { Multiselect }
        , data () {
            return {
                proyectoRuta : 'conf_proyectos'
                , id : 0
                , piTituloMdl : ''
                , perImpliFiltro : ''
                , usuariosSelArr : []
                , usuariosArr : []
                , fuentesRepsSelectArr : []
                , proyectoArr : []
                , proyectoElegido : null
                , tiposDesviSelectArr : []
                , cbiArr : []

                , piLbl : {
                    rpDocumento : ''
                    , rpNombresCompletos : ''
                    , rpCargo : ''
                    , jiDocumento : ''
                    , jiNombresCompletos : ''
                    , jiCargo : ''
                    , supDocumento : ''
                    , supNombresCompletos : ''
                }

                , raicObjCons : {
                    '?page' : 1
                    , descripcionCorta : ''
                    , fechaReporte : ''
                    , fkIdProyecto : ''
                    , reportadoPor : ''
                    , numRegs : 10
                }
                , raicArr : []
                , raicId : ''
                , fechaEvt : ''
                , horaEvt : ''
                , minutoEvt : ''
                , raicBody : {
                    fk_piRepPor_users : ''
                    , rpDocumento : ''
                    , rpNombresCompletos : ''
                    , rpCargo : ''
                    , rpAreaTrabajo : ''
                    , fk_piJefInmed_users : ''
                    , jiDocumento : ''
                    , jiNombresCompletos : ''
                    , jiCargo : ''
                    , fk_piSuperv_users : ''
                    , supDocumento : ''
                    , supNombresCompletos : ''

                    //, tipoSolicitud : '' NO DEBE ESTAR PRESENTE
                    , fk_id_raics_fuentes : ''
                    , fk_id_proyectos : ''
                    , tipoEvento : ''
                    , fechaReporte : ''
                    , fk_id_raics_tipos_desviaciones : ''

                    , areaTrabajo : ''
                    , fechaEvento : ''
                    , sitioEspecifico : ''
                    , prioridad : ''
                    , descripcionCorta : ''
                    , descripcionEvento : ''
                    , haySeguimiento : 'Si'
                }
                , raicCondicion : ''
                , raicUsuCrea : 0
                , tmpEsCerrable : []
                , items : []

                , raicPagin : {
                    total : 0
                    , current_page : 0
                    , per_page : 0
                    , last_page : 0
                    , from : 0
                    , to : 0
                }
                , offset : 3

                , raicOnVacios : false
                , raicRuta : 'raics'

                , raicMensaje : 'Sin asignar'
                , raicColor : 'info'
                , raicInputBtns : '<input type="radio" name="options" autocomplete="off">'
                , matrizRaic : ''

                , tituloModal : 'Registrar'

                , tipoAccion : 'Guardar'

                , responsablesArr : []
                , contingenciaResponsElegido : null
                
                , contingenciaRuta : 'raics_contingencias'
                , contingenciaArr : []
                , contingenciaId : ''
                , contingenciaBody : {
                    consecutivo : ''
                    , accion : ''
                    , fechaLimite : ''
                    , fk_respons_users : ''
                    , fk_id_raics : ''
                }
                , contingenciaRespId : 0
                , contingenciaResp : ''
                , contingenciaObservacion : ''
                , contingenciaCondicion : false
                , contingenciaOnVacios : false
                , contingenciaTituloMdl : 'Registrar'
                
                , evidenciaRuta : 'raics_evidencias'
                , evidenciaId : ''
                , evidenciaArr : []
                , evidenciaBody : {
                    evidencia : ''
                    , ruta : ''
                    , fk_id_raics_contingencias : ''
                }
                , evidenciaOnVacios : false
                , evidenciaTituloMdl : 'Registrar'
                , recursoIdx : 0

                , haySeguimientoOld : 'Si'
                , actualizarDesde : ''
            }
        }
        , computed:
        {
            raicPagesNumber: function ()
            {
                if (!this.raicPagin.to) {
                    return [];
                }
                let from = this.raicPagin.current_page - this.offset; 
                if (from < 1) {
                    from = 1;
                }
                let to = from + (this.offset * 2);
                if (to >= this.raicPagin.last_page) {
                    to = this.raicPagin.last_page;
                }
                let pagesArray = [];
                while (from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;
            }
            , noEsSupervisor : function ()
            {
                return this.piTituloMdl != 'Supervisor';
            }
            // VALIDACIÓN DE CAMPOS VACIOS EN FORMULARIOS
            // Campos raic
            , noHayRPDocumento ()
            {
                return this.raicOnVacios && !this.raicBody.rpDocumento.trim() && !this.raicBody.fk_piRepPor_users;
            }
            , noHayRPNombresCompletos ()
            {
                return this.raicOnVacios && !this.raicBody.rpNombresCompletos.trim() && !this.raicBody.fk_piRepPor_users;
            }
            , noHayRPCargo ()
            {
                return this.raicOnVacios && !this.raicBody.rpCargo.trim() && !this.raicBody.fk_piRepPor_users;
            }
            , noHayRPAreaTrabajo ()
            {
                return this.raicOnVacios && !this.raicBody.rpAreaTrabajo.trim();
            }
            , noHayJIDocumento ()
            {
                return this.raicOnVacios && !this.raicBody.jiDocumento.trim() && !this.raicBody.fk_piJefInmed_users;
            }
            , noHayJINombresCompletos ()
            {
                return this.raicOnVacios && !this.raicBody.jiNombresCompletos.trim() && !this.raicBody.fk_piJefInmed_users;
            }
            , noHayJICargo ()
            {
                return this.raicOnVacios && !this.raicBody.jiCargo.trim() && !this.raicBody.fk_piJefInmed_users;
            }
            , noHaySupDocumento ()
            {
                return this.raicOnVacios && !this.raicBody.supDocumento.trim() && !this.raicBody.fk_piSuperv_users;
            }
            , noHaySupNombresCompletos ()
            {
                return this.raicOnVacios && !this.raicBody.supNombresCompletos.trim() && !this.raicBody.fk_piSuperv_users;
            }
            , noHayFechaReporte ()
            {
                return this.raicOnVacios && !this.raicBody.fechaReporte;
            }
            , noHayFuenteReporte ()
            {
                return this.raicOnVacios && !this.raicBody.fk_id_raics_fuentes;
            }
            , noHayTipoEvento ()
            {
                return this.raicOnVacios && !this.raicBody.tipoEvento;
            }
            , noHayProyecto ()
            {
                return this.raicOnVacios && this.proyectoElegido === null;
            }
            , noHayTipoDesviacion ()
            {
                return this.raicOnVacios && !this.raicBody.fk_id_raics_tipos_desviaciones;
            }
            , noHayAreaTrabajo ()
            {
                return this.raicOnVacios && !this.raicBody.areaTrabajo.trim();
            }
            , noHayFechaEvt ()
            {
                return this.raicOnVacios && !this.fechaEvt;
            }
            , noHayHoraEvt ()
            {
                return this.raicOnVacios && !this.horaEvt;
            }
            , noHayMinutoEvt ()
            {
                return this.raicOnVacios && !this.minutoEvt;
            }
            , noHaySitioEspecifico ()
            {
                return this.raicOnVacios && !this.raicBody.sitioEspecifico.trim();
            }
            , noHayPrioridad ()
            {
                return this.raicOnVacios && !this.raicBody.prioridad;
            }
            , noHayDescripcionCorta ()
            {
                return this.raicOnVacios && !this.raicBody.descripcionCorta.trim();
            }
            , noHayDescripcionEvento ()
            {
                return this.raicOnVacios && !this.raicBody.descripcionEvento.trim();
            }
            , noHayRaicAccion ()
            {
                return this.raicOnVacios && !this.raicBody.haySeguimiento;
            }
            , noHayCbiMarcadas ()
            {
                return this.raicOnVacios && !this.items.length;
            }
            // Campos contingencia
            , noHayConsecutivo ()
            {
                return this.contingenciaOnVacios && !this.contingenciaBody.consecutivo.trim();
            }
            , noHayAccion ()
            {
                return this.contingenciaOnVacios && !this.contingenciaBody.accion.trim();
            }
            , noHayFechaLimite ()
            {
                return this.contingenciaOnVacios && !this.contingenciaBody.fechaLimite;
            }
            , noHayResponsable ()
            {
                return this.contingenciaOnVacios && !this.contingenciaResponsElegido;
            }
            , noHayObservacion ()
            {
                return this.contingenciaOnVacios && !this.contingenciaObservacion.trim();
            }
            // Campos evidencia
            , noHayEvidencia ()
            {
                return this.evidenciaOnVacios && !this.evidenciaBody.evidencia.trim();
            }
            , noHayRuta ()
            {
                return this.evidenciaOnVacios && !this.evidenciaBody.ruta;
            }
        }
        , methods :
        {
            abrirModal (accion, data = {})
            {
                switch (accion) {
                    case 'registrar':
                        //Debe reasignarse prioridad elegida, mensaje y color aquí porque el sweet alert puede quedar abierto cuando se cierra el modal
                        this.raicBody.prioridad = '';
                        this.raicMensaje = 'Sin asignar';
                        this.raicColor = 'info';
                        $('#raic-mdl').modal('show');
                        break;
                    case 'implicados':
                        $('#raic-mdl').modal('hide');
                        this.piTituloMdl = data.tipoUsuario == 1? 'Reportado por' : (data.tipoUsuario == 2 ? 'Jefe inmediato' : 'Supervisor');
                        this.usuariosListar();
                        $('#implicados-mdl').modal('show');
                        break;
                    case 'actualizar':
                        this.tituloModal = 'Editar';

                        this.raicId = data.id;

                        this.raicBody.fk_piRepPor_users = data.fk_piRepPor_users;
                        this.raicBody.fk_piJefInmed_users = data.fk_piJefInmed_users;
                        this.raicBody.fk_piSuperv_users = data.fk_piSuperv_users;
                        let persImplic = data.fk_piRepPor_users || data.fk_piJefInmed_users || data.fk_piSuperv_users;
                        if (!data.fk_piRepPor_users) {
                            this.raicBody.rpDocumento = data.rpDocumento;
                            this.raicBody.rpNombresCompletos = data.rpNombresCompletos;
                            this.raicBody.rpCargo = data.rpCargo;
                        }
                        this.raicBody.rpAreaTrabajo = data.rpAreaTrabajo;
                        if (!data.fk_piJefInmed_users) {
                            this.raicBody.jiDocumento = data.jiDocumento;
                            this.raicBody.jiNombresCompletos = data.jiNombresCompletos;
                            this.raicBody.jiCargo = data.jiCargo;
                        }
                        if (!data.fk_piSuperv_users) {
                            this.raicBody.supDocumento = data.supDocumento;
                            this.raicBody.supNombresCompletos = data.supNombresCompletos;
                        }
                        if (persImplic) this.raicImplicados();
                        
                        this.raicBody.fk_id_raics_fuentes = data.fk_id_raics_fuentes;
                        this.proyectoElegido = { id : data.fk_id_proyectos, proyecto : data.proyecto };
                        this.raicBody.tipoEvento = data.tipoEvento;
                        this.raicBody.fechaReporte = data.fechaReporte;
                        this.raicBody.fk_id_raics_tipos_desviaciones = data.fk_id_raics_tipos_desviaciones;
                        
                        this.raicBody.areaTrabajo = data.areaTrabajo;
                        this.fechaEvt = data.fechaEvento.split(' ')[0];
                        this.horaEvt = data.fechaEvento.split(' ')[1].split(':')[0];
                        this.minutoEvt = data.fechaEvento.split(' ')[1].split(':')[1];
                        this.raicBody.sitioEspecifico = data.sitioEspecifico;
                        this.raicBody.prioridad = data.prioridad;
                        this.colorYMensaje();
                        this.raicBody.descripcionCorta = data.descripcionCorta;
                        this.raicBody.descripcionEvento = data.descripcionEvento;

                        this.raicCbi();

                        this.haySeguimientoOld = this.raicBody.haySeguimiento = data.haySeguimiento;
                        
                        this.tipoAccion = 'Actualizar';

                        $('#raic-mdl').modal('show');
                        break;
                    case 'matriz':
                        this.matrizRaic.fire({
                            onOpen : () => {
                                const content = Swal.getContent();
                                const $ = content.querySelector.bind(content);
                                let cp = this.raicBody.prioridad;
                                if (cp) {
                                    $('#cp_' + cp).click();
                                    // IMITA EL TEXTO CSS DEL BOTON WARNING ORIGINAL
                                    if (cp == '13' || cp == '22' || cp == '31') $('#cp_' + cp).style.color = '#212529';
                                }
                            }
                            , onAfterClose : () => $('#mtx').blur()
                        }).then((result) => {
                            if (result.value) {
                                this.colorYMensaje();
                            }
                        });
                        break;
                    case this.contingenciaRuta +'Listar':
                        this.tituloModal = data.raicDescripcionCorta;
                        this.contingenciaBody.fk_id_raics = this.raicId = data.raicId;
                        this.raicCondicion = data.raicCondicion;
                        this.raicUsuCrea = data.raicUsuCrea;
                        this.contingenciaListar();
                        $('#contingenciacrud-mdl').modal('show');
                        break;
                    case this.contingenciaRuta +'Registrar':
                        $('#contingenciacrud-mdl').modal('hide');
                        $('#contingencia-mdl').modal('show');
                        break;
                    case this.contingenciaRuta +'Actualizar':
                        $('#contingenciacrud-mdl').modal('hide');
                        this.contingenciaTituloMdl = 'Editar';
                        this.contingenciaId = data.id;
                        this.contingenciaBody.consecutivo = data.consecutivo;
                        this.contingenciaBody.accion = data.accion;
                        this.contingenciaBody.fechaLimite = data.fechaLimite;
                        this.contingenciaResponsElegido = {id : data.fk_respons_users, nombre_completo : data.nombre_completo};
                        this.tipoAccion = 'Actualizar';
                        $('#contingencia-mdl').modal('show');
                        break;
                    case this.contingenciaRuta +'Condicion':
                        this.contingenciaTituloMdl = data.contingenciaConsecutivo;
                        this.contingenciaId = data.contingenciaId;
                        this.contingenciaCondicion = data.contingenciaCondicion;
                        $('#contingenciacrud-mdl').modal('hide');
                        $('#contingenciacond-mdl').modal('show');
                        break;
                    case this.evidenciaRuta +'Listar':
                        $('#contingenciacrud-mdl').modal('hide');
                        this.contingenciaTituloMdl = data.consecutivo;
                        this.evidenciaBody.fk_id_raics_contingencias = this.contingenciaId = data.contingenciaId;
                        this.contingenciaRespId = data.respId;
                        this.contingenciaResp = data.resp;
                        this.contingenciaCondicion = data.condicion;
                        this.evidenciaListar();
                        $('#evidenciacrud-mdl').modal('show');
                        break;
                    /*case this.evidenciaRuta +'Registrar':
                        $('#evidenciacrud-mdl').modal('hide');
                        $('#evidencia-mdl').modal('show');
                        break;
                    case this.evidenciaRuta +'Actualizar':
                        $('#evidenciacrud-mdl').modal('hide');
                        this.evidenciaTituloMdl = 'Editar';
                        this.evidenciaId = data.id;
                        this.evidenciaBody.evidencia = data.evidencia;
                        this.evidenciaBody.ruta = data.ruta;
                        this.tipoAccion = 'Actualizar';
                        $('#evidencia-mdl').modal('show');
                        break;*/
                }
            }
            , usuIdObtener ()
            {
                let ruta = this.raicRuta +'/logid';
                this.peticionGet(ruta);
            }
            , colorYMensaje ()
            {
                switch (this.raicBody.prioridad) {
                    case '11':
                        this.raicMensaje = 'Baja';
                        this.raicColor = 'success';
                        break;
                    case '12':case '21':
                        this.raicMensaje = 'Media';
                        this.raicColor = 'success';
                        break;
                    case '13':case '22':case '31':
                        this.raicMensaje = 'Alta';
                        this.raicColor = 'warning';
                        break;
                    case '14':case '15':case '23':case '24':case '25':case '32':case '33':case '34':case '41':case '42':case '43':case '51':case '52':
                        this.raicMensaje = 'Urgente';
                        this.raicColor = 'danger';
                        break;
                    case '35':case '44':case '45':case '53':case '54':case '55':
                        this.raicMensaje = 'Urgente';
                        this.raicColor = 'dark';
                        break;
                }
            }
            , cerrarModal ()
            {
                this.tituloModal = 'Registrar';
                $('#raic-tab a:first-child').tab('show');
                $('#collapse0').collapse();
                this.raicId = 0;
                // Limpieza campos raicBody
                this.raicBody.fk_piRepPor_users = this.raicBody.rpDocumento = this.raicBody.rpNombresCompletos = this.raicBody.rpCargo = this.raicBody.rpAreaTrabajo = this.raicBody.fk_piJefInmed_users = this.raicBody.jiDocumento = this.raicBody.jiNombresCompletos = this.raicBody.jiCargo = this.raicBody.fk_piSuperv_users = this.raicBody.supDocumento = this.raicBody.supNombresCompletos = this.raicBody.fk_id_raics_fuentes = this.raicBody.fk_id_proyectos = this.raicBody.tipoEvento = this.raicBody.fechaReporte = this.raicBody.fk_id_raics_tipos_desviaciones = this.raicBody.areaTrabajo = this.raicBody.fechaEvento = this.raicBody.sitioEspecifico = this.raicBody.descripcionEvento = '';
                this.raicBody.haySeguimiento = this.haySeguimientoOld = 'Si';
                // Limpieza campos que suministran datos a algunos atributos de raicBody
                this.proyectoElegido = null;
                this.fechaEvt = this.horaEvt = this.minutoEvt = '';
                // Desmarcado de checks para causas básicas e inmediatas
                this.cbiDesmarcar();
                this.items = [];
                this.tipoAccion = 'Guardar';
                this.raicOnVacios = false;
                $('#raic-mdl').modal('hide');
            }
            , implicadosCerrarMdl ()
            {
                $('#implicados-mdl').modal('hide');
                this.piTituloMdl = this.perImpliFiltro = '';
                $('#raic-mdl').modal('show');
            }
            , contingenciaMdlCerrar ()
            {
                $('#contingenciacrud-mdl').modal('hide');
                this.tituloModal = 'Registrar';
                this.raicCondicion = this.contingenciaBody.fk_id_raics = this.raicId = '';
                this.raicUsuCrea = 0;
                this.contingenciaArr = [];
            }
            , contingenciaMdlMdlCerrar ()
            {
                $('#contingencia-mdl').modal('hide');
                $('#contingenciacrud-mdl').modal('show');
                this.contingenciaTituloMdl = 'Registrar';
                this.contingenciaId = this.contingenciaBody.consecutivo = this.contingenciaBody.accion = this.contingenciaBody.fechaLimite = this.contingenciaBody.fk_respons_users = this.contingenciaBody.fk_id_raics = '';
                this.tipoAccion = 'Guardar';
                this.contingenciaOnVacios = false;
            }
            , raicEsCerrable (recursoCondicion, finalTitle, recursoRuta, recursoId, recursoSigue)
            {
                this.tmpEsCerrable = [recursoCondicion, finalTitle, recursoRuta, recursoId];
                let ruta = this.raicRuta +'/hay_contingencias/';
                this.actualizarDesde = 'botonCierre';
                this.peticionGet(ruta, recursoId +'');
            }
            , actualizarCondicion (recursoCondicion, finalTitle, recursoRuta, recursoId)
            {
                let accion = 'abrir', textoAviso = '', recurso = finalTitle.endsWith('RAIC');
                if (recursoCondicion == 'Abierto') {
                    accion = 'cerrar';
                    textoAviso = `No podrás gestionar más ${recurso? 'contingencias' : 'evidencias'} para ${finalTitle}`;
                }
                Swal.fire({
                    title: `¿Estas seguro de ${accion} ${finalTitle}?`
                    , text: textoAviso
                    , type: 'warning'
                    , showCancelButton: true
                    , confirmButtonText: 'Aceptar!'
                    , cancelButtonText: 'Cancelar'
                    , confirmButtonClass: 'btn btn-lg btn-pill btn-success'
                    , cancelButtonClass: 'btn btn-lg btn-pill btn-danger'
                    , buttonsStyling: false
                    , reverseButtons: true
                    }).then((result) => {
                    if (result.value) {
                        let ruta = `${recursoRuta}/${accion}/${recursoId}`, textoResp = recurso? 'El raic': 'La contingencia', me = this;
                        axios.patch(ruta).then(response => {
                            Swal.fire(accion == 'cerrar'? 'Cerrado' : 'Abierto', `${textoResp} se ha ${accion == 'cerrar'? 'cerrado' : 'abierto'} con éxito`, 'success');
                            if (recurso) {
                                me.raicListar();
                            } else {
                                me.contingenciaListar();
                            }
                        })
                        .catch(error => console.log(error));
                    } else if (result.dismiss === Swal.DismissReason.cancel /* Read more about handling dismissals */) {}
                });
            }
            , contingenciaCondMdlCerrar ()
            {
                $('#contingenciacond-mdl').modal('hide');
                this.contingenciaTituloMdl = 'Registrar';
                this.contingenciaId = this.contingenciaObservacion = this.contingenciaCondicion = '';
                this.contingenciaOnVacios = false;
                $('#contingenciacrud-mdl').modal('show');
            }
            , evidenciaMdlCerrar ()
            {
                $('#evidenciacrud-mdl').modal('hide');
                this.contingenciaTituloMdl = 'Registrar';
                this.contingenciaId = this.contingenciaRespId = this.contingenciaResp = this.contingenciaObservacion = this.contingenciaCondicion = this.evidenciaBody.evidencia = this.evidenciaBody.ruta = this.evidenciaBody.fk_id_raics_contingencias = '';
                this.$refs.inputRuta.value = '';
                this.contingenciaOnVacios = this.evidenciaOnVacios = false;
                this.evidenciaArr = [];
                $('#contingenciacrud-mdl').modal('show');
            }
            /*, evidenciaMdlMdlCerrar ()
            {
                $('#evidencia-mdl').modal('hide');
                this.evidenciaTituloMdl = 'Registrar';
                this.evidenciaId = this.evidenciaBody.evidencia = this.evidenciaBody.ruta = '';
                this.tipoAccion = 'Guardar';
                this.evidenciaOnVacios = false;
                $('#archivoEvidencia').val('');
                $('#evidenciacrud-mdl').modal('show');
            }*/
            , usuariosSel ()
            {
                let ruta = this.raicRuta +'/usuarios_filtro_sel';
                this.peticionGet(ruta);
            }
            , usuariosListar ()
            {
                let ruta = this.raicRuta +'/usuarios', cadCons = `?filtro=${this.perImpliFiltro}&hayCargo=${this.noEsSupervisor}`;
                cadCons += `&tipoImpli=${this.piTituloMdl == 'Reportado por' ? 'RepPor' : this.piTituloMdl == 'Jefe inmediato' ? 'JefInmed' : 'Superv'}&piRepPor=${this.raicBody.fk_piRepPor_users}&piJefInmed=${this.raicBody.fk_piJefInmed_users}&piSuperv=${this.raicBody.fk_piSuperv_users}`;
                this.peticionGet(ruta, cadCons);
            }
            , fuentesReportesSelect ()
            {
                let ruta = this.raicRuta +'/fuentes_rep';
                this.peticionGet(ruta);
            }
            , proyectoSimple ()
            {
                let ruta = this.proyectoRuta +'/simple', cadCons = '?leer=1';
                this.peticionGet(ruta, cadCons);
            }
            , tiposDesviacionesSelect ()
            {
                let ruta = this.raicRuta +'/tipos_desviaciones';
                this.peticionGet(ruta);
            }
            , cbiListar ()
            {
                let ruta = this.raicRuta +'/cbi';//, cadCons = '?fk_id_raics=';
                this.peticionGet(ruta);
            }
            , cbiDesmarcar ()
            {
                this.cbiArr.forEach((cbiNiv1) => {
                    cbiNiv1.subcausas.forEach((cbiNiv2) => {
                        cbiNiv2.subcausas.forEach((cbiNiv3) => {
                            cbiNiv3.items.forEach((cbiItem) => {
                                cbiItem.marcado = false;
                            });
                        });
                    });
                });
            }
            , cbiMarcar ()
            {
                this.cbiArr.forEach((cbiNiv1) => {
                    cbiNiv1.subcausas.forEach((cbiNiv2) => {
                        cbiNiv2.subcausas.forEach((cbiNiv3) => {
                            cbiNiv3.items.forEach((cbiItem) => {
                                this.items.forEach((item) => {
                                    if (cbiItem.id == item.fk_id_causas_items) {
                                        cbiItem.marcado = true;
                                    }
                                });
                            }, this);
                        }, this);
                    }, this);
                }, this);
            }
            , cbiItemsExtraer ()
            {
                let items = [];
                this.cbiArr.forEach((cbiNiv1) => {
                    cbiNiv1.subcausas.forEach((cbiNiv2) => {
                        cbiNiv2.subcausas.forEach((cbiNiv3) => {
                            cbiNiv3.items.forEach((cbiItem) => {
                                if (cbiItem.marcado) {
                                    items.push(cbiItem.id);
                                }
                            });
                        });
                    });
                });
                return items;
            }
            , labelsCargar (usuario)
            {
                let fkUsu = 'Superv', lblUsu = 'sup';
                if (this.piTituloMdl == 'Reportado por') {
                    fkUsu = 'RepPor';
                    lblUsu = 'rp';
                    this.piLbl.rpCargo = usuario.cargo;
                } else if (this.piTituloMdl == 'Jefe inmediato') {
                    fkUsu = 'JefInmed';
                    lblUsu = 'ji';
                    this.piLbl.jiCargo = usuario.cargo;
                }
                
                this.raicBody[`fk_pi${fkUsu}_users`] = usuario.id;
                this.piLbl[lblUsu +'Documento'] = usuario.num_documento;
                this.piLbl[lblUsu +'NombresCompletos'] = usuario.nombre_completo;

                this.implicadosCerrarMdl();
            }
            , labelsLimpiar (fkUsu, lblUsu)
            {
                this.raicBody[`fk_pi${fkUsu}_users`] = this.raicBody[lblUsu +'Documento'] = this.raicBody[lblUsu +'NombresCompletos'] = this.piLbl[lblUsu +'Documento'] = this.piLbl[lblUsu +'NombresCompletos'] = '';
                if (lblUsu != 'sup') this.raicBody[lblUsu +'Cargo'] = this.piLbl[lblUsu +'Cargo'] = '';
            }
            , cambiarPagina(page, refObjCons, refPagin, listar)
            {
                //Actualiza la página actual
                refObjCons['?page'] = refPagin.current_page = page;
                //Envia la petición para visualizar la data de esa página
                this.tomarFiltro = true;
                switch (listar) {
                    case 1: this.raicListar();break;
                    //...
                }
            }
            , raicListar ()
            {
                let cadCons = Object.entries(this.raicObjCons).map(filtro => `${filtro[0]}=${filtro[1]}`).join('&')
                this.peticionGet(this.raicRuta, cadCons);
            }
            , raicRegistrar ()
            {
                this.raicOnVacios = true;
                this.raicBody.fk_id_proyectos = this.noHayProyecto ? '' : this.proyectoElegido.id;
                this.raicBody.fechaEvento = this.noHayFechaEvt || this.noHayHoraEvt || this.noHayMinutoEvt  ? '' : `${this.fechaEvt} ${this.horaEvt}:${this.minutoEvt}:00`;
                this.items = this.cbiItemsExtraer().slice();
                let ruta = this.raicRuta +'/registrar', raicBody = {...this.raicBody, itemsMarcados: this.items};
                this.peticionPost(ruta, raicBody, 1);
            }
            , raicImplicados ()
            {
                let ruta = this.raicRuta +'/pi_obtener', cadCons = `?fk_piRepPor_users=${this.raicBody.fk_piRepPor_users}&fk_piJefInmed_users=${this.raicBody.fk_piJefInmed_users}&fk_piSuperv_users=${this.raicBody.fk_piSuperv_users}`;
                this.peticionGet(ruta, cadCons);
            }
            , raicCbi ()
            {
                let ruta = this.raicRuta +'/cbi/marcados', cadCons = '?fk_id_raics='+ this.raicId;
                this.peticionGet(ruta, cadCons);
            }
            , raicActualizarAntes ()
            {
                // INGRESA SI Y EL SEGUIMIENTO ES EL MISMO AL INTENTAR ACTUALIZA EL RAIC, POR ENDE NO SERÁ POSIBLE INTENTAR ACTUALIZAR
                // CUANDO SEGUIMIENTO SEA 'Finalizar' PORQUE EL BOTON ACTUALIZAR VA A ESTAR DESHABILITADO PARA RAICS CERRADOS
                if (this.raicBody.haySeguimiento == this.haySeguimientoOld) {
                    this.raicActualizar();
                } else {
                    // INGRESA SI SE CAMBIA SEGUIMIENTO A 'Finalizar' AL INTENTAR ACTUALIZAR EL RAIC, VERIFICA SI HAY CONTINGENCIAS
                    let ruta = this.raicRuta +'/hay_contingencias/';
                    this.actualizarDesde = 'select';
                    this.peticionGet(ruta, this.raicId +'');
                }
            }
            , raicActualizar ()
            {
                this.raicOnVacios = true;
                this.raicBody.fk_id_proyectos = this.noHayProyecto ? '' : this.proyectoElegido.id;
                this.raicBody.fechaEvento = this.noHayFechaEvt || this.noHayHoraEvt || this.noHayMinutoEvt  ? '' : `${this.fechaEvt} ${this.horaEvt}:${this.minutoEvt}:00`;
                this.items = this.cbiItemsExtraer().slice();
                let ruta = this.raicRuta +'/actualizar/', raicBody = {...this.raicBody, itemsMarcados: this.items};
                this.peticionPut(ruta, this.raicId, raicBody, 1);
            }
            , usuariosContingencia ()
            {
                let ruta = this.contingenciaRuta +'/usuarios';
                this.peticionGet(ruta);
            }
            , contingenciaListar ()
            {
                let cadCons = '?fkIdRaics='+ this.raicId;
                this.peticionGet(this.contingenciaRuta, cadCons);
            }
            , contingenciaRegistrar ()
            {
                let ruta = this.contingenciaRuta +'/registrar';
                this.contingenciaBody.fk_respons_users = this.noHayResponsable ? '' : this.contingenciaResponsElegido.id;
                this.contingenciaOnVacios = true;
                this.peticionPost(ruta, this.contingenciaBody, 2);
            }
            , contingenciaActualizar ()
            {
                let ruta = this.contingenciaRuta +'/actualizar/';
                this.contingenciaBody.fk_respons_users = this.noHayResponsable ? '' : this.contingenciaResponsElegido.id;
                this.contingenciaOnVacios = true;
                this.peticionPut(ruta, this.contingenciaId, this.contingenciaBody, 2);
            }
            , contingenciaCondActualizar ()
            {
                this.contingenciaOnVacios = true;
                if (this.noHayObservacion) return;
                this.contingenciaOnVacios = false;
                let ruta = `${this.contingenciaRuta}/${this.contingenciaCondicion == 'Abierto'? 'cerrar':'abrir'}/`, d = new Date(), body;
                body = {
                    fecha: (d.getDate() < 10 ? '0' : '') + d.getDate()
                    +'/'+ (d.getMonth() < 9 ? '0' : '') + (d.getMonth() + 1)
                    +'/'+ d.getFullYear()
                    , observacion: this.contingenciaObservacion
                };
                // AL CERRAR LA CONTINGENCIA SE TOMA EL NOMBRE DEL RESPONSABLE PARA EL ENCABEZADO DE LA BITACORA DE LA COLUMNA 'Al Cerrar'
                if (this.contingenciaResp) body.responsable = this.contingenciaResp;

                this.condPatch(ruta, this.contingenciaId, this.contingenciaCondicion, body);
            }
            , obtenerArchivo (event)
            {
                this.evidenciaBody.ruta = event.target.files[0];
            }
            , evidenciaListar ()
            {
                let cadCons = '?fkIdRaicsContingencias='+ this.contingenciaId;
                this.peticionGet(this.evidenciaRuta, cadCons);
            }
            , evidenciaRegistrar ()
            {
                this.evidenciaOnVacios = true;
                if (this.evidenciaValidar()) return;
                let ruta = this.evidenciaRuta +'/registrar', recursoBody = new FormData(), me = this;
                recursoBody.append('evidencia', this.evidenciaBody.evidencia);
                recursoBody.append('ruta', this.evidenciaBody.ruta);
                recursoBody.append('fk_id_raics_contingencias', this.evidenciaBody.fk_id_raics_contingencias);
                axios.post(ruta, recursoBody, {headers:{'Content-Type':'multipart/form-data'}})
                .then((response) => {
                    me.evidenciaBody.evidencia = me.evidenciaBody.ruta = '';
                    me.$refs.inputRuta.value = '';
                    me.evidenciaOnVacios = false;
                    me.evidenciaListar();
                }).catch(error => console.log(error));
            }
            /*, evidenciaActualizar ()
            {
                this.evidenciaOnVacios = true;
                if (this.evidenciaValidar()) return;
                let ruta = `${this.evidenciaRuta}/actualizar/${this.evidenciaId}`, recursoBody = new FormData(), me = this;
                recursoBody.append('evidencia', this.evidenciaBody.evidencia);
                recursoBody.append('ruta', this.evidenciaBody.ruta);
                recursoBody.append('fk_id_raics_contingencias', this.evidenciaBody.fk_id_raics_contingencias);
                recursoBody.append('_method', 'PUT');
                axios.post(ruta, recursoBody, {headers:{'Content-Type':'multipart/form-data'}})
                .then((response) => {
                    me.evidenciaMdlMdlCerrar();
                    me.evidenciaListar();
                }).catch(error => console.log(error));
            }*/
            , peticionGet (uri, cadenaConsulta = '')
            {
                let peticion = this.axiosApp.get(`${uri}${cadenaConsulta}`);
                this.obtenerRespuesta(uri, peticion);
            }
            , peticionPost (uri, camposBody, validar)
            {
                if (this.esRecursoInvalido(validar))return;
                let peticion = this.axiosApp.post(uri, camposBody);
                this.obtenerRespuesta(uri, peticion);
            }
            , peticionPut (uri, idRecurso, camposBody, validar)
            {
                if (this.esRecursoInvalido(validar))return;
                let peticion = this.axiosApp.put(`${uri}${idRecurso}`, camposBody);
                this.obtenerRespuesta(uri, peticion);
            }
            , estadoPatch (ruta, recursoId, recursoEstado)
            {
                this.tituloSwal = 'Activado';
                this.descripcionSwal = 'Registro activado con éxito.';
                if (recursoEstado) {
                    this.tituloSwal = 'Desactivado';
                    this.descripcionSwal = 'Registro desactivado con éxito.';
                }
                let peticion = this.axiosApp.patch(ruta + recursoId);
                this.obtenerRespuesta(ruta, peticion);
            }
            , condPatch (ruta, recursoId, condicion, recursoBody)
            {
                this.tituloSwal = 'Reanudada';
                this.descripcionSwal = 'Contingencia reanudada con éxito.';
                if (condicion == 'Abierto') {
                    this.tituloSwal = 'Finalizada';
                    this.descripcionSwal = 'Contingencia finalizada con éxito.';
                }
                let peticion = this.axiosApp.patch(ruta + recursoId, recursoBody);
                this.obtenerRespuesta(ruta, peticion);
            }
            , peticionDelete (ruta, recursoId)
            {
                let peticion = this.axiosApp.delete(ruta + recursoId);
                this.obtenerRespuesta(ruta, peticion);
            }
            , esRecursoInvalido (validar) {
                switch (validar) {
                    case 1:
                        if (this.raicValidar()) {
                            if (this.noHayCbiMarcadas) Swal.fire('Causas requeridas!', 'Debes marcar al menos una causa para continuar', 'info');
                            return true;
                        }
                        break;
                    case 2:
                        if (this.contingenciaValidar()) return true;
                        break;
                }
                return false;
            }
            , raicValidar ()
            {
                return this.noHayRPDocumento || this.noHayRPNombresCompletos || this.noHayRPCargo || this.noHayRPAreaTrabajo || this.noHayJIDocumento || this.noHayJINombresCompletos || this.noHayJICargo || this.noHaySupDocumento || this.noHaySupNombresCompletos || this.noHayFechaReporte || this.noHayFuenteReporte || this.noHayTipoEvento || this.noHayProyecto || this.noHayTipoDesviacion || this.noHayAreaTrabajo || this.noHayFechaEvt || this.noHayHoraEvt || this.noHayMinutoEvt || this.noHaySitioEspecifico || this.noHayPrioridad || this.noHayDescripcionCorta || this.noHayDescripcionEvento || this.noHayRaicAccion || this.noHayCbiMarcadas;
            }
            , contingenciaValidar ()
            {
                return this.noHayConsecutivo || this.noHayAccion || this.noHayFechaLimite || this.noHayResponsable;
            }
            , evidenciaValidar ()
            {
                return this.noHayEvidencia || this.noHayRuta;
            }
            , obtenerRespuesta(uri, peticion)
            {
                let me = this;
                peticion.then(function (response) {
                    let respuesta = response.data;
                    if (uri.endsWith('desactivar/') || uri.endsWith('activar/')) Swal.fire(me.tituloSwal, me.descripcionSwal, 'success');
                    switch (uri) {
                        case me.raicRuta +'/logid':
                            me.id = respuesta.id;
                            break;
                        case me.raicRuta +'/usuarios_filtro_sel':
                            me.usuariosSelArr = respuesta.usuarios;
                            break;
                        case me.raicRuta +'/usuarios':
                            me.usuariosArr = respuesta.usuarios;
                            break;
                        case me.raicRuta +'/fuentes_rep':
                            me.fuentesRepsSelectArr = respuesta.fuentesRep;
                            break;
                        case me.proyectoRuta +'/simple':
                            me.proyectoArr = respuesta.proyectos;
                            break;
                        case me.raicRuta +'/tipos_desviaciones':
                            me.tiposDesviSelectArr = respuesta.tiposDesviaciones;
                            break;
                        case me.raicRuta +'/cbi':
                            me.cbiArr = respuesta.causasBasInmed;
                            break;
                        case me.raicRuta:
                            me.raicArr = respuesta.raics.data;
                            me.raicPagin = respuesta.pagination;
                            break;
                        case me.raicRuta +'/registrar':
                        case me.raicRuta +'/actualizar/':
                            // RECARGA EL FILTRO Reportado por
                            if (me.raicBody.rpDocumento || me.raicBody.rpNombresCompletos) me.usuariosSel();
                            me.cerrarModal();
                            me.raicListar();
                            break;
                        case me.raicRuta +'/cbi/marcados':
                            me.items = respuesta.items;
                            me.cbiMarcar();
                            break;
                        case me.raicRuta +'/pi_obtener':
                            if (me.raicBody.fk_piRepPor_users) {
                                let repPor = me.raicBody.fk_piRepPor_users;
                                me.piLbl.rpDocumento = respuesta.raicImplicados[repPor].num_documento;
                                me.piLbl.rpNombresCompletos = respuesta.raicImplicados[repPor].nombre_completo;
                                me.piLbl.rpCargo = respuesta.raicImplicados[repPor].cargo;
                            }
                            if (me.raicBody.fk_piJefInmed_users) {
                                let jefInmed = me.raicBody.fk_piJefInmed_users;
                                me.piLbl.jiDocumento = respuesta.raicImplicados[jefInmed].num_documento;
                                me.piLbl.jiNombresCompletos = respuesta.raicImplicados[jefInmed].nombre_completo;
                                me.piLbl.jiCargo = respuesta.raicImplicados[jefInmed].cargo;
                            }
                            if (me.raicBody.fk_piSuperv_users) {
                                let superv = me.raicBody.fk_piSuperv_users;
                                me.piLbl.supDocumento = respuesta.raicImplicados[superv].num_documento;
                                me.piLbl.supNombresCompletos = respuesta.raicImplicados[superv].nombre_completo;
                            }
                            break;
                        case me.raicRuta +'/hay_contingencias/':
                            // INGRESA SI SE INTENTA FINALIZAR EL RAIC
                            if (me.actualizarDesde == 'select') {
                                if (respuesta.abiertas == -1) {
                                    me.raicActualizar();
                                } else {
                                    Swal.fire('No es posible actualizar!', 'Este RAIC ya tiene contingencias vinculadas', 'info');
                                }
                            }
                            // INGRESA SI SE INTENTA CERRAR EL RAIC
                            if (me.actualizarDesde == 'botonCierre') {
                                // tmpEsCerrable[4] => haySeguimiento
                                switch (respuesta.abiertas) {
                                    case -1: Swal.fire('Sin contingencias!', 'Este RAIC requiere al menos una contingencia', 'info');break;
                                    case 0: me.actualizarCondicion(me.tmpEsCerrable[0], me.tmpEsCerrable[1], me.tmpEsCerrable[2], me.tmpEsCerrable[3]);break;
                                    case 1: Swal.fire('Contingencias abiertas!', 'Hay contingencias por cerrar en este RAIC', 'info');break;
                                }
                            }
                            me.actualizarDesde = '';
                            break;
                        case me.raicRuta +'/desactivar/':
                        case me.raicRuta +'/activar/':
                            me.raicListar();
                            break;
                        case me.contingenciaRuta +'/usuarios':
                            me.responsablesArr = respuesta.usuarios;
                            break;
                        case me.contingenciaRuta:
                            me.contingenciaArr = respuesta.raicContingencias;
                            break;
                        case me.contingenciaRuta +'/registrar':
                        case me.contingenciaRuta +'/actualizar/':
                            me.contingenciaMdlMdlCerrar();
                            me.contingenciaListar();
                            break;
                        case me.contingenciaRuta +'/cerrar/':
                            me.evidenciaMdlCerrar();
                            me.contingenciaListar();
                            break;
                        case me.contingenciaRuta +'/abrir/':
                            me.contingenciaCondMdlCerrar();
                            me.contingenciaListar();
                            break;
                        case me.contingenciaRuta +'/desactivar/':
                        case me.contingenciaRuta +'/activar/':
                            me.contingenciaListar();
                            break;
                        case me.evidenciaRuta:
                            me.evidenciaArr = respuesta.raicEvidencias;
                            break;
                        /*case me.evidenciaRuta +'/registrar':
                        case me.evidenciaRuta +'/actualizar/':
                            me.evidenciaMdlMdlCerrar();
                            me.evidenciaListar();
                            break;*/
                        case me.evidenciaRuta +'/descartar/':
                            Swal.fire('Descartado!', 'La evidencia ha sido descartada con éxito', 'success');
                            me.evidenciaArr.splice(me.recursoIdx, 1);
                            break;
                    }
                }).catch(function (error) {
                    console.log(error);
                });
            }
            , actualizarEstado(recursoEstado, finalTitle, recursoRuta, recursoId)
            {
                let prefijo = recursoEstado ? 'des' : '';
                Swal.fire({
                    title: `Esta seguro de ${prefijo}activar ${finalTitle}?`,
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
                        let ruta = `${recursoRuta}/${prefijo}activar/`;
                        this.estadoPatch(ruta, recursoId, recursoEstado);
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
        }
        , mounted ()
        {
            /*$('#dp').datetimepicker({locale : 'es', format: 'L'});
            $(#dp').on('change.datetimepicker', (e) => this.raicBody.fechaReporte = e.date._i);*/
            this.usuIdObtener();
            this.usuariosSel();
            this.raicListar();
            this.fuentesReportesSelect();
            this.proyectoSimple();
            this.tiposDesviacionesSelect();
            this.cbiListar();
            this.usuariosContingencia();
            // SE CREAN LOS EVENTOS PARA LOS ELEMENTOS QUE CONFORMAN LA MATRIZ RAIC EN EL SWAL.
            let pista = 'table-primary', csel = 'csel', psel = 'psel';
            for (let c = 1; c < 6; c++) {
                for (let p = 1; p < 6; p++) {
                    $('body').on('mouseenter', `#cp_${c}${p}`, () => {
                        if (!$('#c' + c).hasClass(pista)) $('#c' + c).addClass(pista);
                        if (!$('#p' + p).hasClass(pista)) $('#p' + p).addClass(pista);
                        // IMITA EL TEXTO CSS DEL BOTON WARNING ORIGINAL
                        if (c +''+ p == '13' || c +''+ p == '22' || c +''+ p == '31') $(`#cp_${c}${p}`).css('color', '#212529');
                    });
                    $('body').on('mouseleave', `#cp_${c}${p}`, () => {
                        if (!$('#c' + c).hasClass(csel)) $('#c' + c).removeClass(pista);
                        if (!$('#p' + p).hasClass(psel)) $('#p' + p).removeClass(pista);
                        // IMITA EL TEXTO CSS DEL BOTON WARNING ORIGINAL
                        if (c +''+ p == '13' || c +''+ p == '22' || c +''+ p == '31') {
                            if (!$(`#cp_${c}${p}`).hasClass('active')) $(`#cp_${c}${p}`).css('color', '#ffc107');
                        }
                    });
                    $('body').on('click', `#cp_${c}${p}`, () => {
                        if ($('.btn-mtx.active').length) {
                            $('.' + csel).removeClass(pista +' '+ csel);
                            $('.' + psel).removeClass(pista +' '+ psel);
                            // IMITA EL TEXTO CSS DEL BOTON WARNING ORIGINAL
                            if ($('.btn-mtx.active').attr('id') == 'cp_13' || $('.btn-mtx.active').attr('id') == 'cp_22' || $('.btn-mtx.active').attr('id') == 'cp_31') {
                                if ($('.btn-mtx.active').attr('id') != `cp_${c}${p}`) $('.btn-mtx.active').css('color', '#ffc107');
                            }
                        }
                        $('#c' + c).addClass(pista +' '+ csel);
                        $('#p' + p).addClass(pista +' '+ psel);
                    });
                    $('body').on('dblclick', `#cp_${c}${p}`, () => {
                        this.raicBody.prioridad = c +''+ p;
                        Swal.clickConfirm();
                    });
                }
            }
            let raicTrozoBtnAceptable = `class="btn-mtx btn btn-sm btn-outline-success my-0 py-2">${this.raicInputBtns}Aceptable</label>`;
            let raicTrozoBtnDeAlerta = `class="btn-mtx btn btn-sm btn-outline-warning my-0 py-2">${this.raicInputBtns}De alerta</label>`;
            let raicTrozoBtnNoAceptableRojo = `class="btn-mtx btn btn-sm btn-outline-danger my-0 py-2">${this.raicInputBtns}No aceptable</label>`;
            let raicTrozoBtnNoAceptableNegro = `class="btn-mtx btn btn-sm btn-outline-dark my-0 py-2">${this.raicInputBtns}No aceptable</label>`;
            this.matrizRaic = Swal.mixin({
                html :
                    '<div class="alert alert-info" role="alert">Haz click para marcar y doble click para seleccionar</div>'
                    + '<div class="btn-group" data-toggle="buttons">'
                    + '<table width="100%" class="table table-sm table-bordered table-raic">'
                    + '<thead>'
                    + '<tr><th colspan="2" class="border-0"></th><th colspan="5">PROBABILIDAD</th></tr>'
                    + '<tr>'
                    + '<th colspan="2" class="border-0"></th>'
                    + '<th id="p1" style="width:7.583rem;">1. Rara</th>'
                    + '<th id="p2" style="width:7.583rem;">2. Improbable</th>'
                    + '<th id="p3" style="width:7.583rem;">3. Posible</th>'
                    + '<th id="p4" style="width:7.583rem;">4. Probable</th>'
                    + '<th id="p5" style="width:7.583rem;">5. Casi cierto</th>'
                    + '</tr>'
                    + '</thead>'
                    + '<tbody>'
                    + '<tr>'
                    + '<th rowspan="5" class="rotate">CONSECUENCIA</th>'
                    + '<th id="c1" class="text-left">1. Leve</th>'
                    + `<td><label id="cp_11" ${raicTrozoBtnAceptable}</td>`
                    + `<td><label id="cp_12" ${raicTrozoBtnAceptable}</td>`
                    + `<td><label id="cp_13" ${raicTrozoBtnDeAlerta}</td>`
                    + `<td><label id="cp_14" ${raicTrozoBtnNoAceptableRojo}</td>`
                    + `<td><label id="cp_15" ${raicTrozoBtnNoAceptableRojo}</td>`
                    + '</tr>'
                    + '<tr>'
                    + '<th id="c2" class="text-left">2. Moderado</th>'
                    + `<td><label id="cp_21" ${raicTrozoBtnAceptable}</td>`
                    + `<td><label id="cp_22" ${raicTrozoBtnDeAlerta}</td>`
                    + `<td><label id="cp_23" ${raicTrozoBtnNoAceptableRojo}</td>`
                    + `<td><label id="cp_24" ${raicTrozoBtnNoAceptableRojo}</td>`
                    + `<td><label id="cp_25" ${raicTrozoBtnNoAceptableRojo}</td>`
                    + '</tr>'
                    + '<tr>'
                    + '<th id="c3" class="text-left">3. Severo</th>'
                    + `<td><label id="cp_31" ${raicTrozoBtnDeAlerta}</td>`
                    + `<td><label id="cp_32" ${raicTrozoBtnNoAceptableRojo}</td>`
                    + `<td><label id="cp_33" ${raicTrozoBtnNoAceptableRojo}</td>`
                    + `<td><label id="cp_34" ${raicTrozoBtnNoAceptableRojo}</td>`
                    + `<td><label id="cp_35" ${raicTrozoBtnNoAceptableNegro}</td>`
                    + '</tr>'
                    + '<tr>'
                    + '<th id="c4" class="text-left">4. Fatal</th>'
                    + `<td><label id="cp_41" ${raicTrozoBtnNoAceptableRojo}</td>`
                    + `<td><label id="cp_42" ${raicTrozoBtnNoAceptableRojo}</td>`
                    + `<td><label id="cp_43" ${raicTrozoBtnNoAceptableRojo}</td>`
                    + `<td><label id="cp_44" ${raicTrozoBtnNoAceptableNegro}</td>`
                    + `<td><label id="cp_45" ${raicTrozoBtnNoAceptableNegro}</td>`
                    + '</tr>'
                    + '<tr>'
                    + '<th id="c5" class="text-left">5. Catastrófico</th>'
                    + `<td><label id="cp_51" ${raicTrozoBtnNoAceptableRojo}</td>`
                    + `<td><label id="cp_52" ${raicTrozoBtnNoAceptableRojo}</td>`
                    + `<td><label id="cp_53" ${raicTrozoBtnNoAceptableNegro}</td>`
                    + `<td><label id="cp_54" ${raicTrozoBtnNoAceptableNegro}</td>`
                    + `<td><label id="cp_55" ${raicTrozoBtnNoAceptableNegro}</td>`
                    + '</tr>'
                    + '</tbody>'
                    + '</table></div>'
                , width : '913px'
                , position : 'top'
                , showConfirmButton : false
            });
            //$('#raic-mdl').on('shown.bs.modal', () => this.$refs.qrdoc.focus());
            
            /*$('#raic-list a[href="#cbi-list"]').on('hide.bs.tab', function (e) {
                e.target // newly activated tab
                e.relatedTarget // previous active tab
            });*/
            // SCROLL A TOP AL HACER CLICK SOBRE UN ANCHOR DIFERENTE EN EL ACORDEON
            $('#nav-tabContent').on('click', 'a', function () {
                $('#nav-tabContent').scrollTop(0);
            });
            //this.contingenciaCondicionActualizar();
            $('#contingenciacond-mdl').on('shown.bs.modal', () => this.$refs.contingenciaBObservacion.focus());
        }
        , beforeDestroy () {
            // SE DESTRUYEN LOS EVENTOS DE LOS ELEMENTOS
            // Matriz raic-swal
            for (let c = 1; c < 6; c++) {
                for (let p = 1; p < 6; p++) {
                    $('body').off('mouseenter mouseleave click dblclick', `#cp_${c}${p}`);
                }
            }
            $('#nav-tabContent').off('click', 'a');
        }
    }
</script>
<style>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    #modalDialogRaic{
        overflow-y: initial !important;
    }
    /*#modalBodyRaic{
        height: 400px;
        overflow-y: auto;
    }*/
    .inputs-raic{
        width : 97.5%;
    }
    .cbi{
        margin-bottom : 0rem !important;
    }
    .btn-pi{
        min-width: 2rem;
    }
    @media (max-width: 600px) {
        .menu-h{
            display : block;
            
        }
        .menu-v{
            display : none;
        }
        .list-group-item{
            float: left !important;
           margin-left: 1em !important;
            margin-bottom: 1em !important;
        }
    }
    @media (min-width: 601px) {
        .menu-h{
            display : none;
        }
        .menu-v{
            display : flex;
        }
    }
    .table-raic th{
        text-align : center;
        vertical-align : middle;
    }
    .btn-mtx{
        width : 100%;
    }
    input[name="options"]{
        display : none;
    }
    .table-hover tbody tr:hover{
        background-color: #d9edf7
    }
    .is-danger .multiselect__tags {
        border-color: #f86c6b;
    }
</style>