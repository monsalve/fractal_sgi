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
                        <i class="fa fa-align-justify" aria-hidden="true"></i>&nbsp;Roles&nbsp;
                        <button type="button" @click="abrirModal('roles','registrar')" class="btn btn-pill btn-primary btn-sm">
                            <i class="icon-plus" aria-hidden="true"></i>&nbsp;Nuevo
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-auto my-1 pr-1">Nombre</label>
                            <div class="col-md-3 pl-1">
                                <input type="text" v-model="buscar" @keyup.enter="listarRol(1,buscar,criterio)" class="form-control">
                            </div>
                            <label class="col-md-auto my-1 pr-1">Estado</label>
                            <div class="col-md-auto pl-1">
                                <select class="form-control" v-model="estado">
                                    <option value="">Todos</option>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                            <button type="button" @click="listarRol(1,buscar,criterio)" class="btn btn-pill btn-primary btn-sm">
                                <i class="fa fa-search" aria-hidden="true"></i>&nbsp;Buscar
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-md-10">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Nombre</th>
                                            <th class="text-center">Estado</th>
                                            <th class="text-center">Opciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template v-if="arrayRol.length == 0">
                                                <tr>
                                                    <td class="text-center" colspan="4">No hay registros</td>
                                                </tr>
                                        </template>
                                        <template v-else>    
                                            <tr v-for="(rol, index) in arrayRol" :key="rol.nombre">
                                                <td class="text-center" v-text="index + 1"></td>
                                                <td v-text="rol.nombre"></td>
                                                <td class="text-center">
                                                    <div v-if="rol.estado">
                                                        <span class="badge badge-success">Activo</span>
                                                    </div>
                                                    <div v-else>
                                                        <span class="badge badge-danger">Inactivo</span>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <button  v-if="rol.estado" type="button" @click="abrirModal('roles','actualizar',rol)" class="btn rounded btn-warning btn-sm">
                                                      <i class="icon-pencil" aria-hidden="true"></i>
                                                    </button>
            
                                                    <button  v-else type="button" class="btn rounded btn-secondary btn-sm">
                                                      <i class="icon-pencil" aria-hidden="true"></i>
                                                    </button>
            
                                                    <template v-if="rol.estado">
                                                        <button type="button" class="btn rounded btn-danger btn-sm" @click="desactivarRol(rol.id)">
                                                            <i class="icon-trash" aria-hidden="true"></i>
                                                        </button>
                                                    </template>
                                                    <template v-else>
                                                        <button type="button" class="btn rounded btn-info btn-sm" @click="activarRol(rol.id)">
                                                            <i class="icon-check" aria-hidden="true"></i>
                                                        </button>
                                                    </template>
                                                </td>
                                            </tr>                                
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <nav>
                            <ul class="pagination">
                                <li class="page-item" v-if="pagination.current_page > 1">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar,criterio)">Ant</a>
                                </li>
                                <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar,criterio)" v-text="page"></a>
                                </li>
                                <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar,criterio)">Sig</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <!-- Fin ejemplo de tabla Listado -->

                <!--Inicio del modal agregar/actualizar-->
                <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                    <div class="modal-dialog modal-primary modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" v-text="tituloModal +' rol'"></h4>
                                <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                                <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                                    <div class="form-group row">
                                        <label class="col-md-auto my-1 pr-1">Nombre</label>
                                        <div class="col-md-5 pl-1">
                                            <input type="text" v-model="nombre" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-11 pr-0" style="height: 300px; overflow: auto;">
                                            <table class="table table-bordered table-striped table-sm col-md-11">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Módulo</th>
                                                        <th class="text-center">Crear</th>
                                                        <th class="text-center">Leer</th>
                                                        <th class="text-center">Actualizar</th>
                                                        <th class="text-center">Anular</th>
                                                        <th class="text-center">Imprimir</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(permisos, index) in arrayPermisos" :key="permisos.id">
                                                        <td v-if="permisos.tipo==1" v-text="permisos.nombre" style="background: #dae3e8;"></td>
                                                        <td v-else v-text="permisos.nombre" style="background: #ffffff; padding-left: 1em;"></td>
        
                                                        <td class="text-center" v-if="permisos.tipo==1" style="background: #dae3e8;"><input type="checkbox" @click="alternarMarcaHijos(index, 'escritura')" v-model="permisos.escritura"></td>
                                                        <td class="text-center" v-else style="background: #ffffff;"><input type="checkbox" @click="marcarPadre(index, 'escritura')" v-model="permisos.escritura"></td>

                                                        <td class="text-center" v-if="permisos.tipo==1" style="background: #dae3e8;"><input type="checkbox" @click="alternarMarcaHijos(index, 'lectura')" v-model="permisos.lectura"></td>
                                                        <td class="text-center" v-else style="background: #ffffff;"><input type="checkbox" @click="marcarPadre(index, 'lectura')" v-model="permisos.lectura"></td>
        
                                                        <td class="text-center" v-if="permisos.tipo==1" style="background: #dae3e8;"><input type="checkbox" @click="alternarMarcaHijos(index, 'edicion')" v-model="permisos.edicion"></td>
                                                        <td class="text-center" v-else style="background: #ffffff;"><input type="checkbox" @click="marcarPadre(index, 'edicion')" v-model="permisos.edicion"></td>
        
                                                        <td class="text-center" v-if="permisos.tipo==1" style="background: #dae3e8;"><input type="checkbox" @click="alternarMarcaHijos(index, 'anular')" v-model="permisos.anular"></td>
                                                        <td class="text-center" v-else style="background: #ffffff;"><input type="checkbox" @click="marcarPadre(index, 'anular')" v-model="permisos.anular"></td>
        
                                                        <td class="text-center" v-if="permisos.tipo==1" style="background: #dae3e8;"><input type="checkbox" @click="alternarMarcaHijos(index, 'imprimir')" v-model="permisos.imprimir"></td>
                                                        <td class="text-center" v-else style="background: #ffffff;"><input type="checkbox" @click="marcarPadre(index, 'imprimir')" v-model="permisos.imprimir"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div v-show="errorRol" class="form-group row div-error">
                                <div class="text-center text-error">
                                    <div v-for="error in errorMostrarMsjRol" :key="error" v-text="error">

                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-pill btn-danger btn-sm" @click="cerrarModal()">Cerrar</button>
                                <button type="button" v-if="tipoAccion==1" class="btn btn-pill btn-success btn-sm" @click="registrarRol()">Guardar</button>
                                <button type="button" v-if="tipoAccion==2" class="btn btn-pill btn-success btn-sm" @click="actualizarRol()">Actualizar</button>
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>
                <!--Fin del modal-->
            </div>
        </main>
