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
                    <div class="row">
                        <div class="col-md-auto">
                            <i class="fa fa-id-badge fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;Cargos&nbsp;&nbsp;
                            <button type="button" @click="abrirModal(cargoRuta +'Registrar')" class="btn btn-pill btn-primary btn-sm" v-if="permisosUser.crear">
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
                        <label class="col-md-auto my-1 pr-1">Columna</label>
                        <div class="col-md-4 pl-1">
                            <div class="input-group">
                                <div class="input-group-btn">
                                    <select class="form-control" @change="$refs.cargoOCTextoCol.focus()" v-model="cargoObjCons.nombreCol">
                                        <option value="cargo">Cargo</option>
                                        <option value="descripcion">Descripción</option>
                                    </select>
                                </div>
                                <input type="text" ref="cargoOCTextoCol" v-model="cargoObjCons.textoCol" @keyup.enter="cambiarPagina(1, cargoObjCons, cargoPagin, 1)" class="form-control">
                            </div>
                        </div>
                        <label class="col-md-auto my-1 pr-1">Ver</label>
                        <div class="col-md-auto pl-1">
                            <select class="form-control" @change="cambiarPagina(1, cargoObjCons, cargoPagin, 1)" v-model="cargoObjCons.numRegs">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <button type="button" @click="cambiarPagina(1, cargoObjCons, cargoPagin, 1);$refs.cargoOCTextoCol.focus();" class="btn btn-pill btn-primary btn-sm ml-2"><i class="fa fa-search"></i>&nbsp;Buscar</button>
                    </div>
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Cargo</th>
                                <th class="text-center">Descripción</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Documentos</th>
                                <th class="text-center">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-if="cargoArr.length == 0">
                                <tr>
                                    <td class="text-center" colspan="6">No hay registros</td>
                                </tr>
                            </template>
                            <template v-else>
                                <tr v-for="(cargo, index) in cargoArr" :key="cargo.id">
                                    <td class="text-center" v-text="index + 1"></td>
                                    <td v-text="cargo.cargo"></td>
                                    <td v-text="cargo.descripcion"></td>
                                    <td class="text-center">
                                        <span class="badge" :class="'badge-' + (cargo.estado ? 'success':'danger')" v-text="cargo.estado ? 'Activo':'Inactivo'"></span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" @click="abrirModal(documentoRuta, {cargoId: cargo.id, cargo: cargo.cargo})" class="btn rounded btn-info btn-sm" title="Ver documentos" v-if="cargo.estado">
                                            <i class="fa fa-files-o" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                            <i class="fa fa-files-o" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" @click="abrirModal(cargoRuta +'Actualizar', cargo)" class="btn rounded btn-warning btn-sm" title="Editar" v-if="cargo.estado && permisosUser.actualizar">
                                            <i class="icon-pencil" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                            <i class="icon-pencil" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" @click="actualizarEstado(cargo.estado, 'este cargo', cargoRuta, cargo.id)" class="btn rounded btn-sm" :class="'btn-'+ (cargo.estado ? 'danger':'success')" :title="cargo.estado ? 'Anular':'Activar'" v-if="permisosUser.anular">
                                            <i :class="'icon-'+ (cargo.estado ? 'trash':'check')" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                            <i :class="'icon-'+ (cargo.estado ? 'trash':'check')" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </tr>                                
                            </template>
                        </tbody>
                    </table>
                    <nav>
                        <ul class="pagination">
                            <li class="page-item" v-if="cargoPagin.current_page > 1">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(cargoPagin.current_page - 1, cargoObjCons, cargoPagin, 1)">Ant</a>
                            </li>
                            <li class="page-item" v-for="page in cargoPagesNumber" :key="page" :class="[page == cargoPagin.current_page ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(page, cargoObjCons, cargoPagin, 1)" v-text="page"></a>
                            </li>
                            <li class="page-item" v-if="cargoPagin.current_page < cargoPagin.last_page">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(cargoPagin.current_page + 1, cargoObjCons, cargoPagin, 1)">Sig</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <!-- Fin ejemplo de tabla Listado -->
        </div>
        <div id="cargo-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-primary" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="cargoTituloMdl+ ' cargo'"></h4>
                        <button type="button" class="close" @click="cargoCerrarMdl()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form @submit="tipoAccion == 'Guardar' ? cargoRegistrar() : cargoActualizar()">
                            <div class="form-group row justify-content-center">
                                <label class="col-md-2 form-control-label">Cargo</label>
                                <div class="col-md-9">
                                    <input type="text" ref="cargoBCargo" v-model="cargoBody.cargo" class="form-control" :class="{'is-invalid' : noHayCargo}">
                                </div>
                            </div>
                            <div class="form-group row justify-content-center">
                                <label class="col-md-2 form-control-label">Descripción</label>
                                <div class="col-md-9">
                                    <input type="text" ref="cargocbdescripcion" v-model="cargoBody.descripcion" class="form-control" :class="{'is-invalid' : noHayDescripcion}">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-pill btn-danger btn-sm" @click="cargoCerrarMdl()">Cerrar</button>
                        <button type="button" class="btn btn-pill btn-success btn-sm" @click="tipoAccion == 'Guardar'? cargoRegistrar() : cargoActualizar()" v-text="tipoAccion"></button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <div id="documentocrud-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="cargoTituloMdl"></h4>
                        <button type="button" class="close" @click="documentoMdlCerrar()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-auto">
                                        <i class="fa fa-files-o" aria-hidden="true"></i>&nbsp;Documentos&nbsp;
                                    </div>
                                    <button type="button" @click="abrirModal(documentoRuta +'Registrar')" class="btn btn-pill btn-primary btn-sm" v-if="permisosUser.crear">
                                        <i class="icon-plus"></i>&nbsp;Nuevo
                                    </button>
                                    <button type="button" class="btn btn-pill btn-secondary btn-sm" v-else>
                                        <i class="icon-plus"></i>&nbsp;Nuevo
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-md-auto my-1 pr-1">Documento</label>
                                    <div class="col-md-3 pl-1">
                                        <input type="text" ref="documentoOCdocumento" v-model="documentoObjCons['?documento']" @keyup.enter="documentoListar()" class="form-control">
                                    </div>
                                    <label class="col-md-auto my-1 pr-1">Estado</label>
                                    <div class="col-md-2 pl-1">
                                        <select class="form-control" v-model="documentoObjCons.estado">
                                            <option value="">Todos</option>
                                            <option value="1">Activo</option>
                                            <option value="0">Inactivo</option>
                                        </select>                                        
                                    </div>
                                    <button type="button" @click="documentoListar()" class="btn btn-pill btn-primary btn-sm ml-2"><i class="fa fa-search"></i>&nbsp;Buscar</button>
                                </div>
                                <table class="table table-bordered table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Documento</th>
                                            <th class="text-center">Estado</th>
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
                                                <td class="text-center">
                                                    <span class="badge" :class="'badge-'+ (documento.estado?'success': 'danger')" v-text="documento.estado?'Activo' :'Inactivo'"></span>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" @click="abrirModal(documentoRuta +'Actualizar', documento)" class="btn rounded btn-warning btn-sm" title="Editar" v-if="permisosUser.actualizar && documento.estado">
                                                        <i class="icon-pencil" aria-hidden="true"></i>
                                                    </button>
                                                    <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                        <i class="icon-pencil" aria-hidden="true"></i>
                                                    </button>
                                                    <button type="button" @click="actualizarEstado(documento.estado, 'este documento', documentoRuta, documento.id)" class="btn rounded btn-sm" :class="'btn-'+ (documento.estado?'danger': 'success')" :title="documento.estado?'Anular': 'Activar'" v-if="permisosUser.anular">
                                                        <i :class="'icon-'+ (documento.estado?'trash': 'check')" aria-hidden="true"></i>
                                                    </button>
                                                    <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                        <i :class="'icon-'+ (documento.estado?'trash': 'check')" aria-hidden="true"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-pill btn-primary btn-sm" @click="documentoMdlCerrar()"><i class="fa fa-reply" aria-hidden="true"></i>&nbsp;Atrás</button>
                    </div>
                </div>
            </div>
        </div>
        <!--Fin del modal-->
        <!-- Inicio modal documentos CU-->
        <div id="documento-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-primary" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="documentoTituloMdlMdl +' documento'"></h4>
                        <button type="button" class="close" @click="documentoMdlMdlCerrar()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="tipoAccion == 'Guardar' ? documentoRegistrar() : documentoActualizar()">
                            <div class="form-group row justify-content-center">
                                <label class="col-md-3 my-1 pr-1">Documento</label>
                                <div class="col-md-7 pl-1">
                                    <input type="text" ref="documentoBDocumento" class="form-control" :class="{'is-invalid' : noHayDocumento}" v-model="documentoBody.documento">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-pill btn-danger btn-sm" @click="documentoMdlMdlCerrar()">Cerrar</button>
                        <button type="button" class="btn btn-pill btn-success btn-sm" @click="tipoAccion == 'Guardar' ? documentoRegistrar() : documentoActualizar()" v-text="tipoAccion"></button>
                    </div>
                </div>
            </div>
        </div>
        <!--Fin del modal-->
    </main>
