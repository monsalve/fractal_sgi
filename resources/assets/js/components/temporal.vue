<template>
    <main class="main">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Escritorio</a></li>
        </ol>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-auto">
                            <i class="fa fa-id-badge fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;Areas&nbsp;&nbsp;
                            <button type="button" @click="abrirModal(areaRuta +'Registrar')" class="btn btn-pill btn-primary btn-sm" v-if="permisosUser.crear">
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
                                <th class="text-center">Área</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-if="areaArr.length == 0">
                                <tr>
                                    <td class="text-center" colspan="4">No hay registros</td>
                                </tr>
                            </template>
                            <template v-else>
                                <tr v-for="(area, index) in areaArr" :key="area.id">
                                    <td class="text-center" v-text="index + 1"></td>
                                    <td v-text="area.area"></td>
                                    <td class="text-center">
                                        <span class="badge" :class="'badge-' + (area.estado ? 'success':'danger')" v-text="area.estado ? 'Activo':'Inactivo'"></span>
                                    </td>
                                    <!--<td class="text-center">
                                        <button type="button" @click="abrirModal(documentoRuta, {cargoId: cargo.id, cargo: cargo.cargo})" class="btn rounded btn-info btn-sm" title="Ver documentos" v-if="cargo.estado">
                                            <i class="fa fa-files-o" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                            <i class="fa fa-files-o" aria-hidden="true"></i>
                                        </button>
                                    </td>-->
                                    <td class="text-center">
                                        <button type="button" @click="abrirModal(areaRuta +'Actualizar', area)" class="btn rounded btn-warning btn-sm" title="Editar" v-if="area.estado && permisosUser.actualizar">
                                            <i class="icon-pencil" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                            <i class="icon-pencil" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" @click="actualizarEstado(area.estado, 'esta área', areaRuta, area.id)" class="btn rounded btn-sm" :class="'btn-'+ (area.estado ? 'danger':'success')" :title="area.estado ? 'Anular':'Activar'" v-if="permisosUser.anular">
                                            <i :class="'icon-'+ (area.estado ? 'trash':'check')" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" class="btn rounded btn-secondary btn-sm" v-else>
                                            <i :class="'icon-'+ (area.estado ? 'trash':'check')" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </tr>                                
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="area-mdl" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-primary" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="areaTituloMdl+ ' area'"></h4>
                        <button type="button" class="close" @click="areaMdlCerrar()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group row justify-content-center">
                                <label class="col-md-2 form-control-label">Área</label>
                                <div class="col-md-9">
                                    <input type="text" ref="areaBArea" v-model="areaBody.area" class="form-control" :class="{'is-invalid' : noHayArea}">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-pill btn-danger btn-sm" @click="areaMdlCerrar()">Cerrar</button>
                        <button type="button" class="btn btn-pill btn-success btn-sm" @click="tipoAccion == 'Guardar'? areaRegistrar() : areaActualizar()" v-text="tipoAccion"></button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>
<script>
export default {
    props : ['permisosUser', 'ruta', 'axiosApp']
    , data () {
        return {
            areaRuta : 'areas'

            , areaArr : []
            , areaId : 0
            , areaBody : {
                area : ''
            }
            , areaOnVacios : false
            , areaTituloMdl : 'Registrar'
            , tipoAccion : 'Guardar'
        }
    }
    , computed:
    {
        noHayArea : function ()
        {
            return this.areaOnVacios && !this.areaBody.area.trim();
        }
    }
    , methods:
    {
        abrirModal (accion, data = {})
        {
            switch (accion) {
                case this.areaRuta +'Registrar':
                    $('#area-mdl').modal('show');
                    break;
                case this.areaRuta +'Actualizar':
                    this.areaId = data.id;
                    this.areaBody.area = data.area;
                    $('#area-mdl').modal('show');
                    break;
            }
        }
        , areaListar ()
        {
            this.peticionGet(this.areaRuta);
        }
        , areaRegistrar ()
        {
            let ruta = this.areaRuta +'/registrar';
            this.peticionPost(ruta, this.areaBody, 1);
        }
        , areaActualizar ()
        {
            let ruta = this.areaRuta +'/actualizar/';
            this.peticionPut(ruta, this.areaId, this.areaBody, 1);
        }
        , areaMdlCerrar ()
        {
            $('#area-mdl').modal('hide');
            this.areaTituloMdl = 'Registrar';
            this.areaId = this.areaBody.area = '';
            this.areaOnVacios = false;
            this.tipoAccion = 'Guardar';
        }
        , areaValidar ()
        {
            return this.noHayArea;
        }
        , actualizarEstado (recursoEstado, finalTitle, recursoRuta, recursoId)
            {
                let prefijo = recursoEstado ? 'des' : '';
                Swal.fire({
                    title: `¿Esta seguro de ${prefijo}activar ${finalTitle}?`
                    , type: 'warning'
                    , showCancelButton: true
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
        , peticionGet (recursoRuta, cadCons = '')
        {
            let peticion = this.axiosApp.get(recursoRuta + cadCons);
            this.obtenerRespuesta(recursoRuta, peticion);
        }
        , esRecursoValido (validar)
        {
            switch (validar) {
                case 1: if (this.areaValidar()) return true;
                    break;
                // CASO POR RECURSO A VALIDAR
            }
            return false;
        }
        , peticionPost (recursoRuta, recursoBody, validar)
        {
            if (this.esRecursoValido(validar)) return;
            let peticion = this.axiosApp.post(recursoRuta, recursoBody);
            this.obtenerRespuesta(recursoRuta, peticion);
        }
        , peticionPut (recursoRuta, recursoId, recursoBody, validar)
        {
            if (this.esRecursoValido(validar)) return;
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
        , obtenerRespuesta (recursoRuta, peticion)
        {
            let me = this;
            peticion.then(function (response) {
                let respuesta = response.data;
                switch (recursoRuta)
                {
                    case me.areaRuta:
                        me.areaArr = respuesta.areas;
                        break;
                    case me.areaRuta +'/registrar':
                    case me.areaRuta +'/actualizar/':
                        $('#area-mdl').modal('hide');
                        me.areaMdlCerrar();
                        me.areaListar();
                        break;
                    case me.areaRuta +'/desactivar/':
                    case me.areaRuta +'/activar/':
                        me.areaListar();
                        break;
                }
            }).catch(error => console.log(error));
        }
    }
    , mounted ()
    {
        this.areaListar();
        $('#area-mdl').on('shown.bs.modal', () => this.$refs.areaBArea.focus());
    }
}
</script>