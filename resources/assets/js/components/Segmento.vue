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
                                <i class="icon-layers fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;Segmentos&nbsp;&nbsp;
                                <button id="segmentonuevo" type="button" @click="abrirModal(segmentoRuta +'Registrar')" class="btn btn-pill btn-primary btn-sm" v-if="permisosUser.crear">
                                    <i class="icon-plus" aria-hidden="true"></i>&nbsp;Nuevo
                                </button>
                                <button type="button" class="btn btn-pill btn-secondary btn-sm" v-else>
                                    <i class="icon-plus" aria-hidden="true"></i>&nbsp;Nuevo
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-auto my-1 pr-1">Segmento</label>
                            <div class="col-md-3 pl-1">
                                <input type="text" ref="segmentoOCSegmento" v-model="segmentoObjCons.segmento" @keyup.enter="cambiarPagina(1, segmentoObjCons, segmentoPagin, 1)" class="form-control" placeholder="Texto a buscar">
                            </div>
                            <label class="col-md-auto my-1 pr-1">Tipo segmento</label>
                            <div class="col-md-2 pl-1">
                                <select class="form-control" v-model="segmentoObjCons.indiceTipoSegmento">
                                    <option value="">Todos</option>
                                    <option value="1">HSE</option>
                                    <option value="2">CALIDAD</option>
                                </select>
                            </div>
                            <label class="col-md-auto my-1 pr-1">Ver</label>
                            <div class="col-md-auto pl-1">
                                <select class="form-control" @change="segmentoListar()" v-model="segmentoObjCons.numRegs">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <button type="button" @click="cambiarPagina(1, segmentoObjCons, segmentoPagin, 1)" class="btn btn-pill btn-primary btn-sm ml-2"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;Buscar</button>
                        </div>
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Segmento</th>
                                    <th class="text-center">Tipo segmento</th>
                                    <th class="text-center">Observación</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Opciones</th>
                                </tr>
                            </thead>
                            <tbody class="segmentolista">
                                <template v-if="segmentoArr.length == 0">
                                    <tr>
                                        <td class="text-center" colspan="6">No hay registros</td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr v-for="(segmento, index) in segmentoArr" :key="segmento.id">
                                        <td class="text-center" v-text="index + 1"></td>
                                        <td v-text="segmento.segmento"></td>
                                        <td v-text="segmento.tipoSegmento"></td>
                                        <td v-text="segmento.observacion"></td>
                                        <td class="text-center">
                                            <span class="badge" :class="'badge-' + (segmento.estado? 'success':'danger')" v-text="segmento.estado? 'Activo':'Inactivo'"></span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" title="Listar carpetas" @click="abrirModal(carpetaRuta +'Listar', {id : segmento.id, segmento : segmento.segmento})" class="btn rounded btn-info btn-sm carpetasegmentobtn" v-if="segmento.estado">
                                            <i class="fa fa-folder-open-o" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                            <i class="fa fa-folder-open-o" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" title="Editar" @click="abrirModal(segmentoRuta +'Actualizar', segmento)" class="btn rounded btn-warning btn-sm segmentoeditar" v-if="segmento.estado && permisosUser.actualizar">
                                            <i class="icon-pencil" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                            <i class="icon-pencil" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" @click="actualizarEstado(segmento.estado, 'este segmento', segmentoRuta, segmento.id)" class="btn rounded btn-sm" :class="'btn-'+ (segmento.estado ? 'danger':'success')" :title="segmento.estado ? 'Anular':'Activar'" v-if="permisosUser.anular">
                                                <i :class="'icon-'+ (segmento.estado ? 'trash':'check')" aria-hidden="true"></i>
                                            </button>
                                            <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                <i :class="'icon-'+ (segmento.estado ? 'trash':'check')" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <nav>
                            <ul class="pagination">
                                <li class="page-item" v-if="segmentoPagin.current_page > 1">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(segmentoPagin.current_page - 1, segmentoObjCons, segmentoPagin, 1)">Ant</a>
                                </li>
                                <li class="page-item" v-for="page in segmentoPagesNumber" :key="page" :class="[page == segmentoPagin.current_page ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page, segmentoObjCons, segmentoPagin, 1)" v-text="page"></a>
                                </li>
                                <li class="page-item" v-if="segmentoPagin.current_page < segmentoPagin.last_page">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(segmentoPagin.current_page + 1, segmentoObjCons, segmentoPagin, 1)">Sig</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <!-- Fin ejemplo de tabla Listado -->
            </div>
            <!--Modal agregar/actualizar-->
            <div id="segmento-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-primary" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="segmentoTituloMdl +' segmento'"></h4>
                            <button type="button" class="close" @click="segmentoCerrarMdl()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                                <div class="form-row justify-content-center">
                                    <div class="form-group col-md-7">
                                        <label>Segmento</label>
                                        <input type="text" ref="segmentoBSegmento" v-model="segmentoBody.segmento" class="form-control" :class="{'is-invalid' : noHaySegmento}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Tipo segmento</label>
                                        <select v-model="segmentoBody.tipoSegmento" class="form-control" :class="{'is-invalid' : noHayTipoSegmento}">
                                            <option value="" selected>Seleccione</option>
                                            <option value="1">HSE</option>
                                            <option value="2">CALIDAD</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row justify-content-center">
                                    <div class="form-group col-md-10">
                                        <label>Observación</label>
                                        <textarea v-model="segmentoBody.observacion" class="form-control" :class="{'is-invalid' : noHayObservacion}" rows="1"></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-pill btn-danger btn-sm" @click="segmentoCerrarMdl()">Cerrar</button>
                            <button type="button" class="btn btn-pill btn-success btn-sm" @click="tipoAccion == 'Guardar'? segmentoRegistrar():segmentoActualizar()" v-text="tipoAccion"></button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!--Modal carpetas-segmento-->
            <div id="carpetasegmentocrud-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-primary modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="segmentoTituloMdl"></h4>
                            <button type="button" class="close" @click="carpetaCerrarMdl()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-md-auto">
                                                <i class="fa fa-folder-open-o fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;Carpetas&nbsp;&nbsp;
                                                <button type="button" @click="abrirModal(carpetaRuta +'Registrar')" class="btn btn-pill btn-primary btn-sm" v-if="permisosUser.crear">
                                                    <i class="icon-plus" aria-hidden="true"></i>&nbsp;Nuevo
                                                </button>
                                                <button type="button" class="btn btn-pill btn-secondary btn-sm" v-else>
                                                    <i class="icon-plus" aria-hidden="true"></i>&nbsp;Nuevo
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Inicio tabla carpetas -->
                                    <div class="card-body">
                                        <div class="form-group row">
                                            <label class="col-md-auto my-1 pr-1">Carpeta</label>
                                            <div class="col-md-3 pl-1">
                                                <input type="text" ref="carpetaOCCarpeta" v-model="carpetaObjCons.carpeta" @keyup.enter="cambiarPagina(1, carpetaObjCons, carpetaPagin, 2)" class="form-control">
                                            </div>
                                            <label class="col-md-auto my-1 pr-1">Tipo carpeta</label>
                                            <div class="col-md-3 pl-1">
                                                <select class="form-control" v-model="carpetaObjCons.fkIdTiposCarpetas">
                                                    <option value="" selected>Todos</option>
                                                    <option v-for="tipoCarpeta in tiposCarpetasArr" :value="tipoCarpeta.id" v-text="tipoCarpeta.tipoCarpeta"></option>
                                                </select>
                                            </div>
                                            <label class="col-md-auto my-1 pr-1">Ver</label>
                                            <div class="col-md-auto pl-1">
                                                <select class="form-control" @change="carpetaListar()" v-model="carpetaObjCons.numRegs">
                                                    <option value="5">5</option>
                                                    <option value="10">10</option>
                                                    <option value="25">25</option>
                                                    <option value="50">50</option>
                                                    <option value="100">100</option>
                                                </select>
                                            </div>
                                            <button type="button" @click="cambiarPagina(1, carpetaObjCons, carpetaPagin, 2)" class="btn btn-pill btn-primary btn-sm ml-2"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;Buscar</button>
                                        </div>
                                        <table class="table table-bordered table-striped table-sm">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">#</th>
                                                    <th class="text-center">Carpeta</th>
                                                    <th class="text-center">Tipo carpeta</th>
                                                    <th class="text-center">Estado</th>
                                                    <th class="text-center">Opciones</th>
                                                </tr>
                                            </thead>
                                            <tbody class="carpetasegmentolista">
                                                <template v-if="carpetaArr.length == 0">
                                                    <tr>
                                                        <td class="text-center" colspan="5">No hay registros</td>
                                                    </tr>
                                                </template>
                                                <template v-else>
                                                    <tr v-for="(carpeta, index) in carpetaArr" :key="carpeta.id">
                                                        <td class="text-center" v-text="index + 1"></td>
                                                        <td v-text="carpeta.carpeta"></td>
                                                        <td v-text="carpeta.tipoCarpeta"></td>
                                                        <td class="text-center">
                                                            <span class="badge" :class="'badge-' + (carpeta.estado? 'success':'danger')" v-text="carpeta.estado? 'Activo':'Inactivo'"></span>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" title="Listar archivos" @click="abrirModal(archivoRuta +'Listar', {id : carpeta.id, carpeta : carpeta.carpeta})" class="btn rounded btn-info btn-sm archivocarpetabtn" v-if="carpeta.estado">
                                                                <i class="fa fa-files-o" aria-hidden="true"></i>
                                                            </button>
                                                            <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                                <i class="fa fa-folder-open-o" aria-hidden="true"></i>
                                                            </button>
                                                            <button type="button" title="Editar" @click="abrirModal(carpetaRuta +'Actualizar', carpeta)" class="btn rounded btn-warning btn-sm" v-if="carpeta.estado && permisosUser.actualizar">
                                                                <i class="icon-pencil" aria-hidden="true"></i>
                                                            </button>
                                                            <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                                <i class="icon-pencil" aria-hidden="true"></i>
                                                            </button>
                                                            <button type="button" @click="actualizarEstado(carpeta.estado, 'esta carpeta', carpetaRuta, carpeta.id)" class="btn rounded btn-sm" :class="'btn-'+ (carpeta.estado ? 'danger':'success')" :title="carpeta.estado ? 'Anular':'Activar'" v-if="permisosUser.anular">
                                                                <i :class="'icon-'+ (carpeta.estado ? 'trash':'check')" aria-hidden="true"></i>
                                                            </button>
                                                            <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                                <i :class="'icon-'+ (carpeta.estado ? 'trash':'check')" aria-hidden="true"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                        <nav>
                                            <ul class="pagination">
                                                <li class="page-item" v-if="carpetaPagin.current_page > 1">
                                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(carpetaPagin.current_page - 1, carpetaObjCons, carpetaPagin, 2)">Ant</a>
                                                </li>
                                                <li class="page-item" v-for="page in carpetaPagesNumber" :key="page" :class="[page == carpetaPagin.current_page ? 'active' : '']">
                                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page, carpetaObjCons, carpetaPagin, 2)" v-text="page"></a>
                                                </li>
                                                <li class="page-item" v-if="carpetaPagin.current_page < carpetaPagin.last_page">
                                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(carpetaPagin.current_page + 1, carpetaObjCons, carpetaPagin, 2)">Sig</a>
                                                </li>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                                <!--Fin tabla carpetas-->
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-pill btn-primary btn-sm" @click="carpetaCerrarMdl()"><i class="fa fa-reply" aria-hidden="true"></i>&nbsp;Atrás</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!--Modal agregar/actualizar-->
            <div id="carpetasegmento-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-primary" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="carpetaTituloMdl +' carpeta'"></h4>
                            <button type="button" class="close" @click="carpetaCerrarMdlMdl()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                                <div class="form-row justify-content-center">
                                    <div class="form-group col-md-7">
                                        <label>Carpeta</label>
                                        <input type="text" ref="carpetaBCarpeta" v-model="carpetaBody.carpeta" class="form-control" :class="{'is-invalid' : noHayCarpeta}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Tipo Carpeta</label>
                                        <select v-model="carpetaBody.fk_id_tipos_carpetas" class="form-control" :class="{'is-invalid' : noHayTipoCarpeta}">
                                            <option value="" selected>Seleccione</option>
                                            <option v-for="tipoCarpeta in tiposCarpetasArr" :value="tipoCarpeta.id" v-text="tipoCarpeta.tipoCarpeta"></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row justify-content-center">
                                    <div class="form-group col-md-11">
                                        <label>Descripción</label>
                                        <textarea class="form-control" :class="{'is-invalid' : noHayDescripcion}" v-model="carpetaBody.descripcion" rows="1"></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-pill btn-danger btn-sm" @click="carpetaCerrarMdlMdl()">Cerrar</button>
                            <button type="button" class="btn btn-pill btn-success btn-sm" @click="tipoAccion == 'Guardar' ? carpetaRegistrar() : carpetaActualizar()" v-text="tipoAccion"></button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!--Modal archivos-carpeta-->
            <div id="archivocarpetacrud-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-primary modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="carpetaTituloMdl"></h4>
                            <button type="button" class="close" @click="archivoCerrarMdl()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <i class="fa fa-files-o fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;Archivos&nbsp;&nbsp;
                                                <button type="button" @click="abrirModal(archivoRuta +'Registrar')" class="btn btn-pill btn-primary btn-sm" v-if="permisosUser.crear">
                                                    <i class="icon-plus" aria-hidden="true"></i>&nbsp;Nuevo
                                                </button>
                                                <button type="button" class="btn btn-pill btn-secondary btn-sm" v-else>
                                                    <i class="icon-plus" aria-hidden="true"></i>&nbsp;Nuevo
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group row">
                                            <label class="col-md-auto my-1 pr-1">Columna</label>
                                            <div class="col-md-6 pl-1">
                                                <div class="input-group">
                                                    <div class="input-group-btn">
                                                        <select class="form-control" @change="$refs.archivoOCColumnaVal.focus()" v-model="archivoObjCons.nombreCol">
                                                            <option value="archivo">Archivo</option>
                                                            <option value="codigo">Código</option>
                                                            <option value="versionamiento">Versión</option>
                                                        </select>
                                                    </div>
                                                    <input type="text" ref="archivoOCColumnaVal" v-model="archivoObjCons.textoCol" @keyup.enter="cambiarPagina(1, archivoObjCons, archivoPagin, 3)" class="form-control">
                                                </div>
                                            </div>
                                            <label class="col-md-auto my-1 pr-1">Ver</label>
                                            <div class="col-md-auto pl-1">
                                                <select class="form-control" @change="archivoListar()" v-model="archivoObjCons.numRegs">
                                                    <option value="5">5</option>
                                                    <option value="10">10</option>
                                                    <option value="25">25</option>
                                                    <option value="50">50</option>
                                                    <option value="100">100</option>
                                                </select>
                                            </div>
                                            <button type="button" @click="cambiarPagina(1, archivoObjCons, archivoPagin, 3)" class="btn btn-pill btn-primary btn-sm ml-2"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;Buscar</button>
                                        </div>
                                        <!-- Inicio tabla archivos -->
                                        <table class="table table-bordered table-striped table-sm">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">#</th>
                                                    <th class="text-center">Archivo</th>
                                                    <th class="text-center">Código</th>
                                                    <th class="text-center">Versión</th>
                                                    <th class="text-center">Estado</th>
                                                    <th class="text-center">Gestionar</th>
                                                    <th class="text-center">Opciones</th>
                                                </tr>
                                            </thead>
                                            <tbody class="archivocarpetalista">
                                                <template v-if="archivoArr.length == 0">
                                                    <tr>
                                                        <td class="text-center" colspan="7">No hay registros</td>
                                                    </tr>
                                                </template>
                                                <template v-else>
                                                    <tr v-for="(archivo, index) in archivoArr" :key="archivo.id">
                                                        <td class="text-center" v-text="index + 1"></td>
                                                        <td v-text="archivo.archivo"></td>
                                                        <td v-text="archivo.codigo"></td>
                                                        <td class="text-center" v-text="archivo.versionamiento"></td>
                                                        <td class="text-center">
                                                            <span class="badge" :class="'badge-' + (archivo.estado? 'success':'danger')" v-text="archivo.estado? 'Activo':'Inactivo'"></span>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" title="Permisos" @click="abrirModal(archivoRuta +'Permisos', {id: archivo.id, archivo: archivo.archivo})" class="btn rounded btn-info btn-sm" v-if="archivo.gestionable && archivo.estado">
                                                                <i class="fa fa-th" aria-hidden="true"></i>
                                                            </button>
                                                            <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                                <i class="fa fa-th" aria-hidden="true"></i>
                                                            </button>
                                                            <button type="button" title="Categorías y opciones" @click="abrirModal(categoriaArchivoRuta +'Listar', {id: archivo.id, archivo: archivo.archivo})" class="btn rounded btn-info btn-sm" v-if="archivo.gestionable && archivo.estado">
                                                                <i class="fa fa-columns" aria-hidden="true"></i>
                                                            </button>
                                                            <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                                <i class="fa fa-columns" aria-hidden="true"></i>
                                                            </button>
                                                        </td>
                                                        <td class="text-center">
                                                            <a type="button" title="Visualizar" :href="archivo.ruta" target="_blank" class="btn rounded btn-info btn-sm rutabtn" v-if="archivo.estado"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                                            <a type="button" href="#" class="btn rounded btn-secondary btn-sm" v-else><i class="fa fa-eye" aria-hidden="true"></i></a>
                                                            <button type="button" title="Editar" @click="abrirModal(archivoRuta +'Actualizar', archivo)" class="btn rounded btn-warning btn-sm" v-if="archivo.estado && permisosUser.actualizar">
                                                                <i class="icon-pencil" aria-hidden="true"></i>
                                                            </button>
                                                            <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                                <i class="icon-pencil" aria-hidden="true"></i>
                                                            </button>
                                                            <button type="button" @click="actualizarEstado(archivo.estado, 'este archivo', archivoRuta, archivo.id)" class="btn rounded btn-sm" :class="'btn-'+ (archivo.estado ? 'danger':'success')" :title="archivo.estado ? 'Anular':'Activar'" v-if="permisosUser.anular">
                                                                <i :class="'icon-'+ (archivo.estado ? 'trash':'check')" aria-hidden="true"></i>
                                                            </button>
                                                            <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                                                <i :class="'icon-'+ (archivo.estado ? 'trash':'check')" aria-hidden="true"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                        <nav>
                                            <ul class="pagination">
                                                <li class="page-item" v-if="archivoPagin.current_page > 1">
                                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(archivoPagin.current_page - 1, archivoObjCons, archivoPagin, 3)">Ant</a>
                                                </li>
                                                <li class="page-item" v-for="page in archivoPagesNumber" :key="page" :class="[page == archivoPagin.current_page ? 'active' : '']">
                                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page, archivoObjCons, archivoPagin, 3)" v-text="page"></a>
                                                </li>
                                                <li class="page-item" v-if="archivoPagin.current_page < archivoPagin.last_page">
                                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(archivoPagin.current_page + 1, archivoObjCons, archivoPagin, 3)">Sig</a>
                                                </li>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                                <!--Fin tabla carpetas-->
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-pill btn-primary btn-sm" @click="archivoCerrarMdl()"><i class="fa fa-reply" aria-hidden="true"></i>&nbsp;Atrás</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!--Modal archivos agregar/actualizar-->
            <div id="archivocarpeta-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-primary" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="archivoTituloMdl +' archivo'"></h4>
                            <button type="button" class="close" @click="archivoCerrarMdlMdl()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="formArchivo" action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                                <div class="form-row justify-content-center">
                                    <div class="form-group col-md-8">
                                        <label>Archivo</label>
                                        <input type="text" ref="archivoBArchivo" v-model="archivoBody.archivo" class="form-control" :class="{'is-invalid' : noHayArchivo}">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Código</label>
                                        <input type="text" v-model="archivoBody.codigo" class="form-control" :class="{'is-invalid' : noHayCodigo}">
                                    </div>
                                </div>
                                <div class="form-row justify-content-center">
                                    <div class="form-group col-md-8">
                                        <label>Subir archivo</label>
                                        <input type="file" class="form-control form-control-file" id="inputArchivo"  @change="archivoObtener" :class="{'is-invalid' : noHayRuta || archivoInvalido}">
                                        <small class="form-text text-muted font-weight-bold">Archivos admitidos: PDF</small>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Versión</label>
                                        <input type="text" v-model="archivoBody.versionamiento" class="form-control" :class="{'is-invalid' : noHayVersionamiento}">
                                    </div>
                                </div>
                                <div class="form-row justify-content-center">
                                    <div class="form-group col-md-10">
                                        <label>Descripción (opcional)</label>
                                        <textarea v-model="archivoBody.descripcion" class="form-control" rows="1"></textarea>
                                    </div>
                                </div>
                                <div class="form-group row justify-content-center">
                                    <label class="col-md-auto col-form-label pr-1 pt-1" for="toggleGest">Gestionable</label>
                                    <div class="col-md-auto pl-1">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input class="form-check-input" type="checkbox" id="toggleGest" v-model="archivoBody.gestionable">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-pill btn-danger btn-sm" @click="archivoCerrarMdlMdl()">Cerrar</button>
                            <button type="button" class="btn btn-pill btn-success btn-sm" @click="tipoAccion == 'Guardar' ? archivoRegistrar() : archivoActualizar()" v-text="tipoAccion"></button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- MODAL ARCHIVO-PERMISOS -->
            <div id="archivopermisos-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-primary" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="'Archivo - '+ archivoTituloMdl"></h4>
                            <button type="button" class="close" @click="archivoPermisosCerrarMdl()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-row">
                                <div class="form-group col">
                                    <table class="table table-bordered table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th class="text-center align-middle">#</th>
                                                <th class="text-center align-middle">Cargo</th>
                                                <th class="text-center">
                                                    <div class="col">Crear</div>
                                                    <div class="col"><input type="checkbox" ref="chkCrear" @click="alternarPermisos('crear', $event)"></div>
                                                </th>
                                                <th class="text-center">
                                                    <div class="col">Ver todos</div>
                                                    <div class="col"><input type="checkbox" ref="chkVer" @click="alternarPermisos('ver', $event)"></div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template v-if="cargoArr.length == 0">
                                                <tr>
                                                    <td class="text-center" colspan="4">No hay registros</td>
                                                </tr>
                                            </template>
                                            <template v-else>
                                                <tr v-for="(cargo, index) in cargoArr" :key="cargo.id">
                                                    <td class="text-center" v-text="index + 1"></td>
                                                    <td class="text-center" v-text="cargo.cargo"></td>
                                                    <td class="text-center">
                                                        <input type="checkbox" v-model="cargo.crear">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="checkbox" v-model="cargo.ver">
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-pill btn-danger btn-sm" @click="archivoPermisosCerrarMdl()">Cerrar</button>
                            <button type="button" class="btn btn-pill btn-success btn-sm" @click="archivoPermisosRegistrar()">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- MODAL ARCHIVO-CATEGORIAS -->
            <div id="categoriaarchivo-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-primary modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="'Archivo - '+ archivoTituloMdl"></h4>
                            <button type="button" class="close" @click="categoriaArchivoMdlCerrar()" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-row">
                                <div class="form-group col">
                                    <div class="form-row">
                                        <div class="form-group col mb-0">
                                            <label class="col-md-auto pl-1">Categoría</label>
                                            <div class="form-group row">
                                                <div class="col-9 pr-1">
                                                    <input type="text" ref="categoriaArchivoBCategoriaArchivo" class="form-control" :class="{'is-invalid': noHayCategoriaArchivo}" v-model="categoriaArchivoBody.categoriaArchivo" @keyup.enter="categoriaArchivoRegistrar()">
                                                </div>
                                                <button type="button" class="btn btn-success btn-pill btn-sm" @click="categoriaArchivoRegistrar();$refs.categoriaArchivoBCategoriaArchivo.focus();">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Guardar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col">
                                            <table class="table table-bordered table-striped table-sm">
                                                <thead>
                                                    <th class="text-center">#</th>
                                                    <th class="text-center">Categoría</th>
                                                    <th class="text-center">Listar opciones</th>
                                                    <th class="text-center">Descartar</th>
                                                </thead>
                                                <tbody>
                                                    <template v-if="categoriaArchivoArr.length == 0">
                                                        <tr>
                                                            <th class="text-center" colspan="4">No hay registros</th>
                                                        </tr>
                                                    </template>
                                                    <template v-else>
                                                        <tr v-for="(categoriaArchivo, index) in categoriaArchivoArr" :key="categoriaArchivo.id">
                                                            <td class="text-center" v-text="index + 1"></td>
                                                            <td v-text="categoriaArchivo.categoriaArchivo"></td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-sm btn-info rounded" @click="abrirModal(categoriaOpcionRuta +'Listar', {id: categoriaArchivo.id});$refs.categoriaOpcionBCategoriaOpcion.focus();">
                                                                    <i class="fa fa-list-ol" aria-hidden="true"></i>
                                                                </button>
                                                            </td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-sm btn-danger rounded" @click="recursoDescartar(index, categoriaArchivo.id, categoriaArchivoRuta)">
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
                                <div class="form-group col">
                                    <div class="form-row">
                                        <div class="form-group col mb-0">
                                            <label class="col-md-auto pl-1">Opción</label>
                                            <div class="form-group row">
                                                <div class="col-9 pr-1">
                                                    <input type="text" ref="categoriaOpcionBCategoriaOpcion" class="form-control" :class="{'is-invalid': noHayCategoriaOpcion}" v-model="categoriaOpcionBody.categoriaOpcion" @keyup.enter="categoriaOpcionRegistrar()">
                                                </div>
                                                <button type="button" class="btn btn-success btn-pill btn-sm" @click="categoriaOpcionRegistrar();$refs.categoriaOpcionBCategoriaOpcion.focus();" v-if="categoriaOpcionBody.fk_id_categorias_archivos">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Guardar
                                                </button>
                                                <button type="button" class="btn btn-secondary btn-pill btn-sm" v-else>
                                                    <i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Guardar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col">
                                            <table class="table table-bordered table-striped table-sm">
                                                <thead>
                                                    <th class="text-center">#</th>
                                                    <th class="text-center">Opción</th>
                                                    <th class="text-center">Descartar</th>
                                                </thead>
                                                <tbody>
                                                    <template v-if="categoriaOpcionArr.length == 0">
                                                        <template v-if="categoriaOpcionBody.fk_id_categorias_archivos">
                                                            <tr>
                                                                <th class="text-center" colspan="3">No hay registros</th>
                                                            </tr>
                                                        </template>
                                                    </template>
                                                    <template v-else>
                                                        <tr v-for="(categoriaOpcion, index) in categoriaOpcionArr" :key="categoriaOpcion.id">
                                                            <td class="text-center" v-text="index + 1"></td>
                                                            <td v-text="categoriaOpcion.categoriaOpcion"></td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-sm btn-danger rounded" @click="recursoDescartar(index, categoriaOpcion.id, categoriaOpcionRuta)">
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
                            <button type="button" class="btn btn-pill btn-primary btn-sm" @click="categoriaArchivoMdlCerrar()">Cerrar</button>
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
                segmentoRuta : 'segmentos'
                , carpetaRuta : 'segmentos_carpetas'
                , archivoRuta : 'segmentos_archivos'
                , categoriaArchivoRuta : 'categorias_archivos'
                , categoriaOpcionRuta : 'categorias_opciones'
                
                , segmentoObjCons : {
                    '?page' : 1
                    , leer : this.permisosUser.leer
                    , segmento : ''
                    , indiceTipoSegmento : ''
                    , numRegs : 10
                }
                , segmentoArr : []
                , segmentoBody : {
                  segmento : ''  
                  , tipoSegmento : ''
                  , observacion : ''
                }
                , segmentoId : 0

                , segmentoTituloMdl : 'Registrar'

                , carpetaObjCons : {
                    '?page' : 1
                    , fkIdSegmentos : 0
                    , leer : this.permisosUser.leer
                    , carpeta : ''
                    , fkIdTiposCarpetas : ''
                    , numRegs : 10
                }
                , tiposCarpetasArr : []
                , carpetaArr :  []
                , carpetaBody : {
                    carpeta : ''
                    , descripcion : ''
                    , fk_id_segmentos : 0
                    , fk_id_tipos_carpetas : ''
                }
                , carpetaId : 0

                , carpetaTituloMdl : 'Registrar'

                , archivoObjCons : {
                    '?page' : 1
                    , fkIdSegmentosCarpetas : 0
                    , leer : this.permisosUser.leer
                    , nombreCol : 'archivo'
                    , textoCol : ''  
                    , numRegs : 10
                }
                , archivoArr : []
                , archivoBody : {
                    archivo : ''
                    , codigo : ''
                    , descripcion : ''
                    , versionamiento : ''
                    , ruta : ''
                    , gestionable: 0
                    , fk_id_segmentos_carpetas : 0
                }
                , archivoId : 0
                
                , archivoTituloMdl : 'Registrar'
                
                , tipoAccion : 'Guardar'
                , segmentoOnVacios : false
                , carpetaOnVacios : false
                , archivoOnVacios : false

                , cargoArr : []

                , categoriaArchivoArr : []
                , categoriaArchivoBody : {
                    categoriaArchivo : ''
                    , fk_id_segmentos_archivos: 0
                }
                , categoriaArchivoId : 0
                , categoriaArchivoOnVacios : false
                
                , categoriaOpcionArr : []
                , categoriaOpcionBody : {
                    categoriaOpcion : ''
                    , fk_id_categorias_archivos : 0
                }
                , categoriaOpcionId : 0
                , categoriaOpcionOnVacios : false

                , indice : 0

                , segmentoPagin : {
                    total : 0
                    , current_page : 0
                    , per_page : 0
                    , last_page : 0
                    , from : 0
                    , to : 0
                }
                , carpetaPagin : {
                    total : 0
                    , current_page : 0
                    , per_page : 0
                    , last_page : 0
                    , from : 0
                    , to : 0
                }
                , archivoPagin : {
                    total : 0
                    , current_page : 0
                    , per_page : 0
                    , last_page : 0
                    , from : 0
                    , to : 0
                }
                , offset : 3
            }
        }
        , computed :
        {
            // VALIDACIÓN DE CAMPOS VACIOS EN FORMULARIOS
            //Campos segmento
            noHaySegmento : function ()
            {
                return this.segmentoOnVacios && !this.segmentoBody.segmento.trim();
            }
            , noHayTipoSegmento : function ()
            {
                return this.segmentoOnVacios && !this.segmentoBody.tipoSegmento;
            }
            , noHayObservacion : function ()
            {
                return this.segmentoOnVacios && !this.segmentoBody.observacion.trim();
            }
            //Campos carpeta
            , noHayCarpeta : function ()
            {
                return this.carpetaOnVacios && !this.carpetaBody.carpeta.trim();
            }
            , noHayDescripcion : function ()
            {
                return this.carpetaOnVacios && !this.carpetaBody.descripcion.trim();
            }
            , noHayTipoCarpeta : function ()
            {
                return this.carpetaOnVacios && !this.carpetaBody.fk_id_tipos_carpetas;
            }
            // Campos archivo
            , noHayArchivo : function ()
            {
                return this.archivoOnVacios && !this.archivoBody.archivo.trim();
            }
            , noHayCodigo : function ()
            {
                return this.archivoOnVacios && !this.archivoBody.codigo.trim();
            }
            , noHayVersionamiento : function ()
            {
                return this.archivoOnVacios && !this.archivoBody.versionamiento.trim();
            }
            , noHayRuta : function ()
            {
                return this.archivoOnVacios && !this.archivoBody.ruta;
            }
            , archivoInvalido : function () {
                let invalido = false;
                if (typeof this.archivoBody.ruta == 'object') {
                    if (this.archivoBody.ruta.type != 'application/pdf') {
                        invalido = true;
                    }
                }
                return this.archivoOnVacios && invalido;
            }
            // Campos categoria archivo
            , noHayCategoriaArchivo: function ()
            {
                return this.categoriaArchivoOnVacios && !this.categoriaArchivoBody.categoriaArchivo.trim();
            }
            // Campos categoria opcion
            , noHayCategoriaOpcion: function ()
            {
                return this.categoriaOpcionOnVacios && !this.categoriaOpcionBody.categoriaOpcion.trim();
            }
            /*isActived: function (refPagination)
            {
                return refPagination.current_page;
            },*/
            //Calcula los elementos de la paginación
            , segmentoPagesNumber: function ()
            {
               if (!this.segmentoPagin.to) {
                    return [];
                }
                let from = this.segmentoPagin.current_page - this.offset; 
                if (from < 1) {
                    from = 1;
                }
                let to = from + (this.offset * 2);
                if (to >= this.segmentoPagin.last_page) {
                    to = this.segmentoPagin.last_page;
                }  
                let pagesArray = [];
                while (from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;
            }
            , carpetaPagesNumber: function ()
            {
               if (!this.carpetaPagin.to) {
                    return [];
                }
                let from = this.carpetaPagin.current_page - this.offset; 
                if (from < 1) {
                    from = 1;
                }
                let to = from + (this.offset * 2);
                if (to >= this.carpetaPagin.last_page) {
                    to = this.carpetaPagin.last_page;
                }  
                let pagesArray = [];
                while (from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;
            }
            , archivoPagesNumber: function ()
            {
               if (!this.archivoPagin.to) {
                    return [];
                }
                let from = this.archivoPagin.current_page - this.offset; 
                if (from < 1) {
                    from = 1;
                }
                let to = from + (this.offset * 2);
                if (to >= this.archivoPagin.last_page) {
                    to = this.archivoPagin.last_page;
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
            abrirModal (recursoAccion, data = {})
            {
                switch (recursoAccion) {
                    case this.segmentoRuta +'Registrar':
                        $('#segmento-mdl').modal('show');
                        break;
                    case this.segmentoRuta +'Actualizar':
                        this.segmentoTituloMdl = 'Editar';
                        this.segmentoId = data.id;
                        this.segmentoBody.segmento = data.segmento;
                        this.segmentoBody.tipoSegmento = data.tipoSegmento == 'HSE' ? 1 : 2;
                        this.segmentoBody.observacion = data.observacion;
                        this.tipoAccion = 'Actualizar';
                        $('#segmento-mdl').modal('show');
                        break;
                    case this.carpetaRuta +'Listar':
                        this.segmentoTituloMdl = 'Segmento - '+ data.segmento;
                        this.carpetaObjCons.fkIdSegmentos = this.carpetaBody.fk_id_segmentos = data.id;
                        this.selectTiposCarpetas();
                        this.carpetaListar();
                        $('#carpetasegmentocrud-mdl').modal('show');
                        break;
                    case this.carpetaRuta +'Registrar':
                        $('#carpetasegmentocrud-mdl').modal('hide');
                        $('#carpetasegmento-mdl').modal('show');
                        break;
                    case this.carpetaRuta +'Actualizar':
                        this.carpetaTituloMdl = 'Editar';
                        this.carpetaId = data.id;
                        this.carpetaBody.carpeta = data.carpeta;
                        this.carpetaBody.descripcion = data.descripcion;
                        this.carpetaBody.fk_id_tipos_carpetas = data.fk_id_tipos_carpetas;
                        this.tipoAccion = 'Actualizar';
                        $('#carpetasegmentocrud-mdl').modal('hide');
                        $('#carpetasegmento-mdl').modal('show');
                        break;
                    case this.archivoRuta +'Listar':
                        this.carpetaTituloMdl = 'Carpeta - '+ data.carpeta;
                        this.archivoObjCons.fkIdSegmentosCarpetas = this.archivoBody.fk_id_segmentos_carpetas = data.id;
                        this.archivoListar();
                        $('#carpetasegmentocrud-mdl').modal('hide');
                        $('#archivocarpetacrud-mdl').modal('show');
                        break;
                    case this.archivoRuta +'Registrar':
                        $('#archivocarpetacrud-mdl').modal('hide');
                        $('#archivocarpeta-mdl').modal('show');
                        break;
                    case this.archivoRuta +'Actualizar':
                        this.archivoTituloMdl = 'Editar';
                        this.archivoId = data.id;
                        this.archivoBody.archivo = data.archivo;
                        this.archivoBody.codigo = data.codigo;
                        this.archivoBody.versionamiento = data.versionamiento;
                        this.archivoBody.ruta = data.ruta;
                        this.archivoBody.descripcion = data.descripcion;
                        this.archivoBody.gestionable = data.gestionable;
                        this.tipoAccion = 'Actualizar';
                        $('#archivocarpetacrud-mdl').modal('hide');
                        $('#archivocarpeta-mdl').modal('show');
                        break;
                    case this.archivoRuta +'Permisos':
                        this.archivoTituloMdl = data.archivo;
                        this.archivoId = data.id;
                        this.archivoPermisosListar();
                        $('#archivocarpetacrud-mdl').modal('hide');
                        $('#archivopermisos-mdl').modal('show');
                        break;
                    case this.categoriaArchivoRuta +'Listar':
                        this.archivoTituloMdl = data.archivo;
                        this.archivoId = this.categoriaArchivoBody.fk_id_segmentos_archivos = data.id;
                        this.categoriaArchivoListar();
                        $('#archivocarpetacrud-mdl').modal('hide');
                        $('#categoriaarchivo-mdl').modal('show');
                        break;
                    case this.categoriaOpcionRuta +'Listar':
                        this.categoriaArchivoId = this.categoriaOpcionBody.fk_id_categorias_archivos = data.id;
                        this.categoriaOpcionListar();
                        break;
                }
            }
            , cambiarPagina (page, refObjCons, refPagin, listar)
            {
                //Actualiza la página actual
                refObjCons['?page'] = refPagin.current_page = page;
                //Envia la petición para visualizar la data de esa página
                switch (listar) {
                    case 1: this.segmentoListar();break;
                    case 2: this.carpetaListar();break;
                    case 3: this.archivoListar();break;
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
                    if (ruta.endsWith('desactivar/') || ruta.endsWith('activar/'))Swal.fire(me.tituloSwal, me.descripcionSwal, 'success');
                    switch (ruta) {
                        case me.segmentoRuta:
                            me.segmentoArr = respuesta.segmentos.data;
                            me.segmentoPagin = respuesta.pagination;
                            break;
                        case me.segmentoRuta +'/registrar':
                        case me.segmentoRuta +'/actualizar/':
                            me.segmentoCerrarMdl();
                            me.segmentoListar();
                            break;
                        case me.segmentoRuta +'/desactivar/':
                        case me.segmentoRuta +'/activar/':
                            me.segmentoListar();
                            break;
                        case '/tipos_carpetas':
                            me.tiposCarpetasArr = respuesta.tiposCarpetas;
                            break;
                        case me.carpetaRuta:
                            me.carpetaArr = respuesta.carpetas.data;
                            me.carpetaPagin = respuesta.pagination;
                            break;
                        case me.carpetaRuta +'/registrar':
                        case me.carpetaRuta +'/actualizar/':
                            me.carpetaCerrarMdlMdl();
                            me.carpetaListar();
                            break;
                        case me.carpetaRuta +'/desactivar/':
                        case me.carpetaRuta +'/activar/':
                            me.carpetaListar();
                            break;
                        case me.archivoRuta:
                            me.archivoArr = respuesta.archivos.data;
                            me.archivoPagin = respuesta.pagination;
                            break;
                        case me.archivoRuta +'/desactivar/':
                        case me.archivoRuta +'/activar/':
                            me.archivoListar();
                            break;
                        case me.archivoRuta +'/cargos':
                            me.cargoArr = respuesta.cargos;
                            break;
                        case me.archivoRuta +'/permisos':
                            respuesta.permisos.forEach((permiso) => {
                                me.cargoArr.forEach((cargo) => {
                                    if (cargo.id == permiso.fk_id_cargos) {
                                        cargo.crear = permiso.crear;
                                        cargo.ver = permiso.ver;
                                    }
                                });
                            }, me);
                            break;
                        case me.archivoRuta +'/permisos/registrar':
                            me.archivoPermisosCerrarMdl();
                            break;
                        case me.categoriaArchivoRuta:
                            me.categoriaArchivoArr = respuesta.categoriasArchivos;
                            break;
                        case me.categoriaArchivoRuta +'/registrar':
                            me.categoriaArchivoBody.categoriaArchivo = '';
                            me.categoriaArchivoListar();
                            break;
                        case me.categoriaArchivoRuta +'/descartar/':
                            // EN CASO DE ELIMINAR UNA CATEGORIA DIFERENTE DE AQUELLA QUE LISTA LAS OPCIONES
                            if (me.categoriaOpcionBody.fk_id_categorias_archivos == me.categoriaArchivoArr[me.indice].id) {
                                me.categoriaOpcionBody.fk_id_categorias_archivos = 0;
                                me.categoriaOpcionArr = [];
                            }
                            me.categoriaArchivoArr.splice(me.indice, 1);
                            me.indice = 0;
                            me.$refs.categoriaArchivoBCategoriaArchivo.focus();
                            break;
                        case me.categoriaOpcionRuta:
                            me.categoriaOpcionArr = respuesta.categoriasOpciones;
                            break;
                        case me.categoriaOpcionRuta +'/registrar':
                            me.categoriaOpcionBody.categoriaOpcion = '';
                            me.categoriaOpcionListar();
                            break;
                        case me.categoriaOpcionRuta +'/descartar/':
                            me.categoriaOpcionArr.splice(me.indice, 1);
                            break;
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
            }
            , esRecursoInvalido (validar) {
                switch (validar) {
                    case 1: if (this.segmentoValidar())return true;
                        break;
                    case 2: if (this.carpetaValidar())return true;
                        break;
                    case 3: if (this.archivoValidar())return true;
                        break;
                }
                return false;
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
            , peticionDelete (ruta, recursoId) {
                let peticion = this.axiosApp.delete(ruta + recursoId);
                this.obtenerRespuesta(ruta, peticion);
            }
            , segmentoListar ()
            {
                this.peticionGet(this.segmentoRuta, this.obtenerCadCons(this.segmentoObjCons));
            }
            , segmentoValidar ()
            {                
                return this.noHaySegmento || this.noHayTipoSegmento || this.noHayObservacion;
            }
            , segmentoRegistrar ()
            {
                this.segmentoOnVacios = true;
                let ruta = this.segmentoRuta +'/registrar';
                this.peticionPost(ruta, this.segmentoBody, 1);
            }
            , segmentoActualizar ()
            {
                this.segmentoOnVacios = true;
                let ruta = this.segmentoRuta +'/actualizar/';
                this.peticionPut(ruta, this.segmentoId, this.segmentoBody, 1);
            }
            , segmentoCerrarMdl ()
            {
                this.segmentoTituloMdl = 'Registrar';
                this.segmentoId = 0;
                this.segmentoBody.segmento = this.segmentoBody.tipoSegmento = this.segmentoBody.observacion = '';
                this.tipoAccion = 'Guardar';
                this.segmentoOnVacios = false;
                $('#segmento-mdl').modal('hide');
            }
            , selectTiposCarpetas ()
            {
                let uri = '/tipos_carpetas';
                this.peticionGet(uri);
            }
            , carpetaListar ()
            {
                this.peticionGet(this.carpetaRuta, this.obtenerCadCons(this.carpetaObjCons));
            }
            , carpetaValidar ()
            {
                return this.noHayCarpeta || this.noHayDescripcion || this.noHayTipoCarpeta;
            }
            , carpetaRegistrar ()
            {
                this.carpetaOnVacios = true;
                let ruta = this.carpetaRuta +'/registrar';
                this.peticionPost(ruta, this.carpetaBody, 2);
            }
            , carpetaActualizar ()
            {
                this.carpetaOnVacios = true;
                let ruta = this.carpetaRuta +'/actualizar/';
                this.peticionPut(ruta, this.carpetaId, this.carpetaBody, 2);
            }   
            , carpetaCerrarMdl ()
            {
                this.segmentoTituloMdl = 'Registrar';
                this.carpetaObjCons.fkIdSegmentos = this.carpetaBody.fk_id_segmentos = this.segmentoId = 0;
                this.carpetaObjCons.carpeta = this.carpetaObjCons.fkIdTiposCarpetas = '';
                this.carpetaArr = [];
                $('#carpetasegmentocrud-mdl').modal('hide');
            }
            , carpetaCerrarMdlMdl ()
            {
                this.carpetaTituloMdl = 'Registrar';
                this.carpetaBody.carpeta = this.carpetaBody.descripcion = this.carpetaBody.fk_id_tipos_carpetas = '';
                this.tipoAccion = 'Guardar';
                this.carpetaOnVacios = false;
                $('#carpetasegmento-mdl').modal('hide');
                $('#carpetasegmentocrud-mdl').modal('show');
            }
            , archivoListar ()
            {
                this.peticionGet(this.archivoRuta, this.obtenerCadCons(this.archivoObjCons));
            }
            , archivoValidar ()
            {
                return this.noHayArchivo || this.noHayCodigo || this.noHayVersionamiento || this.noHayRuta || this.archivoInvalido;
            }
            , archivoObtener (event)
            {
                this.archivoBody.ruta = event.target.files[0];
            }
            , archivoRegistrar ()
            {
                this.archivoOnVacios = true;
                if (this.archivoValidar()) return;
                let archivoBody = new FormData(), ruta = this.archivoRuta +'/registrar', me = this;
                archivoBody.append('archivo', this.archivoBody.archivo);
                archivoBody.append('codigo', this.archivoBody.codigo);
                archivoBody.append('versionamiento', this.archivoBody.versionamiento);
                archivoBody.append('ruta', this.archivoBody.ruta);
                archivoBody.append('descripcion', this.archivoBody.descripcion);
                archivoBody.append('gestionable', this.archivoBody.gestionable);
                archivoBody.append('fk_id_segmentos_carpetas', this.archivoBody.fk_id_segmentos_carpetas);
                axios.post(ruta, archivoBody, {headers:{'Content-Type':'multipart/form-data'}}
                ).then(function (response) {
                    me.archivoCerrarMdlMdl();
                    me.archivoListar();
                }).catch(function (error) {
                    console.log(error);
                });
            }
            , archivoActualizar ()
            {
                this.archivoOnVacios = true;
                if (this.archivoValidar())return;
                let archivoBody = new FormData(), ruta = this.archivoRuta +'/actualizar/'+ this.archivoId, me = this;
                archivoBody.append('archivo', this.archivoBody.archivo);
                archivoBody.append('codigo', this.archivoBody.codigo);
                archivoBody.append('versionamiento', this.archivoBody.versionamiento);
                archivoBody.append('ruta', this.archivoBody.ruta);
                archivoBody.append('descripcion', this.archivoBody.descripcion);
                archivoBody.append('gestionable', this.archivoBody.gestionable);
                archivoBody.append('_method', 'PUT');
                axios.post(ruta, archivoBody, {headers:{'Content-Type':'multipart/form-data'}}
                ).then(function (response) {
                    me.archivoCerrarMdlMdl();
                    me.archivoListar();
                }).catch(function (error) {
                    console.log(error);
                });
            }
            // SOLO SE EJECUTA EN EL mounted
            , cargosActivos ()
            {
                let ruta = this.archivoRuta +'/cargos';
                this.peticionGet(ruta);
            }
            , archivoPermisosListar ()
            {
                let ruta = this.archivoRuta +'/permisos', cadCons = '?fkIdSegmentosArchivos='+ this.archivoId;
                this.peticionGet(ruta, cadCons);
            }
            , archivoPermisosRegistrar ()
            {
                let ruta = this.archivoRuta +'/permisos/registrar';
                this.peticionPost(ruta, {fkIdSegmentosArchivos: this.archivoId, permisosArr: this.cargoArr}, 0);
            }
            , alternarPermisos (permiso, event)
            {
                let checkPadre = $(event.target).is(":checked") ? 1 : 0;
                this.cargoArr.forEach(cargo => cargo[permiso] = checkPadre);
            }
            , categoriaArchivoListar ()
            {
                this.peticionGet(this.categoriaArchivoRuta, '?fkIdSegmentosArchivos='+ this.archivoId);
            }
            , categoriaArchivoRegistrar ()
            {
                this.categoriaArchivoOnVacios = true;
                if (this.noHayCategoriaArchivo) return;
                this.categoriaArchivoOnVacios = false;
                let ruta = this.categoriaArchivoRuta +'/registrar';
                this.peticionPost(ruta, this.categoriaArchivoBody);
            }
            , categoriaOpcionListar ()
            {
                this.peticionGet(this.categoriaOpcionRuta, '?fkIdCategoriasArchivos='+ this.categoriaArchivoId);
            }
            , categoriaOpcionRegistrar ()
            {
                if (!this.categoriaOpcionBody.fk_id_categorias_archivos) return;
                this.categoriaOpcionOnVacios = true;
                if (this.noHayCategoriaOpcion) return;
                this.categoriaOpcionOnVacios = false;
                let ruta = this.categoriaOpcionRuta +'/registrar';
                this.peticionPost(ruta, this.categoriaOpcionBody);
            }
            , recursoDescartar (indice, recursoId, recursoRuta)
            {
                let ruta = recursoRuta +'/descartar/';
                this.indice = indice;
                this.peticionDelete(ruta, recursoId);
            }
            , archivoCerrarMdl ()
            {
                this.carpetaTituloMdl = 'Registrar';
                this.archivoObjCons.fkIdSegmentosCarpetas = this.archivoBody.fk_id_segmentos_carpetas = this.carpetaId= 0;
                this.archivoObjCons.nombreCol = this.archivoObjCons.textoCol = '';
                this.archivoArr = [];
                this.archivoObjCons.nombreCol = 'archivo';
                $('#archivocarpetacrud-mdl').modal('hide');
                $('#carpetasegmentocrud-mdl').modal('show');
            }
            , archivoCerrarMdlMdl ()
            {
                this.archivoTituloMdl = 'Registrar';
                this.archivoBody.archivo = this.archivoBody.codigo = this.archivoBody.versionamiento = this.archivoBody.ruta = this.archivoBody.descripcion = '';
                //if (this.archivoBody.gestionable) this.cargoArr.forEach(cargo => cargo.crear = cargo.ver = 0);
                this.archivoBody.gestionable = 0;
                this.tipoAccion = 'Guardar';
                $('#inputArchivo').val('');
                this.archivoOnVacios = false;
                $('#archivocarpeta-mdl').modal('hide');
                $('#archivocarpetacrud-mdl').modal('show');
            }
            , archivoPermisosCerrarMdl ()
            {
                this.archivoTituloMdl = 'Registrar';
                this.archivoId = 0;
                this.$refs.chkCrear.checked = this.$refs.chkVer.checked = false;
                this.cargoArr.forEach(cargo => cargo.crear = cargo.ver = 0);
                $('#archivopermisos-mdl').modal('hide');
                $('#archivocarpetacrud-mdl').modal('show');
            }
            , categoriaArchivoMdlCerrar ()
            {
                this.archivoTituloMdl = 'Registrar';
                this.archivoId = this.categoriaArchivoId = 0;
                this.categoriaArchivoArr = [];
                this.categoriaOpcionArr = [];
                this.categoriaArchivoBody.categoriaArchivo = this.categoriaArchivoBody.fk_id_segmentos_archivos = this.categoriaOpcionBody.categoriaOpcion = this.categoriaOpcionBody.fk_id_categorias_archivos = '';
                this.categoriaArchivoOnVacios = this.categoriaOpcionOnVacios = false;
                $('#categoriaarchivo-mdl').modal('hide');
                $('#archivocarpetacrud-mdl').modal('show');
                
            }
            , actualizarEstado (recursoEstado, finalTitle, recursoRuta, recursoId)
            {
                let prefijo = recursoEstado? 'des':'';
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
                    ) {
                    }
                });
            }
        }
        , mounted ()
        {
            this.segmentoListar();
            this.cargosActivos();
            this.$refs.segmentoOCSegmento.focus();
            $('#segmento-mdl').on('shown.bs.modal', () => this.$refs.segmentoBSegmento.focus());
            $('#segmento-mdl').on('hidden.bs.modal', () => this.$refs.segmentoOCSegmento.focus());
            $('#carpetasegmentocrud-mdl').on('shown.bs.modal', () => this.$refs.carpetaOCCarpeta.focus());
            $('#carpetasegmentocrud-mdl').on('hidden.bs.modal', () => this.$refs.segmentoOCSegmento.focus());
            $('#carpetasegmento-mdl').on('shown.bs.modal', () => this.$refs.carpetaBCarpeta.focus());
            $('#archivocarpetacrud-mdl').on('shown.bs.modal', () => this.$refs.archivoOCColumnaVal.focus());
            $('#archivocarpeta-mdl').on('shown.bs.modal', () => this.$refs.archivoBArchivo.focus());
            $('#categoriaarchivo-mdl').on('shown.bs.modal', () => this.$refs.categoriaArchivoBCategoriaArchivo.focus());
        }
    }
</script>
<style>
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
</style>