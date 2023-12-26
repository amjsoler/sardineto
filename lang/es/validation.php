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
            "unique" => "Este usuario ya ha sido invitado"
        ]
    ]
];
