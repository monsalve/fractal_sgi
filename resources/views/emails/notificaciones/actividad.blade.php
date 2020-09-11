@component('mail::message')
<h1>Hola!</h1>

Te informamos que ahora eres responsable de realizar el seguimiento de la actividad <strong>{{$actividad}}</strong>:
Segmento - <strong>{{$segmento}}</strong>
Plan de acción - <strong>{{$planAccion}}</strong>
Proyecto - <strong>{{$proyecto}}</strong>

@component('mail::button', ['url' => config('app.url')])
Ir a FractalSGI
@endcomponent

Atentamente,<br>
{{$empresa}}
@endcomponent