</template>
<script>
    export default {
        props : ['permisosUser', 'ruta', 'axiosApp']
        , data () {
            return {
                cargoRuta : 'cargos'
                , documentoRuta : 'cargos_documentos'
                
                , cargoObjCons : {
                    '?page' : 1
                    , leer : this.permisosUser.leer
                    , nombreCol : 'cargo'
                    , textoCol : ''
                    , numRegs : 10
                }

                , cargoArr : []
                , cargoId: 0
                , cargoBody : {
                    cargo : ''
                    , descripcion : ''
                }
                , cargoOnVacios : false
                , cargoPagin : {
                    total : 0
                    , current_page : 0
                    , per_page : 0
                    , last_page : 0
                    , from : 0
                    , to : 0
                }
                , offset : 3

                , cargoTituloMdl : 'Registrar'
                , tipoAccion : 'Guardar'
                //, onFiltros : false
                , tomarFiltro : false

                , documentoObjCons : {
                    '?documento' : ''
                    , fk_id_cargos : 0
                    , estado : ''
                }
                , documentoArr : []
                , documentoBody : {
                    documento : ''
                    , fk_id_cargos : 0
                }
                , documentoId : 0
                , documentoOnVacios : false
                , documentoTituloMdlMdl : 'Registrar'
            }
        }
        , computed:
        {
            // VALIDACIÓN DE FILTRO
            /*noHayCampo : function () {
                return this.onFiltros && !this.cargoObjCons.nombreCol;
            }*/
            // VALIDACIÓN DE CAMPOS VACIOS EN FORMULARIOS
            //Campos cargo
            noHayCargo : function ()
            {
                return this.cargoOnVacios && !this.cargoBody.cargo.trim();
            }
            , noHayDescripcion : function ()
            {
                return this.cargoOnVacios && !this.cargoBody.descripcion.trim();
            }
            // Campos documento
            , noHayDocumento : function ()
            {
                return this.documentoOnVacios && !this.documentoBody.documento.trim();
            }
            //Calcula los elementos de la paginación
            , cargoPagesNumber: function ()
            {
                if (!this.cargoPagin.to) {
                    return [];
                }
                let from = this.cargoPagin.current_page - this.offset; 
                if (from < 1) {
                    from = 1;
                }
                let to = from + (this.offset * 2); 
                if (to >= this.cargoPagin.last_page) {
                    to = this.cargoPagin.last_page;
                }  
                let pagesArray = [];
                while (from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;
            }
        }
        , methods:
        {
            abrirModal (accion, data = {})
            {
                switch (accion) {
                    case this.cargoRuta +'Registrar':
                        if (this.tomarFiltro) this.cargoBody[this.cargoObjCons.nombreCol] = this.cargoObjCons.textoCol;
                        $('#cargo-mdl').modal('show');
                        break;
                    case this.cargoRuta +'Actualizar':
                        this.cargoTituloMdl = 'Editar';
                        this.tipoAccion = 'Actualizar';
                        this.cargoId = data.id;
                        this.cargoBody.cargo = data.cargo;
                        this.cargoBody.descripcion = data.descripcion;
                        $('#cargo-mdl').modal('show');
                        break;
                    case this.documentoRuta:
                        this.cargoTituloMdl = 'Cargo - '+ data.cargo;
                        //this.cargoId = data.cargoId;
                        this.documentoObjCons.fk_id_cargos = this.documentoBody.fk_id_cargos = data.cargoId;
                        this.documentoListar();
                        $('#documentocrud-mdl').modal('show');
                        break;
                    case this.documentoRuta +'Registrar':
                        $('#documentocrud-mdl').modal('hide');
                        $('#documento-mdl').modal('show');
                        break;
                    case this.documentoRuta +'Actualizar':
                        this.documentoTituloMdlMdl = 'Editar';
                        this.documentoId = data.id;
                        this.documentoBody.documento = data.documento;
                        this.tipoAccion = 'Actualizar';
                        $('#documentocrud-mdl').modal('hide');
                        $('#documento-mdl').modal('show');
                        break;
                }
            }
            , peticionGet (recursoRuta, cadCons = '')
            {
                let peticion = this.axiosApp.get(recursoRuta + cadCons);
                this.obtenerRespuesta(recursoRuta, peticion);
            }
            , peticionPost (recursoRuta, recursoBody)
            {
                if (this.cargoValidar()) return;
                let peticion = this.axiosApp.post(recursoRuta, recursoBody);
                this.obtenerRespuesta(recursoRuta, peticion);
            }
            , peticionPut (recursoRuta, recursoId, recursoBody)
            {
                if (this.cargoValidar()) return;
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
            , obtenerRespuesta(recursoRuta, peticion)
            {
                let me = this;
                peticion.then(function (response) {
                    let respuesta = response.data;
                    if (recursoRuta.endsWith('desactivar/') || recursoRuta.endsWith('activar/')) Swal.fire(me.tituloSwal, me.descripcionSwal, 'success');
                    switch (recursoRuta) {
                        case me.cargoRuta:
                            me.cargoArr = respuesta.cargos.data;
                            me.cargoPagin = respuesta.pagination;
                            break;
                        case me.cargoRuta +'/registrar':
                        case me.cargoRuta +'/actualizar/':
                            $('#cargo-mdl').modal('hide');
                            me.cargoCerrarMdl();
                            me.cargoListar();
                            break;
                        case me.cargoRuta +'/desactivar/':
                            if (respuesta.hayUsuarios) {
                                Swal.fire('No es posible desactivar!', 'Hay usuarios activos con este cargo', 'error');
                                return;
                            }
                            me.cargoListar();
                            break;
                        case me.cargoRuta +'/activar/':
                            me.cargoListar();
                            break;
                        case me.documentoRuta:
                            me.documentoArr = respuesta.cargoDocumentos;
                            break;
                        case me.documentoRuta +'/registrar':
                        case me.documentoRuta +'/actualizar/':
                            me.documentoMdlMdlCerrar();
                            me.documentoListar();
                            break;
                        case me.documentoRuta +'/desactivar/':
                            if (respuesta.hayDocumentos) {
                                Swal.fire('No es posible desactivar!', 'Hay documentos de usuarios asociados a este documento', 'error');
                                return;
                            }
                            me.documentoListar();
                            break;
                        case me.documentoRuta +'/activar/':
                            me.documentoListar();
                            break;
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
            }
            , cargoValidar ()
            {
                return this.noHayCargo || this.noHayDescripcion;
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
            , cambiarPagina (page, refObjCons, refPagin, listar)
            {
                //Actualiza la página actual
                refObjCons['?page'] = refPagin.current_page = page;
                this.tomarFiltro = true;
                //Envia la petición para visualizar la data de esa página
                switch (listar) {
                    case 1: this.cargoListar();break;
                    //...
                }
            }
            , obtenerCadCons (objCons)
            {
                return Object.entries(objCons).map(filtro => `${filtro[0]}=${filtro[1]}`).join('&');
            }

            // MÉTODOS PROPIOS DEL RECURSO CARGO
            , cargoCerrarMdl ()
            {
                $('#cargo-mdl').modal('hide');
                this.cargoBody.cargo = this.cargoBody.descripcion = '';
                this.cargoTituloMdl = 'Registrar';
                this.tipoAccion = 'Guardar';
                this.cargoOnVacios = this.tomarFiltro = false;
            }
            , cargoListar ()
            {
                this.peticionGet(this.cargoRuta, this.obtenerCadCons(this.cargoObjCons));
            }
            , cargoRegistrar ()
            {
                this.cargoOnVacios = true;
                let uri = this.cargoRuta +'/registrar';
                this.peticionPost(uri, this.cargoBody);
            }
            , cargoActualizar ()
            {
                this.cargoOnVacios = true;
                let uri = this.cargoRuta +'/actualizar/';
                this.peticionPut(uri, this.cargoId, this.cargoBody);
            }

            // MÉTODOS PROPIOS DEL RECURSO DOCUMENTO
            , documentoMdlCerrar ()
            {
                $('#documentocrud-mdl').modal('hide');
                this.cargoTituloMdl = 'Registrar';
                this.documentoObjCons.fk_id_cargos = this.documentoBody.fk_id_cargos = 0;
                this.documentoArr = [];
            }
            , documentoMdlMdlCerrar ()
            {
                this.documentoTituloMdlMdl = 'Registrar';
                this.documentoId = 0;
                this.documentoBody.documento = '';
                this.documentoOnVacios = false;
                this.tipoAccion = 'Guardar';
                $('#documento-mdl').modal('hide');
                $('#documentocrud-mdl').modal('show');
            }
            , documentoValidar ()
            {
                return this.noHayDocumento;
            }
            , documentoListar ()
            {
                this.peticionGet(this.documentoRuta, this.obtenerCadCons(this.documentoObjCons));
            }
            , documentoRegistrar ()
            {
                this.documentoOnVacios = true;
                if (this.documentoValidar()) return;
                let ruta = this.documentoRuta +'/registrar';
                this.peticionPost(ruta, this.documentoBody);
            }
            , documentoActualizar ()
            {
                this.documentoOnVacios = true;
                if (this.documentoValidar()) return;
                let ruta = this.documentoRuta +'/actualizar/';
                this.peticionPut(ruta, this.documentoId, this.documentoBody);
            }
        }
        , mounted ()
        {
            this.cargoListar();
            this.$refs.cargoOCTextoCol.focus();
            $('#cargo-mdl').on('shown.bs.modal', () => this.$refs.cargoBCargo.focus());
            $('#cargo-mdl, #documentocrud-mdl').on('hidden.bs.modal', () => this.$refs.cargoOCTextoCol.focus());
            $('#documentocrud-mdl').on('shown.bs.modal', () => this.$refs.documentoOCdocumento.focus());
            $('#documento-mdl').on('shown.bs.modal', () => this.$refs.documentoBDocumento.focus());
        }
    }
</script>
<style>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
</style>