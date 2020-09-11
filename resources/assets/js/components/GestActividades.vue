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
                            <i class="fa fa-tasks fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;Gestión actividades
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-auto  my-1 pr-1">Segmento</label>
                        <div class="col-md-3 pl-1">
                            <select class="form-control" v-model="actividadObjCons.fkIdSegmentos" @change="actividadObjCons.fkIdPlanesAccion = ''">
                                <option value="" selected>Todos</option>
                                <option v-for="segmento in segmentoArr" :key="segmento.id" :value="segmento.id" v-text="segmento.segmento"></option>
                            </select>
                        </div>
                        <label class="col-md-auto  my-1 pr-1">Plan acción</label>
                        <div class="col-md-3 pl-1">
                            <select class="form-control" v-model="actividadObjCons.fkIdPlanesAccion">
                                <option value="" selected>Todos</option>
                                <option v-for="planAccion in planAccionArr[actividadObjCons.fkIdSegmentos]" :key="planAccion.id" :value="planAccion.id" v-text="planAccion.planAccion"></option>
                            </select>
                        </div>
                        <label class="col-md-auto  my-1 pr-1">Proyecto</label>
                        <div class="col-md-3 pl-1">
                            <select class="form-control" v-model="actividadObjCons.fkIdProyectos">
                                <option value="" selected>Todos</option>
                                <option v-for="proyecto in proyectoArr" :key="proyecto.id" :value="proyecto.id" v-text="proyecto.proyecto"></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-auto my-1 pr-1">Ordenar por</label>
                        <div class="col-md-auto pl-1">
                            <select class="form-control" v-model="actividadObjCons.ordenColumna">
                                <option value="actividad">Actividad</option>
                                <option value="segmento">Segmento</option>
                                <option value="planAccion">Plan acción</option>
                                <option value="proyecto">Proyecto</option>
                                <option value="ponderacion">Ponderación</option>
                                <option value="indicador">Indicador</option>
                                <option value="planAsociado">Plan asociado</option>
                                <option value="diaMesLimite">Día reporte</option>
                            </select>
                        </div>
                        <label class="col-md-auto my-1 pr-1">Sentido</label>
                        <div class="col-md-auto pl-1">
                            <select class="form-control" v-model="actividadObjCons.ordenSentido">
                                <option value="asc">Ascendente</option>
                                <option value="desc">Descendente</option>
                            </select>
                        </div>
                        <label class="col-md-auto my-1 pr-1">Año</label>
                        <div class="col-md-1 pl-1">
                            <select class="form-control" v-model="actividadObjCons.anio">
                                <option v-for="anio in aniosArr" :key="anio" :value="anio" v-text="anio"></option>
                            </select>
                        </div>
                        <label class="col-md-auto my-1 pr-1">Ver</label>
                        <div class="col-md-auto pl-1">
                            <select class="form-control" @change="cambiarPagina(1, actividadObjCons, actividadPagin)" v-model="actividadObjCons.numRegs">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <button type="button" @click="cambiarPagina(1, actividadObjCons, actividadPagin)" class="btn btn-pill btn-primary ml-2"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;Buscar</button>
                    </div>
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Actividad</th>
                                <th class="text-center">Segmento</th>
                                <th class="text-center">Plan acción</th>
                                <th class="text-center">Proyecto</th>
                                <th class="text-center">Ponderación</th>
                                <th class="text-center">Indicador</th>
                                <th class="text-center">Plan asociado</th>
                                <th class="text-center">Día reporte</th>
                                <th class="text-center">Ene</th><th class="text-center">Feb</th><th class="text-center">Mar</th><th class="text-center">Abr</th>
                                <th class="text-center">May</th><th class="text-center">Jun</th><th class="text-center">Jul</th><th class="text-center">Ago</th>
                                <th class="text-center">Sep</th><th class="text-center">Oct</th><th class="text-center">Nov</th><th class="text-center">Dic</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-if="actividadesArr.length == 0">
                                <tr>
                                    <td class="text-center" colspan="21">No hay registros</td>
                                </tr>
                            </template>
                            <template v-else>
                                <tr v-for="(actividad, index) in actividadesArr" :key="actividad.id">
                                    <td class="text-center" v-text="index + 1"></td>
                                    <td v-text="actividad.actividad"></td>
                                    <td v-text="actividad.segmento"></td>
                                    <td v-text="actividad.planAccion"></td>
                                    <td v-text="actividad.proyecto"></td>
                                    <td v-text="actividad.ponderacion"></td>
                                    <td v-text="actividad.indicador"></td>
                                    <td v-text="actividad.planAccionAsociado"></td>
                                    <td class="text-center" v-text="actividad.diaMesLimite"></td>
                                    <td class="text-center" v-for="mes in arrMeses">
                                        <button type="button" class="circulo border border-dark rounded-circle" v-if="actividad[mes.abrev] != 0"
                                            @click="abrirModal('mostrarSeguimiento', {
                                                idSeguimiento : collSegs[actividad.id][mes.abrev].hasOwnProperty('id') ? collSegs[actividad.id][mes.abrev]['id'] : 0
                                                , idActividad : actividad.id
                                                , actividad : actividad.actividad
                                                , abrev : mes.abrev
                                                , mes : mes.mes
                                            })"
                                            :class="collSegs[actividad.id][mes.abrev]['estilo']"></button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <nav>
                        <ul class="pagination">
                            <li class="page-item" v-if="actividadPagin.current_page > 1">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(actividadPagin.current_page - 1, actividadObjCons, actividadPagin)">Ant</a>
                            </li>
                            <li class="page-item" v-for="page in actividadPagesNumber" :key="page" :class="[page == actividadPagin.current_page ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(page, actividadObjCons, actividadPagin)" v-text="page"></a>
                            </li>
                            <li class="page-item" v-if="actividadPagin.current_page < actividadPagin.last_page">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(actividadPagin.current_page + 1, actividadObjCons, actividadPagin)">Sig</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        <div id="seguimiento-evidencia-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-html="tituloMdl"></h4>
                        <button type="button" class="close" @click="cerrarMdl()" data-dismiss="modal" aria-label="Close"> <!-- PENDIENTE -->
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-group row justify-content-center">
                                <div class="col-md-auto">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-addon pb-1">
                                            <input id="noAplica" type="checkbox" class="mx-1" v-model="seguimientoBody.noAplica">
                                        </span>
                                        <span class="input-group-addon px-0 py-0"><label class="mb-0 px-2" for="noAplica">No se aplica</label></span>
                                    </div>
                                </div>
                                <label class="col-md-auto">Fecha Reporte</label>
                                <div class="col-md-2">
                                    <input type="date" class="form-control" :class="{'is-invalid' : noHayFechaReporte}" v-model="seguimientoBody.fechaReporte">
                                </div>
                                <label class="col-md-auto">Observaciones</label>
                                <div class="col-md-4">
                                    <textarea ref="seguimientoBObservacion" v-model="seguimientoBody.observacion" class="form-control" :class="{'is-invalid' : noHayObservacion}" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="form-group row justify-content-center">
                                <button type="button" class="btn btn-pill btn-success btn-sm" @click="abrev? seguimientoRegistrar() : seguimientoActualizar()" v-text="abrev? 'Guardar':'Actualizar'"></button>
                            </div>
                        </form>
                        <div v-if="!abrev">
                            <div class="row justify-content-center">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <i class="fa fa-file-o" aria-hidden="true"></i>&nbsp;Evidencias
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group row justify-content-start">
                                                <div class="col-md-4">
                                                    <input type="text" ref="evidenciacbEvidencia" v-model="evidenciaBody.evidencia" class="form-control" :class="{'is-invalid' : noHayEvidencia}" placeholder="Nombre de evidencia" maxlength="50">
                                                </div>
                                                <div class="col-md-5">
                                                    <input type="file" class="form-control-file form-control" :class="{'is-invalid' : noHayRuta || evidenciaInvalida}" id="inputArchivo"  @change="archivoVModel">
                                                    <small class="form-text text-muted font-weight-bold">Archivos admitidos: GIF, JPEG, JPG, PDF, PNG.</small>
                                                </div>
                                                <div class="col-md-auto">
                                                    <button id="evidenciaguardar" type="button" class="btn btn-pill btn-info btn-sm" @click="evidenciaRegistrar();$refs.evidenciacbEvidencia.focus();">
                                                        <i class="fa fa-upload" aria-hidden="true"></i>&nbsp;Subir evidencia
                                                    </button>
                                                </div>
                                            </div>
                                            <table class="table table-bordered table-striped table-sm">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">#</th>
                                                        <th class="text-center">Evidencia</th>
                                                        <th class="text-center">Visualizar</th>
                                                        <th class="text-center">Descartar</th>
                                                    </tr>
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
                                                                <a type="button" title="Visualizar" :href="evidencia.ruta" target="_blank" class="btn rounded btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a></td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn rounded btn-danger btn-sm" @click="descartar(index, evidencia.id)">
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-pill btn-danger btn-sm" @click="cerrarMdl()" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>
<script>
    export default {
        props : ['permisosUser' ,'ruta', 'axiosApp']
        , data () {
            return {
                gestionActividadRuta : 'gestion_actividades'
                , indicadorActividadRuta : 'indicadores_actividades'
                , actividadSeguimientoRuta : 'actividades_seguimientos'
                , actividadEvidenciaRuta : 'actividades_evidencias'
                , segmentoRuta : 'segmentos'
                , proyectoRuta : 'conf_proyectos'
                
                , aniosArr : []
                , planAccionArr : []
                , segmentoArr : []
                , proyectoArr : []
                , actividadObjCons : {
                    '?page' : 1
                    , anio : 0
                    , fkIdPlanesAccion : ''
                    , fkIdSegmentos : ''
                    , fkIdProyectos : ''
                    , ordenColumna : 'actividad'
                    , ordenSentido : 'asc'
                    , numRegs : 10
                }
                , actividadesArr : []

                , seguimientoOnVacios : false
                , seguimientoBody : {
                    fechaReporte : ''
                    , noAplica : false
                    , observacion : ''
                }

                , id : 0 // PUEDE SER EL ID ACTIVIDAD O SEGUIMIENTO
                , abrev : ''
                , collSegs : []
                , indDescartar : 0

                , evidenciaArr : []

                , evidenciaOnVacios : false
                , evidenciaBody : {
                    evidencia : ''
                    , ruta : ''
                }
                
                , arrMeses : [{abrev : 'ene', mes : 'Enero'}, {abrev : 'feb', mes : 'Febrero'}, {abrev : 'mar', mes : 'Marzo'}, {abrev : 'abr', mes : 'Abril'}, {abrev : 'may', mes : 'Mayo'}, {abrev : 'jun', mes : 'Junio'}, {abrev : 'jul', mes : 'Julio'}, {abrev : 'ago', mes : 'Agosto'}, {abrev : 'sep', mes : 'Septiembre'}, {abrev : 'oct', mes : 'Octubre'}, {abrev : 'nov', mes : 'Noviembre'}, {abrev : 'dic', mes : 'Diciembre'}]
                , arrMesEstado : ['', 'btn btn-warning btn-sm', 'btn btn-success btn-sm', 'btn btn-danger btn-sm']

                , tituloMdl : ''

                , actividadPagin : {
                    'total' : 0
                    , 'current_page' : 0
                    , 'per_page' : 0
                    , 'last_page' : 0
                    , 'from' : 0
                    , 'to' : 0
                }
                , offset : 3
            }
        }
        , computed :
        {
            // VALIDACIÓN DE CAMPOS VACIOS EN FORMULARIOS
            //Campos seguimiento
            noHayFechaReporte : function ()
            {
                return this.seguimientoOnVacios && !this.seguimientoBody.fechaReporte;
            }
            , noHayObservacion : function ()
            {
                return this.seguimientoOnVacios && !this.seguimientoBody.observacion.trim();
            }
            // Campos evidencia
            , noHayEvidencia : function ()
            {
                return this.evidenciaOnVacios && !this.evidenciaBody.evidencia.trim();
            }
            , noHayRuta : function ()
            {
                return this.evidenciaOnVacios && !this.evidenciaBody.ruta;
            }
            , evidenciaInvalida : function ()
            {
                let invalido = false;
                if (typeof this.evidenciaBody.ruta == 'object') {
                    if (this.evidenciaBody.ruta.type != 'application/pdf' && this.evidenciaBody.ruta.type != 'image/gif'
                    && this.evidenciaBody.ruta.type != 'image/jpeg' && this.evidenciaBody.ruta.type != 'image/png') {
                        invalido = true;
                    }
                }
                return this.evidenciaOnVacios && invalido;
            }
            , actividadPagesNumber: function ()
            {
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
            , fechaActual : function ()
            {
                let fechaActual = new Date(), anioActual;
                anioActual = fechaActual.getFullYear();
                for (let i = -5; i < 6; i++) {
                    this.aniosArr.push(anioActual + i);
                }
                return anioActual
                +'-'+ (fechaActual.getMonth() < 9? '0' : '') + (fechaActual.getMonth() + 1)
                +'-'+ (fechaActual.getDate() < 10? '0' : '') + fechaActual.getDate();
            }
        }
        , methods:
        {
            abrirModal(accion, data = {})
            {
                switch (accion) {
                    case 'mostrarSeguimiento':
                        this.tituloMdl = `Actividad - ${data.actividad}</br>Mes - ${data.mes}`;
                        if (data.idSeguimiento) {
                            this.id = data.idSeguimiento;
                            this.seguimientoObtener();
                        } else {
                            //var fechaActual = new Date();
                            this.seguimientoBody.fechaReporte = this.fechaActual;
                            this.id = data.idActividad;
                            this.abrev = data.abrev;
                        }
                        $('#seguimiento-evidencia-mdl').modal('show');
                        // ENVIAR PETICION PARA OBTENER REGISTRO DEL SEGUIMIENTO Y SUS ARCHIVOS
                        break;
                }
            }
            , cambiarPagina (page, refObjCons, refPagin)
            {
                //Actualiza la página actual
                refObjCons['?page'] = refPagin.current_page = page;
                //Envia la petición para visualizar la data de esa página
                this.actividadListar();
            }
            , segmentoSimple ()
            {
                let ruta = this.segmentoRuta +'/simple', cadCons = `?leer=${this.permisosUser.leer}`;
                this.peticionGet(ruta, cadCons);
            }
            , basicoPlanesAccion ()
            {
                let ruta = this.indicadorActividadRuta +'/basico_planes_accion';
                this.peticionGet(ruta);
            }
            , proyectoSimple ()
            {
                let ruta = this.proyectoRuta +'/simple', cadCons = `?leer=${this.permisosUser.leer}`;
                this.peticionGet(ruta, cadCons);
            }
            , actividadListar ()
            {
                let cadCons = Object.entries(this.actividadObjCons).map(filtro => `${filtro[0]}=${filtro[1]}`).join('&');
                this.peticionGet(this.gestionActividadRuta, cadCons);
            }
            , seguimientoObtener () {
                this.peticionGet(this.actividadSeguimientoRuta + '/', this.id);
            }
            , seguimientoValidar ()
            {
                return this.noHayFechaReporte || this.noHayObservacion;
            }
            , seguimientoRegistrar ()
            {
                this.seguimientoOnVacios = true;
                if(this.seguimientoValidar())return;
                let ruta = this.actividadSeguimientoRuta +'/registrar', refSeguimiento = {mes : this.abrev, fk_id_actividades : this.id};
                this.peticionPost(ruta, {...this.seguimientoBody, ...refSeguimiento});
            }
            , seguimientoActualizar ()
            {
                this.seguimientoOnVacios = true;
                if(this.seguimientoValidar())return;
                let ruta = this.actividadSeguimientoRuta +'/actualizar/';
                this.peticionPut(ruta, this.id, this.seguimientoBody);
            }
            , evidenciaListar ()
            {
                this.peticionGet(this.actividadEvidenciaRuta, '?fkIdActividadSeguimiento='+ this.id);
            }
            , archivoVModel (event)
            {
                this.evidenciaBody.ruta = event.target.files[0];
            }
            , evidenciaValidar ()
            {
                return this.noHayEvidencia || this.noHayRuta || this.evidenciaInvalida;
            }
            , evidenciaRegistrar ()
            {
                this.evidenciaOnVacios = true;
                if (this.evidenciaValidar())return;
                let ruta = `${this.actividadEvidenciaRuta}/registrar`, evidenciaBody = new FormData(), me = this;
                evidenciaBody.append('evidencia', this.evidenciaBody.evidencia);
                evidenciaBody.append('ruta', this.evidenciaBody.ruta);
                evidenciaBody.append('fk_id_actividades_seguimientos', this.id);
                axios.post(ruta, evidenciaBody, {headers:{'Content-Type':'multipart/form-data'}}
                ).then(function (response) {
                    me.evidenciaOnVacios = false;
                    me.evidenciaBody.evidencia = me.evidenciaBody.ruta = '';
                    $('#inputArchivo').val('');
                    me.evidenciaListar();
                }).catch(function (error) {
                    console.log(error);
                });
            }
            , descartar (index, recursoId) {
                Swal.fire({
                    title: `Esta seguro de descartar esta evidencia?`,
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
                        this.indDescartar = index;
                        let ruta = this.actividadEvidenciaRuta +'/descartar/';
                        this.peticionDelete(ruta, recursoId);
                    } else if (result.dismiss === Swal.DismissReason.cancel /* Read more about handling dismissals */) {}
                });
            }
            , cerrarMdl ()
            {
                this.id = 0;
                this.seguimientoBody.noAplica = this.seguimientoOnVacios = this.evidenciaOnVacios = false;
                this.tituloMdl = this.seguimientoBody.fechaReporte = this.seguimientoBody.observacion = this.evidenciaBody.evidencia = this.evidenciaBody.ruta = this.abrev = '';
                $('#inputArchivo').val('');
                this.evidenciaArr = [];
            }
            , peticionGet (ruta, cadCons = '')
            {
                let peticion = this.axiosApp.get(ruta + cadCons);
                this.obtenerRespuesta(ruta, peticion);
            }
            , peticionPost (ruta, recursoBody)
            {
                let peticion = this.axiosApp.post(ruta, recursoBody);
                this.obtenerRespuesta(ruta, peticion);
            }
            , peticionPut (ruta, recursoId, recursoBody)
            {
                let peticion = this.axiosApp.put(ruta + recursoId, recursoBody);
                this.obtenerRespuesta(ruta, peticion);
            }
            , peticionDelete (ruta, recursoId) {
                let peticion = this.axiosApp.delete(ruta + recursoId);
                this.obtenerRespuesta(ruta, peticion);
            }
            , obtenerRespuesta(ruta, peticion)
            {
                let me = this;
                peticion.then(function (response) {
                    let respuesta = response.data;
                    switch (ruta) {
                        case me.segmentoRuta +'/simple':
                            me.segmentoArr = respuesta.segmentos;
                            break;
                        case me.indicadorActividadRuta +'/basico_planes_accion':
                            me.planAccionArr = respuesta.planesAccion;
                            break;
                        case me.proyectoRuta +'/simple':
                            me.proyectoArr = respuesta.proyectos;
                            break;
                        case me.gestionActividadRuta:
                            me.actividadesArr = respuesta.actividades.data;
                            me.collSegs = respuesta.collSegs;
                            me.actividadPagin = respuesta.pagination;
                            break;
                        case me.actividadSeguimientoRuta +'/registrar':
                            me.collSegs[me.id][me.abrev].id = respuesta.idSeguimiento;
                            me.collSegs[me.id][me.abrev].estilo = 'btn btn-success btn-sm';
                            me.id = respuesta.idSeguimiento;
                            me.abrev = '';
                            Swal.mixin({toast : true, position : 'top', showConfirmButton : false, timer : 3000})
                            .fire({type : 'success', title : 'Seguimiento registrado'});
                            break;
                        case me.actividadSeguimientoRuta + '/':
                            let actividadSeg = respuesta.actividadSeguimiento;
                            me.seguimientoBody.noAplica = !actividadSeg.aplica;
                            me.seguimientoBody.fechaReporte = actividadSeg.fechaReporte;
                            me.seguimientoBody.observacion = actividadSeg.observacion;
                            me.evidenciaListar();
                            break;
                        case me.actividadSeguimientoRuta +'/actualizar/':
                            Swal.mixin({toast : true, position : 'top', showConfirmButton : false, timer : 3000})
                            .fire({type : 'success', title : 'Seguimiento actualizado'});
                            break;
                        case me.actividadEvidenciaRuta:
                            me.evidenciaArr = respuesta.seguimientoEvidencias;
                            break;
                        case me.actividadEvidenciaRuta +'/descartar/':
                            me.evidenciaArr.splice(me.indDescartar, 1);
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
            this.actividadObjCons.anio = this.fechaActual.split('-')[0];
            this.actividadListar();
            this.basicoPlanesAccion();
            this.segmentoSimple();
            this.proyectoSimple();
            $('#seguimiento-evidencia-mdl').on('shown.bs.modal', () => this.$refs.seguimientoBObservacion.focus());
        }
    }
</script>
<style>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .circulo{
        width: 1.5em;
        height: 1.5em;
    }
</style>