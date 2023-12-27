<?php

return [
    "gimnasio" => [
        "nombre" => [
            "required" => "Especifica el nombre de tu gimnasio",
            "max" => "El nombre es muy largo",
        ],
        "descripcion" => [
            "max" => "La descripción es demasiado larga"
        ],
        "direccion" => [
            "max" => "La dirección es demasiado larga"
        ],
        "email" => [
            "required" => "Especificada el correo del usuario al que quieres invitar",
            "email" => "El email debe ser una dirección de correo válida",
            "exists" => "No hay ningún usuario con ese correo. ¿Se ha registrado ya?",
            "comprobarSiUsuarioYaEstaInvitadoAGimnasio" => "Este usuario ya ha sido invitado",

        ],
        "usuarioId" => [
            "exists" => "El usuario especificado no está invitado",
            "yaAceptado" => "El usuario ya ha aceptado la invitación"
        ]
    ],
    "clase" => [
        "nombre" => [
            "required" => "Especifica un nombre para la clase",
            "max" => "El nombre de la clase es muy largo"
        ],
        "descripcion" => [
            "max" => "La descripción de la clase es muy larga"
        ],
        "fechayhora" => [
            "required" => "Especifica cuando se celebrará la clase"
        ],
        "plazas" => [
            "required" => "Indica el cantidad de plazas para esta clase",
            "integer" => "La cantidad de plazas debe ser un número entero",
            "min" => "La cantidad de plazas debe ser mayor que 0"
        ],
        "pertenece" => "Esta clase no pertenece al gimnasio"
    ],
    "usuarioApuntaClase" => [
        "exists" => "Debes estar invitado al gimnasio para poder apuntarte a la clase",
        "unique" => "Ya estás apuntado a esta clase"
    ]
];
