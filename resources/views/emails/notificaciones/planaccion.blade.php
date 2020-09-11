@component('mail::message')
<h1>Hola!</h1>

Te informamos que ahora estás a cargo del plan de acción <strong>{{$planAccion}}</strong> del segmento <strong>{{$segmento}}</strong>.

@component('mail::button', ['url' => config('app.url')])
Ir a FractalSGI
@endcomponent

Atentamente,<br>
{{$empresa}}
@endcomponent