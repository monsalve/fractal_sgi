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
                            <i class="fa fa-align-justify"></i>&nbsp;Niveles&nbsp;
                            <button id="nivelnuevo" type="button" @click="abrirModal('registrarNivel')" class="btn btn-pill btn-primary btn-sm" data-toggle="modal" data-target="#nivel-mdl" v-if="permisosUser.crear">
                                <i class="icon-plus"></i>&nbsp;Nuevo
                            </button>
                            <button type="button" class="btn btn-pill btn-secondary btn-sm" v-else>
                                <i class="icon-plus"></i>&nbsp;Nuevo
                            </button>
                        </div>
                        <div class="col-md-auto" v-if="this.padres.length > 1">
                            <button type="button" @click="ascenderNivel()" class="btn btn-pill btn-primary btn-sm">
                                <i class="fa fa-reply" aria-hidden="true"></i>&nbsp;Volver
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!--<div class="row justify-content-start">
                        <div class="alert alert-primary col-md-auto ml-3 px-1 py-1" role="alert">
                            Busca un código de causa
                        </div>
                    </div>-->
                    <div class="form-group row justify-content-start">
                        <label class="col-md-auto my-1 pr-1">Código causa</label>
                        <div class="col-md-2 pl-1">
                            <input type="text" ref="causaccCodigo" class="form-control" placeholder="Texto a buscar" v-model="codigo">
                        </div>
                        <button type="button" @click="buscarCausa();causaCcCodigo();" class="btn btn-pill btn-primary ml-2">
                            <i class="fa fa-search"></i>&nbsp;Buscar
                        </button>
                    </div>
                    <div class="row justify-content-start">
                        <div class="col-md-10">
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Nivel</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-center">Opciones</th>
                                    </tr>
                                </thead>
                                <tbody class="nivellista">
                                    <tr v-for="(nivel, index) in arrNiveles" :key="nivel.id">
                                        <td class="text-center" v-text="index + 1"></td>
                                        <td v-text="nivel.causaNovedad"></td>
                                        <td class="text-center">
                                            <div v-if="nivel.estado">
                                                <span class="badge badge-success">Activo</span>
                                            </div>
                                            <div v-else>
                                                <span class="badge badge-danger">Inactivo</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-primary btn-sm btn-nivel-causa" @click="abrirModal('elegir', {id:nivel.id, causaNovedad:nivel.causaNovedad, recuentoSubniveles:nivel.subniveles_count, recuentoItems:nivel.causas_novedades_items_count})" v-if="nivel.estado">
                                                <template v-if="nivel.subniveles_count">Subniveles</template>
                                                <template v-else-if="nivel.causas_novedades_items_count">Causas</template>
                                                <template v-else><i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i></template>
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm btn-nivel-causa" v-else>
                                                <template v-if="nivel.subniveles_count">Subniveles</template>
                                                <template v-else-if="nivel.causas_novedades_items_count">Causas</template>
                                                <template v-else><i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i></template>
                                            </button>
                                            <button type="button" @click="abrirModal('actualizarNivel', nivel)" class="btn btn-warning btn-sm niveleditar" data-toggle="modal" data-target="#nivel-mdl" title="Editar" v-if="nivel.estado && permisosUser.actualizar">
                                                <i class="icon-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm" v-else>
                                                <i class="icon-pencil"></i>
                                            </button>
                                            <template v-if="permisosUser.anular">
                                                <button type="button" class="btn btn-danger btn-sm" @click="descartar(undefined, nivel.estado, 'este nivel', recursoCausasNovedades, nivel.id)" title="Anular" v-if="nivel.estado">
                                                    <i class="icon-trash"></i>
                                                </button>
                                                <button type="button" class="btn btn-info btn-sm" @click="descartar(undefined, nivel.estado, 'este nivel', recursoCausasNovedades, nivel.id)" title="Activar" v-else>
                                                    <i class="icon-check"></i>
                                                </button>
                                            </template>
                                            <template v-else>
                                                <button type="button" class="btn btn-secondary btn-sm">
                                                    <i class="icon-trash" v-if="nivel.estado"></i>
                                                    <i class="icon-check" v-else></i>
                                                </button>
                                            </template>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="nivel-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-primary" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal"></h4>
                        <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-row justify-content-center">
                                <div class="form-group col-md-10">
                                    <label>Nivel</label>
                                    <input type="text" ref="nivelcbCausaNovedad" class="form-control" :class="{'is-invalid' : noHayCausaNovedad}" placeholder="Nombre del nivel" v-model="camposBodyNiveles.causaNovedad">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-pill btn-danger btn-sm" @click="cerrarModal()">Cerrar</button>
                        <button type="button" class="btn btn-pill btn-success btn-sm" @click="tipoAccion == 'Guardar'? registrarNivel() : actualizarNivel()" v-text="tipoAccion"></button>
                    </div>
                </div>
            </div>
        </div>
        <div id="causanivelcrud-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Causas</h4>
                        <button type="button" class="close" @click="cerrarModalCausas()" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <i class="fa fa-puzzle-piece" aria-hidden="true"></i>&nbsp;Causas&nbsp;
                                            <button type="button" @click="abrirModal('registrarCausa')" class="btn btn-pill btn-primary btn-sm" data-dismiss="modal" data-toggle="modal" data-target="#causanivel-mdl" v-if="permisosUser.crear">
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
                                            <tr>
                                                <th class="text-center">#</th>
                                                <th class="text-center">Código</th>
                                                <th class="text-center">Causa</th>
                                                <th class="text-center">Opciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(causa, index) in arrCausas" :key="causa.id">
                                                <td class="text-center" v-text="index + 1"></td>
                                                <td v-text="causa.codigo"></td>
                                                <td v-text="causa.causaNovedadItem"></td>
                                                <td class="text-center">
                                                    <button type="button" @click="abrirModal('actualizarCausa', causa)" class="btn btn-warning btn-sm" data-dismiss="modal" data-toggle="modal" data-target="#causanivel-mdl" title="Editar" v-if="permisosUser.actualizar">
                                                        <i class="icon-pencil"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-secondary btn-sm" v-else>
                                                        <i class="icon-pencil"></i>
                                                    </button>
                                                    <button type="button" title="Descartar" class="btn btn-danger btn-sm" @click="descartar(index, undefined, 'esta causa', recursoCausasNovedadesItems, causa.id)">
                                                        <i class="fa fa-times-circle" aria-hidden="true"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <nav>
                                        <ul class="pagination">
                                            <li class="page-item" v-if="pagination.current_page > 1">
                                                <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1)">Ant</a>
                                            </li>
                                            <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                                <a class="page-link" href="#" @click.prevent="cambiarPagina(page)" v-text="page"></a>
                                            </li>
                                            <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                                <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1)">Sig</a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-pill btn-primary btn-sm" @click="cerrarModalCausas()" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="causanivel-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Causas</h4>
                        <button type="button" class="close" @click="cerrarModalCausasModal()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-row justify-content-center">
                                <div class="form-group col-md-9">
                                    <label>Código</label>
                                    <input type="number" ref="causacbCodigo" class="form-control" :class="{'is-invalid' : noHayCodigo}" v-model="camposBodyCausas.codigo">
                                </div>
                            </div>
                            <div class="form-row justify-content-center">
                                <div class="form-group col-md-9">
                                    <label>Causa</label>
                                    <textarea class="form-control" :class="{'is-invalid' : noHayCausaNovedadItem}" rows="3" v-model="camposBodyCausas.causaNovedadItem"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-pill btn-danger btn-sm" @click="cerrarModalCausasModal()">Cerrar</button>
                        <button type="button" class="btn btn-pill btn-success btn-sm" @click="tipoAccion == 'Guardar' ? registrarCausa() : actualizarCausa()" v-text="tipoAccion"></button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>
