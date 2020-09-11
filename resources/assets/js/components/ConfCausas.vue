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
                            <i class="fa fa-puzzle-piece fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;Niveles&nbsp;&nbsp;
                            <button type="button" @click="abrirModal('registrarNivel')" class="btn btn-pill btn-primary btn-sm" v-if="permisosUser.crear">
                                <i class="icon-plus" aria-hidden="true"></i>&nbsp;Nuevo
                            </button>
                            <button type="button" class="btn btn-pill btn-secondary btn-sm" v-else>
                                <i class="icon-plus" aria-hidden="true"></i>&nbsp;Nuevo
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
                        <!--<label class="col-md-auto my-1 pr-1">Código causa</label>
                        <div class="col-md-2 pl-1">
                        </div>-->
                        <input type="hidden" ref="causaNovedadItemCodigo" class="form-control" placeholder="Texto a buscar" v-model="codigo">
                        <label class="col-md-auto my-1 pr-1">Ver</label>
                        <div class="col-md-auto pl-1">
                            <select class="form-control" @change="cambiarPagina(1, nivelObjCons, nivelPagin, 1)" v-model="nivelObjCons.numRegs">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <!--<button type="button" @click="buscarCausa();" class="btn btn-pill btn-primary ml-2">
                            <i class="fa fa-search" aria-hidden="true"></i>&nbsp;Buscar
                        </button>-->
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
                                <tbody>
                                    <template v-if="nivelArr.length == 0">
                                        <tr>
                                            <td class="text-center" colspan="4">No hay registros</td>
                                        </tr>
                                    </template>
                                    <template v-else>
                                        <tr v-for="(causa, index) in nivelArr" :key="causa.id">
                                            <td class="text-center" v-text="index + 1"></td>
                                            <td v-text="causa.causaNovedad"></td>
                                            <td class="text-center">
                                                <span class="badge" :class="'badge-' + (causa.estado? 'success':'danger')" v-text="causa.estado? 'Activo':'Inactivo'"></span>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn rounded btn-primary btn-sm btn-nivel-causa" @click="abrirModal('elegir', {id:causa.id, causaNovedad:causa.causaNovedad, recuentoSubniveles:causa.subniveles_count, recuentoItems:causa.causas_novedades_items_count})" v-if="causa.estado">
                                                    <template v-if="causa.subniveles_count">Subniveles</template>
                                                    <template v-else-if="causa.causas_novedades_items_count">Causas</template>
                                                    <template v-else><i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i></template>
                                                </button>
                                                <button type="button" class="btn rounded btn-secondary btn-sm btn-nivel-causa" v-else>
                                                    <template v-if="causa.subniveles_count">Subniveles</template>
                                                    <template v-else-if="causa.causas_novedades_items_count">Causas</template>
                                                    <template v-else><i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i></template>
                                                </button>
                                                <button type="button" @click="abrirModal('actualizarNivel', causa)" class="btn rounded btn-warning btn-sm" title="Editar" v-if="causa.estado && permisosUser.actualizar">
                                                    <i class="icon-pencil" aria-hidden="true"></i>
                                                </button>
                                                <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                    <i class="icon-pencil" aria-hidden="true"></i>
                                                </button>
                                                <button type="button" @click="descartar(undefined, causa.estado, 'este nivel', causaRuta, causa.id)" class="btn rounded btn-sm" :class="'btn-'+ (causa.estado ? 'danger':'success')" :title="causa.estado ? 'Anular':'Activar'" v-if="permisosUser.anular">
                                                    <i :class="'icon-'+ (causa.estado ? 'trash':'check')" aria-hidden="true"></i>
                                                </button>
                                                <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                    <i :class="'icon-'+ (causa.estado ? 'trash':'check')" aria-hidden="true"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                            <nav>
                                <ul class="pagination">
                                    <li class="page-item" v-if="nivelPagin.current_page > 1">
                                        <a class="page-link" href="#" @click.prevent="cambiarPagina(nivelPagin.current_page - 1, nivelObjCons, nivelPagin, 1)">Ant</a>
                                    </li>
                                    <li class="page-item" v-for="page in nivelPagesNumber" :key="page" :class="[page == nivelPagin.current_page ? 'active' : '']">
                                        <a class="page-link" href="#" @click.prevent="cambiarPagina(page, nivelObjCons, nivelPagin, 1)" v-text="page"></a>
                                    </li>
                                    <li class="page-item" v-if="nivelPagin.current_page < nivelPagin.last_page">
                                        <a class="page-link" href="#" @click.prevent="cambiarPagina(nivelPagin.current_page + 1, nivelObjCons, nivelPagin, 1)">Sig</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="nivel-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-primary" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="nivelTituloMdl +' nivel'"></h4>
                        <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-row justify-content-center">
                                <div class="form-group col-md-10">
                                    <label>Nivel</label>
                                    <input type="text" ref="nivelcbCausaNovedad" class="form-control" :class="{'is-invalid' : noHayCausaNovedad}" placeholder="Nombre del nivel" v-model="nivelBody.causaNovedad">
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
                        <h4 class="modal-title" v-text="'Nivel - '+ nivelTituloMdl"></h4>
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
                                            <button type="button" @click="abrirModal('registrarCausa')" class="btn btn-pill btn-primary btn-sm" v-if="permisosUser.crear">
                                                <i class="icon-plus" aria-hidden="true"></i>&nbsp;Nuevo
                                            </button>
                                            <button type="button" class="btn btn-pill btn-secondary btn-sm" v-else>
                                                <i class="icon-plus" aria-hidden="true"></i>&nbsp;Nuevo
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="form-group row justify-content-start">
                                        <label class="col-md-auto my-1 pr-1">Ver</label>
                                        <div class="col-md-auto pl-1">
                                            <select class="form-control" @change="cambiarPagina(1, itemObjCons, itemPagin, 2)" v-model="itemObjCons.numRegs">
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
                                                <th class="text-center">Código</th>
                                                <th class="text-center">Causa</th>
                                                <th class="text-center">Opciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template v-if="itemArr.length == 0">
                                                <tr>
                                                    <td class="text-center" colspan="4">No hay registros</td>
                                                </tr>
                                            </template>
                                            <template v-else>
                                                <tr v-for="(causa, index) in itemArr" :key="causa.id">
                                                    <td class="text-center" v-text="index + 1"></td>
                                                    <td v-text="causa.codigo"></td>
                                                    <td v-text="causa.causaNovedadItem"></td>
                                                    <td class="text-center">
                                                        <button type="button" @click="abrirModal('actualizarCausa', causa)" class="btn rounded btn-warning btn-sm" title="Editar" v-if="permisosUser.actualizar">
                                                            <i class="icon-pencil" aria-hidden="true"></i>
                                                        </button>
                                                        <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                            <i class="icon-pencil" aria-hidden="true"></i>
                                                        </button>
                                                        <button type="button" title="Descartar" class="btn rounded btn-danger btn-sm" @click="descartar(index, undefined, 'esta causa', recursoCausasNovedadesItems, causa.id)">
                                                            <i class="fa fa-times-circle" aria-hidden="true"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                    <nav>
                                        <ul class="pagination">
                                            <li class="page-item" v-if="itemPagin.current_page > 1">
                                                <a class="page-link" href="#" @click.prevent="cambiarPagina(itemPagin.current_page - 1, itemObjCons, itemPagin, 2)">Ant</a>
                                            </li>
                                            <li class="page-item" v-for="page in causaPagesNumber" :key="page" :class="[page == itemPagin.current_page ? 'active' : '']">
                                                <a class="page-link" href="#" @click.prevent="cambiarPagina(page, itemObjCons, itemPagin, 2)" v-text="page"></a>
                                            </li>
                                            <li class="page-item" v-if="itemPagin.current_page < itemPagin.last_page">
                                                <a class="page-link" href="#" @click.prevent="cambiarPagina(itemPagin.current_page + 1, itemObjCons, itemPagin, 2)">Sig</a>
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
                        <h4 class="modal-title" v-text="itemTituloMdl +' causa'"></h4>
                        <button type="button" class="close" @click="cerrarModalCausasModal()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-row justify-content-center">
                                <div class="form-group col-md-9">
                                    <label>Código</label>
                                    <input type="number" ref="itemBCodigo" class="form-control" :class="{'is-invalid' : noHayCodigo}" v-model="itemBody.codigo">
                                </div>
                            </div>
                            <div class="form-row justify-content-center">
                                <div class="form-group col-md-9">
                                    <label>Causa</label>
                                    <textarea class="form-control" :class="{'is-invalid' : noHayCausaNovedadItem}" rows="3" v-model="itemBody.causaNovedadItem"></textarea>
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
                causaRuta : 'causas_novedades'
                , recursoCausasNovedadesItems : 'causas_novedades_items'

                , codigo : ''

                , nivelObjCons : {
                    '?page' : 1
                    , padre : 0
                    , numRegs : 10
                }
                , nivelArr : []
                , nivelTituloMdl : 'Registrar'
                , nivelId : 0
                , nivelBody : {
                    causaNovedad : ''
                    , padre : 0
                }
                , nivelOnVacios : false
                , padres : [0]
                , nivelPagin : {
                    total : 0
                    , current_page : 0
                    , per_page : 0
                    , last_page : 0
                    , from : 0
                    , to : 0
                }

                , itemObjCons : {
                    '?page' : 1
                    , fk_id_causas_novedades : 0
                    , numRegs : 10
                }
                , itemArr : []
                , itemTituloMdl : 'Registrar'
                , idCausa : 0
                , itemBody : {
                    codigo : ''
                    , causaNovedadItem : ''
                    , fk_id_causas_novedades : 0
                }
                , itemOnVacios : false
                , indDescartar : 0
                , itemPagin : {
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
            }
        }
        , computed :
        {
            // VALIDACIÓN DE CAMPOS VACIOS EN FORMULARIOS
            // Campos nivel
            noHayCausaNovedad : function ()
            {
                return this.nivelOnVacios && !this.nivelBody.causaNovedad.trim();
            }
            // Campos causa
            , noHayCodigo : function ()
            {
                return this.itemOnVacios && !this.itemBody.codigo.trim();
            }
            , noHayCausaNovedadItem : function ()
            {
                return this.itemOnVacios && !this.itemBody.causaNovedadItem.trim();
            }
            //Calcula los elementos de la paginación
            , nivelPagesNumber: function()
            {
                if (!this.nivelPagin.to) {
                    return [];
                }
                let from = this.nivelPagin.current_page - this.offset;
                if (from < 1) {
                    from = 1;
                }
                let to = from + (this.offset * 2);
                if (to >= this.nivelPagin.last_page) {
                    to = this.nivelPagin.last_page;
                }  
                let pagesArray = [];
                while (from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;
            }
            , causaPagesNumber: function()
            {
                if (!this.itemPagin.to) {
                    return [];
                }
                let from = this.itemPagin.current_page - this.offset;
                if (from < 1) {
                    from = 1;
                }
                let to = from + (this.offset * 2);
                if (to >= this.itemPagin.last_page) {
                    to = this.itemPagin.last_page;
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
            causaCcCodigo ()
            {
                this.$refs.causaccCodigo.focus();
            }
            , buscarCausa () {
                let uri = `${this.recursoCausasNovedadesItems}/buscar`;
                this.$refs.causaNovedadItemCodigo.focus();
                this.peticionGet(uri, `?codigo=${this.codigo}`);
            }
            , cambiarPagina (page, refObjCons, refPagin, listar) {
                refObjCons['?page'] = refPagin.current_page = page;
                switch (listar) {
                    case 1:this.listarNiveles();break;
                    case 2:this.listarCausas();break;
                }
            }
            , abrirModal(accion, data = {})
            {
                switch (accion) {
                    case 'registrarNivel':
                        $('#nivel-mdl').modal('show');
                        break;
                    case 'actualizarNivel':
                        this.nivelTituloMdl = 'Editar';
                        this.nivelBody.causaNovedad = data.causaNovedad;
                        this.nivelId = data.id;
                        this.tipoAccion = 'Actualizar';
                        $('#nivel-mdl').modal('show');
                        break;
                    case 'elegir':
                        // SE ESTABLECE LA NAVEGACION DE JERARQUÍA POR NIVELES
                        if (data.recuentoSubniveles) {
                            this.opcionNiveles(data.id);
                        } else if (data.recuentoItems) {
                            this.opcionCausas(data.id, data.causaNovedad);
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
                                        this.opcionCausas(data.id, data.causaNovedad)
                                    }
                                }
                            });
                        }
                        break;
                    case 'registrarCausa':
                        $('#causanivelcrud-mdl').modal('hide');
                        $('#causanivel-mdl').modal('show');
                        break;
                    case 'actualizarCausa':
                        $('#causanivelcrud-mdl').modal('hide');
                        this.itemTituloMdl = 'Editar';
                        this.itemBody.codigo = data.codigo;
                        this.itemBody.causaNovedadItem = data.causaNovedadItem;
                        this.idCausa = data.id;
                        this.tipoAccion = 'Actualizar';
                        $('#causanivel-mdl').modal('show');
                        break;
                }
            }
            , opcionNiveles (id)
            {
                this.nivelObjCons.padre = this.nivelBody.padre = id;
                this.padres.push(id);
                this.$refs.causaNovedadItemCodigo.focus();
                this.listarNiveles();
            }
            , opcionCausas (id, causaNovedad)
            {
                this.nivelId = id;
                this.itemObjCons.fk_id_causas_novedades = this.itemBody.fk_id_causas_novedades = id;
                this.nivelTituloMdl = causaNovedad;
                this.listarCausas();
                $('#causanivelcrud-mdl').modal('show');
            }
            , cerrarModal ()
            {
                this.nivelTituloMdl = 'Registrar';
                this.nivelId = 0;
                this.tipoAccion = 'Guardar';
                this.nivelBody.causaNovedad = '';
                this.nivelOnVacios = false;
                $('#nivel-mdl').modal('hide');
            }
            , cerrarModalCausas ()
            {           
                this.nivelId = this.itemObjCons.fk_id_causas_novedades = this.itemBody.fk_id_causas_novedades = 0;
                this.nivelTituloMdl = 'Registrar';
                this.itemArr = [];
            }
            ,cerrarModalCausasModal ()
            {
                this.idCausa = 0;
                this.tipoAccion = 'Guardar';
                this.itemBody.codigo = this.itemBody.causaNovedadItem = '';
                this.itemOnVacios = false;
                this.itemTituloMdl = 'Registrar';
                $('#causanivel-mdl').modal('hide');
                $('#causanivelcrud-mdl').modal('show');
            }
            , ascenderNivel ()
            {
                this.padres.pop();
                this.nivelObjCons.padre = this.nivelBody.padre = this.padres[this.padres.length - 1];
                //this.causaCcCodigo();
                this.$refs.causaNovedadItemCodigo.focus();
                this.cambiarPagina(1, this.nivelObjCons, this.nivelPagin, 1);
            }
            , listarNiveles ()
            {
                this.peticionGet(this.causaRuta, this.obtenerCadCons(this.nivelObjCons));
            }
            , listarCausas ()
            {
                this.peticionGet(this.recursoCausasNovedadesItems, this.obtenerCadCons(this.itemObjCons));
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
                this.nivelOnVacios = true;
                if (this.validarNivel())return;
                var uri = `${this.causaRuta}/registrar`;
                this.peticionPost(uri, this.nivelBody);
            }
            , registrarCausa ()
            {
                this.itemOnVacios = true;
                if (this.validarCausa())return;
                var uri = `${this.recursoCausasNovedadesItems}/registrar`;
                this.peticionPost(uri, this.itemBody);
            }
            , actualizarNivel ()
            {
                this.nivelOnVacios = true;
                if (this.validarNivel())return;
                var uri = `${this.causaRuta}/actualizar/`;
                this.peticionPut(uri, this.nivelId, this.nivelBody);
            }
            , actualizarCausa ()
            {
                this.itemOnVacios = true;
                if (this.validarCausa())return;
                var uri = `${this.recursoCausasNovedadesItems}/actualizar/`;
                this.peticionPut(uri, this.idCausa, this.itemBody);
            }
            , descartar (index = -1, estado = -1, finalTitle, recurso, idRecurso)
            {
                var accion = index == -1? `${estado?'des':''}activar` : 'descartar';
                Swal.fire({
                    title: `¿Esta seguro de ${accion} ${finalTitle}?`
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
                        this.indDescartar = index;
                        var uri = `${recurso}/${accion}/`;
                        if (index == -1) {
                            this.estadoPatch(uri, idRecurso, estado);
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
            , estadoPatch (uri, idRecurso, estado)
            {
                this.tituloSwal = 'Activado';
                this.descripcionSwal = 'Registro activado con éxito.';
                if (estado) {
                    this.tituloSwal = 'Desactivado';
                    this.descripcionSwal = 'Registro desactivado con éxito.';
                }
                var peticion = this.axiosApp.patch(`${uri}${idRecurso}`);
                this.obtenerRespuesta(uri, peticion);
            }
            , peticionDelete (uri, idRecurso) {
                var peticion = this.axiosApp.delete(`${uri}${idRecurso}`);
                this.obtenerRespuesta(uri, peticion);
            }
            , obtenerCadCons(objCons) {
                return Object.entries(objCons).map(filtro => `${filtro[0]}=${filtro[1]}`).join('&');
            }
            , obtenerRespuesta(uri, peticion)
            {
                var me = this;
                peticion.then(function (response) {
                    let respuesta = response.data;
                    switch (uri) {
                        case me.causaRuta:
                            me.nivelArr = respuesta.causasNovedades.data;
                            me.nivelPagin = respuesta.pagination;
                            break;
                        case `${me.causaRuta}/registrar`:
                        case `${me.causaRuta}/actualizar/`:
                            me.listarNiveles();
                            me.cerrarModal();
                            break;
                        case `${me.causaRuta}/desactivar/`:
                        case `${me.causaRuta}/activar/`:
                            me.listarNiveles();
                            Swal.fire(me.tituloSwal, me.descripcionSwal, 'success');
                            break;
                        case me.recursoCausasNovedadesItems:
                            me.itemArr = respuesta.causasNovedadesItems.data;
                            me.itemPagin = respuesta.pagination;
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
                            me.itemArr.splice(me.indDescartar, 1);
                            Swal.fire('Descartado!', 'Registro descartado con éxito', 'success');
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
            this.$refs.causaNovedadItemCodigo.focus();

            $('#nivel-mdl').on('shown.bs.modal', () => this.$refs.nivelcbCausaNovedad.focus());
            $('#nivel-mdl, #causanivelcrud-mdl').on('hidden.bs.modal', () => this.$refs.causaNovedadItemCodigo.focus());
            $('#causanivel-mdl').on('shown.bs.modal', () => this.$refs.itemBCodigo.focus());
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