</template>

<script>
    export default {
        props : ['ruta'],
        data (){
            return {
                rol_id: 0,
                nombre : '',
                validarNombre : '',
                id_modulo: 0,
                arrayRol : [],
                arrayNombresRoles:[],
                arrayPermisos : [],
                errorRol : [],
                errorMostrarMsjRol : [],
                modal : 0,
                tituloModal : '',
                tipoAccion : 0,
                pagination : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0,
                },
                offset : 3,
                criterio : 'nombre',
                buscar : '',
                estado : '',
            }
        },
        computed:{
            isActived: function(){
                return this.pagination.current_page;
            },
            //Calcula los elementos de la paginación
            pagesNumber: function() {
                if(!this.pagination.to) {
                    return [];
                }
                
                var from = this.pagination.current_page - this.offset; 
                if(from < 1) {
                    from = 1;
                }

                var to = from + (this.offset * 2); 
                if(to >= this.pagination.last_page){
                    to = this.pagination.last_page;
                }  

                var pagesArray = [];
                while(from <= to) {
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;             

            }
        },
        methods : {
            alternarMarcaHijos (indiceModulo, accion)
            {
                console.log('INDICE DE MODULO -> '+ indiceModulo);
                let marcaHijos = this.arrayPermisos[indiceModulo][accion] = !this.arrayPermisos[indiceModulo][accion];
                for (let i = indiceModulo + 1; (i < (this.arrayPermisos.length)) ? (this.arrayPermisos[i].padre != null) ? true : false : false; i++) {
                    this.arrayPermisos[i][accion] = marcaHijos;
                }
            },
            marcarPadre (indiceModulo, accion)
            {
                for (var i = indiceModulo - 1; i >= 0; i--) {
                    if (this.arrayPermisos[i].padre == null) {
                        this.arrayPermisos[i][accion] = true;
                        break;
                    }
                }
            },
            listarRol (page,buscar,criterio){
                let me=this;
                
                var url= this.ruta+'/rol?page=' + page + '&buscar='+ buscar + '&criterio='+ criterio +'&estado='+ this.estado;
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayRol = respuesta.roles;
                    me.pagination= respuesta.pagination;
                    me.arrayNombresRoles = respuesta.roles2.data;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            listarPermisos(rol_id){
                let me=this;
                
                var url= this.ruta+'/rol/permisos?id_rol='+rol_id;
                axios.get(url).then(function (response) {
                    var respuesta= response.data;
                    me.arrayPermisos = respuesta.permisos;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            cambiarPagina(page,buscar,criterio){
                let me = this;
                //Actualiza la página actual
                me.pagination.current_page = page;
                //Envia la petición para visualizar la data de esa página
                me.listarRol(page,buscar,criterio);
            },
            registrarRol(){
                if (this.validarRol()){
                    return;
                }
                
                let me = this;

                axios.post(this.ruta +'/rol/registrar',{
                    'nombre': this.nombre,
                    'id_modulo': this.id_modulo,
                    'modulos' : this.arrayPermisos
                }).then(function (response) {
                    me.cerrarModal();
                    me.listarRol(1,'','nombre');
                }).catch(function (error) {
                    console.log(error);
                });
            },
            actualizarRol(){
                if (this.validarRol()){
                    return;
                }
                
                let me = this;

                axios.put(this.ruta +'/rol/actualizar',{
                    'nombre': this.nombre,
                    'modulos' : this.arrayPermisos,
                    'id' : this.rol_id,
                }).then(function (response) {
                    me.cerrarModal();
                    me.listarRol(1,'','nombre');
                }).catch(function (error) {
                    console.log(error);
                });
            },
            validarRol(){
                let me = this;
                me.errorRol=0;
                me.errorMostrarMsjRol =[];

                if (!me.nombre) me.errorMostrarMsjRol.push("El nombre de la Rol no puede estar vacío.");
                
                for(var r=0; r<me.arrayRol.length; r++)
                {
                    if(me.tipoAccion==1 && me.arrayRol[r]['nombre']==me.nombre) me.errorMostrarMsjRol.push("El nombre del rol ya se encuentra registrado");

                    if(me.tipoAccion==2 && me.validarNombre!=me.nombre && me.nombre==me.arrayRol[r]['nombre']) me.errorMostrarMsjRol.push("El nombre del rol ya se encuentra registrado");
                }

                let ban = 1;
                if(me.arrayPermisos.length)
                {
                    for (let i = 0; i < this.arrayPermisos.length; i++) {
                        if (this.arrayPermisos[i].lectura
                            || this.arrayPermisos[i].escritura
                            || this.arrayPermisos[i].edicion
                            || this.arrayPermisos[i].anular
                            || this.arrayPermisos[i].imprimir) {
                            ban = 0;
                            break;
                        }
                    }
                }

                if (ban) me.errorMostrarMsjRol.push("Debe asignar por lo menos un permiso.");

                if (me.errorMostrarMsjRol.length) me.errorRol = 1;

                return me.errorRol;
            },
            desactivarRol(id){
               Swal.fire({
                title: '¿Esta seguro de desactivar este rol?',
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
                    let me = this;
                    
                    axios.put(this.ruta +'/rol/desactivar',{
                        'id': id
                    }).then(function (response) {
                        if (response.data.hayUsuarios) {
                            Swal.fire('No es posible desactivar!', 'Hay usuarios activos con este rol', 'error');
                            return;
                        }
                        me.listarRol(1,'','nombre');
                        Swal.fire(
                        'Desactivado!',
                        'El rol ha sido desactivado con éxito.',
                        'success'
                        )
                    }).catch(function (error) {
                        console.log(error);
                    });
                    
                    
                } else if (
                    // Read more about handling dismissals
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    
                }
                }) 
            },
            activarRol(id){
               Swal.fire({
                title: '¿Esta seguro de activar este rol?',
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
                    let me = this;

                    axios.put(this.ruta +'/rol/activar',{
                        'id': id
                    }).then(function (response) {
                        me.listarRol(1,'','nombre');
                        Swal.fire(
                        'Activado!',
                        'El rol ha sido activado con éxito.',
                        'success'
                        )
                    }).catch(function (error) {
                        console.log(error);
                    });
                    
                    
                } else if (
                    // Read more about handling dismissals
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    
                }
                }) 
            },
            cerrarModal(){
                this.modal=0;
                this.tituloModal='';
                this.tipoAccion = 0;
                this.nombre = '';
                this.id_modulo = 0;

                this.rol_id = 0;
                this.errorRol = 0;
                this.errorMostrarMsjRol = [];
                this.arrayPermisos = [];
            },
            abrirModal(modelo, accion, data = []){
                let me = this;
                switch(modelo){
                    case "roles":
                    {
                        switch(accion){
                            case 'registrar':
                            {
                                this.modal = 1;
                                this.tituloModal = 'Registrar';
                                this.tipoAccion = 1;
                                this.nombre= '';
                                this.id_modulo = 0;
                                break;
                            }
                            case 'actualizar':
                            {
                                this.rol_id = data['id'];
                                this.modal=1;
                                this.tituloModal='Actualizar';
                                this.tipoAccion=2;
                                this.nombre = data['nombre'];
                                this.validarNombre = data['nombre'];
                                break;
                            }
                        }
                        this.listarPermisos(this.rol_id);
                    }
                }
            }
        },
        mounted() {
            this.listarRol(1,this.buscar,this.criterio);
        }
    }
</script>
<style>    
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .mostrar{
        display: list-item !important;
        opacity: 1 !important;
        position: absolute !important;
        background-color: #3c29297a !important;
    }
    .div-error{
        display: flex;
        justify-content: center;
    }
    .text-error{
        color: red !important;
        font-weight: bold;
    }
</style>
