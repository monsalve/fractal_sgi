<template>
    <main class="main">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Ajustes del sistema</a></li>
        </ol>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-auto">
                            <i class="fa fa-align-justify" aria-hidden="true"></i>&nbsp;Sistema&nbsp;
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <span class="display-4">HOLA, HAS INGRESADO A UN MÓDULO SUPER USUARIO :)</span>
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
                
            }
        }
    }
</script>