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
                            <i class="fa fa-exchange fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;Solicitudes&nbsp;&nbsp;
                            <button type="button" @click="abrirModal(solicitudRuta +'Registrar')" class="btn btn-pill btn-primary btn-sm" v-if="permisoCrear/*permisosUser.crear*/">
                                <i class="icon-plus" aria-hidden="true"></i>&nbsp;Nuevo
                            </button>
                            <button type="button" class="btn btn-pill btn-secondary btn-sm" v-else>
                                <i class="icon-plus" aria-hidden="true"></i>&nbsp;Nuevo
                            </button>&nbsp;
                            <a type="button" :href="descargaRuta" target="_blank" class="btn rounded btn-info btn-sm" v-if="permisoCrear">
                                <i class="fa fa-download" aria-hidden="true"></i>&nbsp;Descargar formato
                            </a>
                            <button type="button" class="btn btn-secondary btn-pill btn-sm" v-else>
                                <i class="fa fa-download" aria-hidden="true"></i>&nbsp;Descargar formato
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-auto my-1 pr-1">Segmento</label>
                        <div class="col-md-2 pl-1">
                            <select class="form-control" v-model="segmentoId" @change="segmentoCarpetaId = ''">
                                <option value="">Seleccione</option>
                                <option v-for="segmento in segmentoArr" :key="segmento.id" :value="segmento.id" v-text="segmento.segmento"></option>
                            </select>
                        </div>
                        <label class="col-md-auto my-1 pr-1">Carpeta</label>
                        <div class="col-md-2 pl-1">
                            <select class="form-control" v-model="segmentoCarpetaId" @change="solicitudObjCons.fkIdSegmentosArchivos = ''">
                                <option value="">Seleccione</option>
                                <option v-for="segmentoCarpeta in segmentoCarpetaArr[segmentoId]" :key="segmentoCarpeta.id" :value="segmentoCarpeta.id" v-text="segmentoCarpeta.carpeta"></option>
                            </select>
                        </div>
                        <label class="col-md-auto my-1 pr-1">Formato</label>
                        <div class="col-md-2 pl-1">
                            <select class="form-control" v-model="solicitudObjCons.fkIdSegmentosArchivos" @change="permisoYDescarga()">
                                <option value="">Seleccione</option>
                                <option v-for="segmentoArchivo in segmentoArchivoArr[segmentoCarpetaId]" :key="segmentoArchivo.id" :value="segmentoArchivo.id" v-text="segmentoArchivo.archivo"></option>
                            </select>
                        </div>
                        <label class="col-md-auto my-1 pr-1">Ver</label>
                        <div class="col-md-auto pl-1">
                            <select class="form-control" @change="cambiarPagina(1, solicitudObjCons, solicitudPagin, 1)" v-model="solicitudObjCons.numRegs">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <button type="button" @click="cambiarPagina(1, solicitudObjCons, solicitudPagin, 1)" class="btn btn-primary btn-pill btn-sm ml-2"><i class="fa fa-search"></i>&nbsp;Buscar</button>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-11">
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Usuario</th>
                                    <th class="text-center">Fecha</th>
                                    <th class="text-center">Recibe</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Opciones</th>
                                </thead>
                                <tbody>
                                    <template v-if="solicitudArr.length == 0">
                                        <tr>
                                            <td class="text-center" colspan="6">No hay registros</td>
                                        </tr>
                                    </template>
                                    <template v-else>
                                        <tr v-for="(solicitud, index) in solicitudArr" :key="solicitudArr.id">
                                            <td class="text-center" v-text="index + 1"></td>
                                            <td v-text="solicitud.usuario"></td>
                                            <td class="text-center" v-text="solicitud.fecha.split(' ')[0].split('-').reverse().join('/')"></td>
                                            <td v-text="solicitud.recibe"></td>
                                            <td class="text-center">
                                                <span class="badge" :class="'badge-' + (solicitud.estado == 1 ? 'success': solicitud.estado == 0 ? 'danger' : 'light')" v-text="solicitud.estado == 1 ? 'Activo': solicitud.estado == 0 ? 'Inactivo' : 'Finalizado'"></span>
                                            </td>
                                            <td class="text-center">
                                                <a type="button" title="Visualizar" :href="solicitud.ruta" target="_blank" class="btn rounded btn-info btn-sm rutabtn" v-if="solicitud.estado"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                                <a type="button" href="#" class="btn rounded btn-secondary btn-sm" v-else><i class="fa fa-eye" aria-hidden="true"></i></a>
                                                <button type="button" @click="abrirModal(solicitudRuta +'Actualizar', solicitud)" class="btn rounded btn-warning btn-sm" title="Editar" v-if="(solicitud.fk_usuCrea_users == id) && solicitud.estado == 1">
                                                    <i class="icon-pencil" aria-hidden="true"></i>
                                                </button>
                                                <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                    <i class="icon-pencil" aria-hidden="true"></i>
                                                </button>
                                                <button type="button" @click="actualizarEstado(solicitud.estado, 'esta solicitud', solicitudRuta, solicitud.id)" class="btn rounded btn-sm" :class="'btn-'+ (solicitud.estado ? 'danger':'success')" :title="solicitud.estado ? 'Anular':'Activar'" v-if="solicitud.estado != 2 && permisosUser.anular">
                                                    <i :class="'icon-'+ (solicitud.estado ? 'trash':'check')" aria-hidden="true"></i>
                                                </button>
                                                <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                    <i :class="'icon-'+ (solicitud.estado ? 'trash':'check')" aria-hidden="true"></i>
                                                </button>
                                                <!-- RESPONDER SOLICITUD -->
                                                <button type="button" class="btn rounded btn-info btn-sm" title="Responder" @click="abrirModal(solicitudRuta +'Responder', {destinatarioId: solicitud.fk_usuCrea_users})" v-if="solicitud.fk_recibe_users == id && solicitud.estado == 1">
                                                    <i class="icon-envelope" aria-hidden="true"></i>
                                                </button>
                                                <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                    <i class="icon-envelope" aria-hidden="true"></i>
                                                </button>
                                                <!-- FINALIZAR SOLICITUD -->
                                                <button type="button" class="btn rounded btn-info btn-sm" title="Finalizar" @click="solicitudFinalizar(solicitudRuta, solicitud.id)" v-if="solicitud.fk_recibe_users == id && solicitud.estado == 1">
                                                    <i class="fa fa-flag-checkered" aria-hidden="true"></i>
                                                </button>
                                                <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                    <i class="fa fa-flag-checkered" aria-hidden="true"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                            <nav>
                                <ul class="pagination">
                                    <li class="page-item" v-if="solicitudPagin.current_page > 1">
                                        <a class="page-link" href="#" @click.prevent="cambiarPagina(solicitudPagin.current_page - 1, solicitudObjCons, solicitudPagin, 1)">Ant</a>
                                    </li>
                                    <li class="page-item" v-for="page in solicitudPagesNumber" :key="page" :class="[page == solicitudPagin.current_page ? 'active' : '']">
                                        <a class="page-link" href="#" @click.prevent="cambiarPagina(page, solicitudObjCons, solicitudPagin, 1)" v-text="page"></a>
                                    </li>
                                    <li class="page-item" v-if="solicitudPagin.current_page < solicitudPagin.last_page">
                                        <a class="page-link" href="#" @click.prevent="cambiarPagina(solicitudPagin.current_page + 1, solicitudObjCons, solicitudPagin, 1)">Sig</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="solicitud-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-primary" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="solicitudTituloMdl +' solicitud'"></h4>
                        <button type="button" class="close" @click="solicitudMdlCerrar()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row justify-content-center">
                            <label class="col-md-3 my-1">Observación</label>
                            <div class="col-md-8 pl-1">
                                <textarea ref="observacion" class="form-control" rows="3" v-model="observacion" :class="{'is-invalid' : noHayObservacion}"></textarea>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <label class="col-md-3 my-1">Recibe</label>
                            <div class="col-md-8 pl-1">
                                <select class="form-control" v-model="solicitudBody.fk_recibe_users" :class="{'is-invalid' : noHayRecibe}">
                                    <option value="">Seleccione</option>
                                    <option v-for="user in userArr" :key="user.id" :value="user.id" v-text="user.nombre_completo"></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <label class="col-md-3 my-1">Subir formato</label>
                            <div class="col-md-8 pl-1">
                                <input type="file" class="form-control form-control-file" id="inputArchivo" @change="archivoObtener" :class="{'is-invalid' : noHayRuta}">
                            </div>
                        </div>
                        <div class="form-group row justify-content-center"><div class="col-md-11"><hr></div></div>
                        <template v-for="categoriaConOpciones in categoriaConOpcionesArr">
                            <div class="form-group row justify-content-center">
                                <label class="col-md-3 my-1" v-text="categoriaConOpciones.categoriaArchivo"></label>
                                <div class="col-md-8 pl-1">
                                    <select class="form-control" v-model="categoriaConOpciones.seleccionado" :class="{'is-invalid' : solicitudOnVacios && !categoriaConOpciones.seleccionado}">
                                        <option value="">Seleccione</option>
                                        <option v-for="categoriaOpcion in categoriaConOpciones.categorias_opciones" :key="categoriaOpcion.id" :value="categoriaOpcion.id" v-text="categoriaOpcion.categoriaOpcion"></option>
                                    </select>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-pill btn-danger btn-sm" @click="solicitudMdlCerrar()">Cerrar</button>
                        <button type="button" class="btn btn-pill btn-success btn-sm" @click="tipoAccion == 'Guardar' ? solicitudRegistrar() : solicitudActualizar()" v-text="tipoAccion"></button>
                    </div>
                </div>
            </div>
        </div>
        <div id="feedback-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-primary" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="'Solicitud - '+ solicitudTituloMdl"></h4>
                        <button type="button" class="close" @click="solicitudFeedBackMdlCerrar()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row justify-content-center">
                            <div class="form-group col-md-10">
                                <label>Observación</label>
                                <textarea ref="observacionFeedBack" class="form-control" :class="{'is-invalid': noHayObservacion}" rows="5" v-model="observacion"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-pill btn-danger btn-sm" @click="solicitudFeedBackMdlCerrar()">Cerrar</button>
                        <button type="button" class="btn btn-pill btn-success btn-sm" @click="solicitudFeedBackEnviar()">Enviar</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>
