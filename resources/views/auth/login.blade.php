@extends('auth.contenido')

@section('login')
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card-group mb-0">
        <div class="card p-4">
          <div id="app">
            <form ref="loginForm" class="form-horizontal was-validated" method="POST" action="{{ route('login')}}">
              {{ csrf_field() }}
              <div class="card-body">
                <h1>Acceder</h1>
                <p class="text-muted">Control de acceso al sistema</p>
                <div class="form-group mb-3{{$errors->has('usuario' ? 'is-invalid' : '')}}">
                  <span class="input-group-addon"><i class="icon-user"></i></span>
                  <input type="text" value="{{old('usuario')}}" name="usuario" id="usuario" class="form-control" placeholder="Usuario" @blur="empresaListar()" v-model.trim="usuario" @keyup.enter="login()" required>
                  {!!$errors->first('usuario','<span class="invalid-feedback">:message</span>')!!}
                </div>
                <div class="form-group mb-3">
                  <span v-show="empresaArr.length > 0" class="input-group-addon"><i class="fa fa-building-o"></i></span>
                  <select v-show="empresaArr.length > 0" class="form-control" name="empresa" ref="empresa" required>
                    <option value="">Seleccione</option>
                    <option v-for="empresa in empresaArr" :key="empresa.id" :value="empresa.id" v-text="empresa.nombre"></option>
                  </select>
                </div>
                <div class="form-group mb-4{{$errors->has('password' ? 'is-invalid' : '')}}">
                  <span class="input-group-addon"><i class="icon-key"></i></span>
                  <input type="password" name="password" id="password" class="form-control" placeholder="Password" @keyup.enter="login()" required>
                  {!!$errors->first('password','<span class="invalid-feedback">:message</span>')!!}
                </div>
                <div class="row">
                  <div class="col-6">
                    <button type="button" class="btn btn-primary px-4" @click="login()">Acceder</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    new Vue({
      el: '#app'
      , data : {
        usuario : ''
        , empresaArr : []
        , axiosApp : axios.create({baseUrl: '{{$url}}/'})
      }
      , methods: {
        empresaListar: function ()
        {
          if (!this.usuario) return;

          this.empresaArr = [];
          this.$refs.empresa.value = '';
          let me = this, ruta = 'lista_empresas?usuario='+ this.usuario;
          this.axiosApp.get(ruta, {headers:{'X-Requested-With': 'XMLHttpRequest'}})
          .then((response) => {
            if (Array.isArray(response.data.empresas)) me.empresaArr = response.data.empresas;
          })
          .catch(error => console.log(error));
        }
        , login () {
          if (this.empresaArr.length) {
            if (!this.$refs.empresa.value) return;
          }
          this.$refs.loginForm.submit();
        }
      }
    });
  </script>
@endsection
