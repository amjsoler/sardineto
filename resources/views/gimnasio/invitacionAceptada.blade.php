@extends("layout")
@section("title")
    Invitación aceptada
@endsection
@section("content")
    <div class="d-flex flex-column justify-content-center h-100 text-center">
        @if($response["code"] == 0)
            <span class="material-symbols-outlined text-success fs-1 fw-bold">done</span>
            <p><b>{{ __("vistas.gimnasio.invitacionAceptada.ok1") }}</b></p>
            <p>{{ __("vistas.gimnasio.invitacionAceptada.ok2") }}</p>
        @else
            <span class="material-symbols-outlined text-danger fs-1 fw-bold">close</span>
            <p><b>{{ __("vistas.gimnasio.invitacionAceptada.ko1") }}</b></p>
            <p>{{ __("vistas.gimnasio.invitacionAceptada.ko2") }}</p>
        @endif
    </div>
@endsection