<script>
    export default {
        props : ['permisosUser', 'ruta', 'axiosApp']
        , data() {
            return {
                solicitudRuta : 'solicitudes'
                , segmentoRuta : 'segmentos'
                , segmentoCarpetaRuta : 'segmentos_carpetas'
                , segmentoArchivoRuta : 'segmentos_archivos'
                , categoriaArchivoRuta : 'categorias_archivos'
                , userRuta : 'users'
                , descargaRuta : ''

                , segmentoArr : []
                , segmentoCarpetaArr : []
                , segmentoArchivoArr : []
                , permisoCrear : 0
                , userArr : []

                , segmentoId : ''
                , segmentoCarpetaId : ''
                , solicitudObjCons : {
                    '?page' : 1
                    , fkIdSegmentosArchivos : ''
                    , numRegs : 10
                }
                , categoriaConOpcionesArr : []
                , filtroOnVacios : false
                
                , id : 0
                , observacion : ''
                , destinatarioId : 0

                , solicitudArr : []
                , solicitudBody : {
                    fk_recibe_users : ''
                    , ruta : ''
                    , fk_id_segmentos_archivos : ''
                }
                , solicitudId : 0
                , solicitudOnVacios : false

                , categoriaArr : []

                , solicitudTituloMdl : 'Registrar'
                , tipoAccion : 'Guardar'

                , solicitudPagin : {
                    total : 0
                    , current_page : 0
                    , per_page : 0
                    , last_page : 0
                    , from : 0
                    , to : 0
                }
                , offset : 3
                , tituloSwal : ''
                , descripcionSwal : ''
            }
        }
        , computed :
        {
            // VALIDACIÓN DE CAMPOS VACIOS EN FORMULARIOS
            //Campos solicitud
            noHayObservacion : function ()
            {
                return this.solicitudOnVacios && !this.observacion.trim();
            }
            , noHayRecibe : function ()
            {
                return this.solicitudOnVacios && !this.solicitudBody.fk_recibe_users;
            }
            , noHayRuta : function ()
            {
                return this.solicitudOnVacios && !this.solicitudBody.ruta;
            }
            , solicitudPagesNumber: function ()
            {
                if (!this.solicitudPagin.to) {
                    return [];
                }
                let from = this.solicitudPagin.current_page - this.offset; 
                if (from < 1) {
                    from = 1;
                }
                let to = from + (this.offset * 2); 
                if (to >= this.solicitudPagin.last_page) {
                    to = this.solicitudPagin.last_page;
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
            abrirModal (accion, data = {}) {
                switch (accion) {
                    case this.solicitudRuta +'Registrar':
                        $('#solicitud-mdl').modal('show');
                        break;
                    case this.solicitudRuta +'Actualizar':
                        this.solicitudTituloMdl = 'Editar';
                        this.solicitudId = data.id;
                        this.solicitudBody.fk_recibe_users = data.fk_recibe_users; // TOMAR EL NOMBRE O DESCRIPCION EN LA CONSULTA
                        this.solicitudBody.ruta = data.ruta;
                        this.solicitudOpciones();
                        this.tipoAccion = 'Actualizar';
                        $('#solicitud-mdl').modal('show');
                        break;
                    case this.solicitudRuta +'Responder':
                        this.solicitudTituloMdl = '(Nombre pendiente)';
                        this.destinatarioId = data.destinatarioId;
                        $('#feedback-mdl').modal('show');
                        break;
                }
            }
            , cambiarPagina (page, refObjCons, refPagin, listar)
            {
                //Actualiza la página actual
                refObjCons['?page'] = refPagin.current_page = page;
                //Envia la petición para visualizar la data de esa página
                switch (listar) {
                    case 1: this.solicitudListar();break;
                    //...
                }
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
                    if (ruta.endsWith('desactivar/') || ruta.endsWith('activar/')) Swal.fire(me.tituloSwal, me.descripcionSwal, 'success');
                    switch (ruta) {
                        case me.segmentoRuta +'/simple':
                            me.segmentoArr = respuesta.segmentos;
                            break;
                        case me.segmentoCarpetaRuta +'/simple_agrupada':
                            me.segmentoCarpetaArr = respuesta.segmentosCarpetas;
                            break;
                        case me.segmentoArchivoRuta +'/simple_agrupada_con_permiso':
                            me.segmentoArchivoArr = respuesta.segmentosArchivos;
                            break;
                        case me.categoriaArchivoRuta +'/simple_con_opciones_simple':
                            me.categoriaConOpcionesArr = respuesta.categoriasConOpciones;
                            break;
                        case me.userRuta +'/simple_excluido':
                            me.userArr = respuesta.usuarios;
                            break;
                        case me.userRuta +'/id':
                            me.id = respuesta.id;
                            break;
                        case me.solicitudRuta:
                            me.solicitudArr = respuesta.solicitudes.data;
                            me.solicitudPagin = respuesta.pagination;
                            break;
                        case me.solicitudRuta +'_opciones/':
                            me.categoriaConOpcionesArr.forEach((categoriaConOpciones) => {
                                respuesta.opcionesRegistradas.forEach((opcion) => {
                                    if (opcion.fk_id_categorias_archivos == categoriaConOpciones.id) {
                                        categoriaConOpciones.seleccionado = opcion.id;
                                    }
                                });
                            });
                            break;
                        case me.solicitudRuta +'/desactivar/':
                        case me.solicitudRuta +'/activar/':
                            me.solicitudListar();
                            break;
                        case me.solicitudRuta +'/finalizar/':
                            me.solicitudListar();
                            break;
                    }
                })
                .catch(error => console.log(error));
            }
            , peticionGet (ruta, cadenaConsulta = '')
            {
                let peticion = this.axiosApp.get(ruta + cadenaConsulta);
                this.obtenerRespuesta(ruta, peticion);
            }
            , peticionPost (ruta, recursoBody, validar)
            {
                if (this.esRecursoInvalido(validar))return;
                let peticion = this.axiosApp.post(ruta, recursoBody);
                this.obtenerRespuesta(ruta, peticion);
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
            // METODOS FILTRO

            , segmentoSimple ()
            {
                let ruta = this.segmentoRuta +'/simple', cadCons = `?leer=${this.permisosUser.leer}`;
                this.peticionGet(ruta, cadCons);
            }
            , segmentoCarpetaSimpleAgrupada ()
            {
                let ruta = this.segmentoCarpetaRuta +'/simple_agrupada';
                this.peticionGet(ruta);
            }
            , segmentoArchivoSimpleAgrupadaConPermiso ()
            {
                let ruta = this.segmentoArchivoRuta +'/simple_agrupada_con_permiso';
                this.peticionGet(ruta);
            }
            , permisoYDescarga ()
            {
                if (!this.solicitudObjCons.fkIdSegmentosArchivos) {
                    this.permisoCrear = 0;
                    this.descargaRuta = this.solicitudBody.fk_id_segmentos_archivos = '';
                    return;
                }
                let seleccionado = this.segmentoArchivoArr[this.segmentoCarpetaId].find((segmentoArchivo) => {
                    return segmentoArchivo.id == this.solicitudObjCons.fkIdSegmentosArchivos;
                }, this);
                this.permisoCrear = seleccionado.crear;
                this.descargaRuta = seleccionado.ruta;
                this.solicitudBody.fk_id_segmentos_archivos = this.solicitudObjCons.fkIdSegmentosArchivos;

                // SOLICITA LAS CATEGORIAS CON SUS RESPECTIVAS OPCIONES PARA MOSTRAR EN EL FORMULARIO DE REGISTRO/ACTUALIZACION
                if (this.permisoCrear) {
                    let ruta = this.categoriaArchivoRuta +'/simple_con_opciones_simple';
                    this.peticionGet(ruta, '?fkIdSegmentosArchivos='+ this.solicitudObjCons.fkIdSegmentosArchivos);
                }
            }
            , userSimpleExcluido ()
            {
                let ruta = this.userRuta +'/simple_excluido';
                this.peticionGet(ruta);
            }
            , actorId ()
            {
                let ruta = this.userRuta +'/id';
                this.peticionGet(ruta);
            }
            
            // METODOS RECURSO SOLICITUD
            , archivoObtener (event)
            {
                this.solicitudBody.ruta = event.target.files[0];
            }
            , solicitudListar ()
            {
                this.peticionGet(this.solicitudRuta, this.obtenerCadCons(this.solicitudObjCons));
            }
            , solicitudValidar ()
            {
                let faltanOpciones = 0;
                this.categoriaConOpcionesArr.forEach((categoriaConOpciones) => {
                    if (!categoriaConOpciones.seleccionado) faltanOpciones = 1;
                });
                return this.noHayObservacion || this.noHayRecibe || this.noHayRuta || faltanOpciones;
            }
            , solicitudRegistrar ()
            {
                this.solicitudOnVacios = true;
                if (this.solicitudValidar()) return;
                let solicitudBody = new FormData(), ruta = this.solicitudRuta +'/registrar', me = this;
                solicitudBody.append('fk_recibe_users', this.solicitudBody.fk_recibe_users);
                solicitudBody.append('ruta', this.solicitudBody.ruta);
                solicitudBody.append('fk_id_segmentos_archivos', this.solicitudBody.fk_id_segmentos_archivos);
                solicitudBody.append('opciones', JSON.stringify(this.categoriaConOpcionesArr.map(categoriaConOpciones => categoriaConOpciones.seleccionado)));

                this.axiosApp.post(ruta, solicitudBody).then(function (response) {
                    me.axiosApp.post(me.solicitudRuta +'/notificar', {destinatarioId: me.solicitudBody.fk_recibe_users, observacion: me.observacion}).catch(error => console.log(error));
                    me.solicitudMdlCerrar();
                    me.solicitudListar();
                })
                .catch(error => console.log(error));
            }
            , solicitudOpciones ()
            {
                let ruta = this.solicitudRuta +'_opciones/';
                this.peticionGet(ruta, this.solicitudId);
            }
            , solicitudActualizar ()
            {
                this.solicitudOnVacios = true;
                if (this.solicitudValidar()) return;
                let solicitudBody = new FormData(), ruta = this.solicitudRuta +'/actualizar/'+ this.solicitudId, me = this;
                solicitudBody.append('fk_recibe_users', this.solicitudBody.fk_recibe_users);
                solicitudBody.append('ruta', this.solicitudBody.ruta);
                solicitudBody.append('opciones', JSON.stringify(this.categoriaConOpcionesArr.map(categoriaConOpciones => categoriaConOpciones.seleccionado)));
                solicitudBody.append('_method', 'PUT');

                this.axiosApp.post(ruta, solicitudBody, {headers:{'Content-Type':'multipart/form-data'}})
                .then(function (response) {
                    me.axiosApp.post(me.solicitudRuta +'/notificar', {destinatarioId: me.solicitudBody.fk_recibe_users, observacion: me.observacion}).catch(error => console.log(error));
                    me.solicitudMdlCerrar();
                    me.solicitudListar();
                })
                .catch(error => console.log(error));
            }
            , actualizarEstado (recursoEstado, finalTitle, recursoRuta, recursoId)
            {
                let prefijo = recursoEstado ? 'des' : '';
                Swal.fire({
                    title: `Esta seguro de ${prefijo}activar ${finalTitle}?`
                    , type: 'warning'
                    , showCancelButton: true
                    , confirmButtonText: 'Aceptar!'
                    , cancelButtonText: 'Cancelar'
                    , customClass : { confirmButton: 'btn btn-pill btn-success btn-lg', cancelButton: 'btn btn-pill btn-danger btn-lg'}
                    , buttonsStyling: false
                    , reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        let ruta = `${recursoRuta}/${prefijo}activar/`;
                        this.estadoPatch(ruta, recursoId, recursoEstado);
                    } else if (result.dismiss === Swal.DismissReason.cancel /* Read more about handling dismissals */) {}
                });
            }
            , solicitudFinalizar (recursoRuta, recursoId)
            {
                Swal.fire({
                    title: 'Esta seguro de finalizar esta solicitud'
                    , type: 'warning'
                    , showCancelButton: true
                    , confirmButtonText: 'Aceptar!'
                    , cancelButtonText: 'Cancelar'
                    , customClass : { confirmButton: 'btn btn-pill btn-success btn-lg', cancelButton: 'btn btn-pill btn-danger btn-lg'}
                    , buttonsStyling: false
                    , reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        let ruta = recursoRuta +'/finalizar/'+ recursoId, me = this;
                        this.axiosApp.patch(ruta).then(response => {
                            me.solicitudListar();
                            Swal.fire('Finalizado!', 'Solicitud finalizada con éxito', 'success');
                        }).catch(error => console.log(error));
                    } else if (result.dismiss === Swal.DismissReason.cancel) {}
                });
            }
            , solicitudMdlCerrar ()
            {
                $('#solicitud-mdl').modal('hide');
                this.solicitudTituloMdl = 'Registrar';
                this.observacion = this.solicitudBody.fk_recibe_users = this.solicitudBody.ruta = '';
                $('#inputArchivo').val('');
                this.categoriaConOpcionesArr.forEach(categoriaConOpciones => categoriaConOpciones.seleccionado = '');
                this.solicitudOnVacios = false;
                this.tipoAccion = 'Guardar';
            }

            , solicitudFeedBackEnviar ()
            {
                this.solicitudOnVacios = true;
                if (this.noHayObservacion) return;
                this.axiosApp.post(me.solicitudRuta +'/notificar', {destinatarioId: this.destinatarioId, observacion: this.observacion}).catch(error => console.log(error));
            }
            , solicitudFeedBackMdlCerrar ()
            {
                $('#feedback-mdl').modal('hide');
                this.solicitudTituloMdl = this.observacion = '';
                this.destinatarioId = 0;
                this.solicitudOnVacios = false;
            }
        }
        , mounted ()
        {
            this.segmentoSimple();
            this.segmentoCarpetaSimpleAgrupada();
            this.segmentoArchivoSimpleAgrupadaConPermiso();
            this.userSimpleExcluido();
            this.actorId();
            $('#solicitud-mdl').on('shown.bs.modal', () => this.$refs.observacion.focus());
            $('#feedback-mdl').on('shown.bs.modal', () => this.$refs.observacionFeedBack.focus());
            /*console.log(this.axiosApp.defaults);
            console.log(axios.defaults);*/
        }
    }
</script>