<script>
    export default {
        props : ['permisosUser', 'ruta', 'axiosApp']
        , data ()
        {
            return {
                recursoCausasNovedades : 'causas_novedades'
                , recursoCausasNovedadesItems : 'causas_novedades_items'

                , codigo : ''
                , arrNiveles : []

                , onVaciosNiveles : false
                , camposBodyNiveles : {
                    causaNovedad : ''
                    , padre : 0
                }
                , idNivel : 0
                , padres : [0]

                , arrCausas : []
                , onVaciosCausas : false
                , camposBodyCausas : {
                    codigo : ''
                    , causaNovedadItem : ''
                    , fk_id_causas_novedades : 0
                }
                , idCausa : 0
                , indDescartar : 0
                , pagination : {
                    total : 0
                    , current_page : 0
                    , per_page : 0
                    , last_page : 0
                    , from : 0
                    , to : 0
                }
                , offset : 3

                , modalEleccion : Swal.mixin({
                    html :
                    '<div class="row justify-content-center">'
                    + '<button id="subniveles" type="button" class="btn btn-outline-primary btn-lg btn-eleccion">Registrar subniveles</button>'
                    + '<button id="causas" type="button" class="btn btn-outline-primary btn-lg btn-eleccion">Registrar causas</button>'
                    + '</div>'
                    , showConfirmButton : false
                })
                , tipoAccion : 'Guardar'
                , tituloModal : 'Registrar nivel'
            }
        }
        , computed :
        {
            // VALIDACIÓN DE CAMPOS VACIOS EN FORMULARIOS
            // Campos nivel
            noHayCausaNovedad : function ()
            {
                return this.onVaciosNiveles && !this.camposBodyNiveles.causaNovedad.trim();
            }
            // Campos causa
            , noHayCodigo : function ()
            {
                return this.onVaciosCausas && !this.camposBodyCausas.codigo.trim();
            }
            , noHayCausaNovedadItem : function ()
            {
                return this.onVaciosCausas && !this.camposBodyCausas.causaNovedadItem.trim();
            }
            , isActived: function()
            {
                return this.pagination.current_page;
            }
            //Calcula los elementos de la paginación
            , pagesNumber: function()
            {
                if (!this.pagination.to) {
                    return [];
                }
                
                var from = this.pagination.current_page - this.offset; 
                if (from < 1) {
                    from = 1;
                }

                var to = from + (this.offset * 2); 
                if (to >= this.pagination.last_page) {
                    to = this.pagination.last_page;
                }  

                var pagesArray = [];
                while (from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;
            }
        }
        , methods :
        {
            causaCcCodigo ()
            {
                this.$refs.causaccCodigo.focus();
            }
            , buscarCausa () {
                let uri = `${this.recursoCausasNovedadesItems}/buscar`;
                this.peticionGet(uri, `?codigo=${this.codigo}`);                
            }
            , abrirModal(accion, data = {})
            {
                switch (accion) {
                    case 'registrarNivel':
                        break;
                    case 'actualizarNivel':
                        this.camposBodyNiveles.causaNovedad = data.causaNovedad;
                        this.idNivel = data.id;
                        this.tipoAccion = 'Actualizar';
                        break;
                    case 'elegir':
                        // SE ESTABLECE LA NAVEGACION DE JERARQUÍA POR NIVELES
                        if (data.recuentoSubniveles) {
                            this.opcionNiveles(data.id);
                        } else if (data.recuentoItems) {
                            this.opcionCausas(data.id);
                        } else {
                            let tipoEvento = 'subniveles';
                            this.modalEleccion.fire({
                                onBeforeOpen : () => {
                                    const content = Swal.getContent();
                                    const $ = content.querySelector.bind(content);
                                    const subniveles = $('#subniveles');
                                    const causas = $('#causas');

                                    subniveles.addEventListener('click', () => {
                                        Swal.clickConfirm();
                                    });
                                    causas.addEventListener('click', () => {
                                        tipoEvento = 'causas';
                                        Swal.clickConfirm();
                                    });
                                }
                                , onOpen : () => document.activeElement.blur()
                            }).then((result) => {
                                if (result.value) {
                                    if (tipoEvento == 'subniveles') {
                                        this.opcionNiveles(data.id)
                                    } else {
                                        this.opcionCausas(data.id)
                                    }
                                }
                            });
                        }
                        break;
                    case 'registrarCausa':
                        break;
                    case 'actualizarCausa':
                        this.camposBodyCausas.codigo = data.codigo;
                        this.camposBodyCausas.causaNovedadItem = data.causaNovedadItem;
                        this.idCausa = data.id;
                        this.tipoAccion = 'Actualizar';
                        break;
                }
            }
            , opcionNiveles (id)
            {
                this.camposBodyNiveles.padre = id;
                this.padres.push(id);
                this.causaCcCodigo();
                this.listarNiveles();
            }
            , opcionCausas (id)
            {
                this.idNivel = id;
                this.camposBodyCausas.fk_id_causas_novedades = id;
                this.listarCausas();
                $('#causanivelcrud-mdl').modal('show');
            }
            , cerrarModal ()
            {
                this.idNivel = 0;
                this.tipoAccion = 'Guardar';
                this.camposBodyNiveles.causaNovedad = '';
                this.onVaciosNiveles = false;
                $('#nivel-mdl').modal('hide');
            }
            , cerrarModalCausas ()
            {           
                this.idNivel = this.camposBodyCausas.fk_id_causas_novedades = 0;
                this.arrCausas = [];
            }
            ,cerrarModalCausasModal ()
            {
                $('#causanivel-mdl').modal('hide');
                $('#causanivelcrud-mdl').modal('show');
                this.idCausa = 0;
                this.tipoAccion = 'Guardar';
                this.camposBodyCausas.codigo = this.camposBodyCausas.causaNovedadItem = '';
                this.onVaciosCausas = false;
            }
            , ascenderNivel ()
            {
                this.padres.pop();
                this.camposBodyNiveles.padre = this.padres[this.padres.length - 1];
                this.causaCcCodigo();
                this.listarNiveles();
            }
            , listarNiveles ()
            {
                this.peticionGet(this.recursoCausasNovedades, `?padre=${this.padres[this.padres.length - 1]}`);
            }
            , listarCausas ()
            {
                this.peticionGet(this.recursoCausasNovedadesItems, `?fkIdCausasNovedades=${this.idNivel}`);
            }
            , validarNivel ()
            {
                return this.noHayCausaNovedad;
            }
            , validarCausa ()
            {
                return this.noHayCodigo || this.noHayCausaNovedadItem;
            }
            , registrarNivel ()
            {
                this.onVaciosNiveles = true;
                if (this.validarNivel())return;
                var uri = `${this.recursoCausasNovedades}/registrar`;
                this.peticionPost(uri, this.camposBodyNiveles);
            }
            , registrarCausa ()
            {
                this.onVaciosCausas = true;
                if (this.validarCausa())return;
                var uri = `${this.recursoCausasNovedadesItems}/registrar`;
                this.peticionPost(uri, this.camposBodyCausas);
            }
            , actualizarNivel ()
            {
                this.onVaciosNiveles = true;
                if (this.validarNivel())return;
                var uri = `${this.recursoCausasNovedades}/actualizar/`;
                this.peticionPut(uri, this.idNivel, this.camposBodyNiveles);
            }
            , actualizarCausa ()
            {
                this.onVaciosCausas = true;
                if (this.validarCausa())return;
                var uri = `${this.recursoCausasNovedadesItems}/actualizar/`;
                this.peticionPut(uri, this.idCausa, this.camposBodyCausas);
            }
            , descartar (index = -1, estado = -1, finalTitle, recurso, idRecurso)
            {
                var accion = index == -1? `${estado?'des':''}activar` : 'descartar';
                Swal.fire({
                    title: `Esta seguro de ${accion} ${finalTitle}?`
                    , type: 'warning'
                    , showCancelButton: true
                    , confirmButtonColor: '#3085d6'
                    , cancelButtonColor: '#d33'
                    , confirmButtonText: 'Aceptar!'
                    , cancelButtonText: 'Cancelar'
                    , confirmButtonClass: 'btn btn-success'
                    , cancelButtonClass: 'btn btn-danger'
                    , buttonsStyling: false
                    , reverseButtons: true
                    }).then((result) => {
                    if (result.value) {
                        this.indDescartar = index;
                        var uri = `${recurso}/${accion}/`;
                        if (index == -1) {
                            this.peticionPatch(uri, idRecurso, estado);
                        }
                        else {
                            this.peticionDelete(uri, idRecurso);
                        }
                    } else if (result.dismiss === Swal.DismissReason.cancel /* Read more about handling dismissals */) {}
                });
            }
            , peticionGet (uri, cadenaConsulta = '')
            {
                var peticion = this.axiosApp.get(`${uri}${cadenaConsulta}`);
                this.obtenerRespuesta(uri, peticion);
            }
            , peticionPost (uri, camposBody)
            {
                var peticion = this.axiosApp.post(uri, camposBody);
                this.obtenerRespuesta(uri, peticion);
            }
            , peticionPut (uri, idRecurso, camposBody)
            {
                var peticion = this.axiosApp.put(`${uri}${idRecurso}`, camposBody);
                this.obtenerRespuesta(uri, peticion);
            }
            , peticionPatch (uri, idRecurso, estado)
            {
                this.tituloSwal = 'Activado';
                this.descripcionSwal = 'El registro ha sido activado con éxito.';
                if (estado) {
                    this.tituloSwal = 'Desactivado';
                    this.descripcionSwal = 'El registro ha sido desactivado con éxito.';
                }
                var peticion = this.axiosApp.patch(`${uri}${idRecurso}`);
                this.obtenerRespuesta(uri, peticion);
            }
            , peticionDelete (uri, idRecurso) {
                var peticion = this.axiosApp.delete(`${uri}${idRecurso}`);
                this.obtenerRespuesta(uri, peticion);
            }
            , obtenerRespuesta(uri, peticion)
            {
                var me = this;
                peticion.then(function (response) {
                    var respuesta = response.data;
                    switch (uri) {
                        case me.recursoCausasNovedades:
                            me.arrNiveles = respuesta.causasNovedades;
                            break;
                        case `${me.recursoCausasNovedades}/registrar`:
                        case `${me.recursoCausasNovedades}/actualizar/`:
                            me.listarNiveles();
                            me.cerrarModal();
                            break;
                        case `${me.recursoCausasNovedades}/desactivar/`:
                        case `${me.recursoCausasNovedades}/activar/`:
                            me.listarNiveles();
                            Swal.fire(me.tituloSwal, me.descripcionSwal, 'success');
                            break;
                        case me.recursoCausasNovedadesItems:
                            me.arrCausas = respuesta.causasNovedadesItems.data;
                            me.pagination = respuesta.pagination;
                            break;
                        case `${me.recursoCausasNovedadesItems}/buscar`:
                            Swal.fire('Resultado busqueda', respuesta.mensaje, 'info');
                            break;
                        case `${me.recursoCausasNovedadesItems}/registrar`:
                        case `${me.recursoCausasNovedadesItems}/actualizar/`:
                            me.listarCausas();
                            me.cerrarModalCausasModal();
                            break;
                        case `${me.recursoCausasNovedadesItems}/descartar/`:
                            me.arrCausas.splice(me.indDescartar, 1);
                            me.indDescartar = 0;
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
            this.listarNiveles();
            this.causaCcCodigo();
            $('#nivel-mdl').on('shown.bs.modal', () => this.$refs.nivelcbCausaNovedad.focus());
            $('#nivelnuevo').focus(() => {if(!$('#nivelnuevo:hover').length) this.causaCcCodigo();});
            $('.nivellista').on('focus', '.niveleditar', () => {if(!$('button.segmentoeditar:hover').length) this.causaCcCodigo();});
            $('#causanivelcrud-mdl').on('hidden.bs.modal', () => this.causaCcCodigo());

            $('#causanivel-mdl').on('shown.bs.modal', () => this.$refs.causacbCodigo.focus());
        }
    }
</script>
<style>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .btn-nivel-causa{
        width : 6em;
    }
    .btn-eleccion{
        width : 10.5em;
    }
</style>