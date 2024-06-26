<?php

return [
    "gimnasio" => [
        "nombre" => [
            "required" => "Especifica el nombre de tu gimnasio",
            "max" => "El nombre es demasiado largo",
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
        ],
        "gimnasioId" => [
            "unique" => "El usuario ya es administrador del gimnasio"
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
            "required" => "Especifica cuando se celebrará la clase",
            "date" => "La fecha/hora especificada no es válida",
        ],
        "plazas" => [
            "required" => "Indica el cantidad de plazas para esta clase",
            "integer" => "La cantidad de plazas debe ser un número entero",
            "min" => "La cantidad de plazas debe ser mayor que 0"
        ],
        "pertenece" => "Esta clase no pertenece al gimnasio",
        "claseId" => [
            "ComprobarSiQuedanPlazasEnLaClase" => "No qudan plazas para esta clase"
        ],
        "usuarioId" => [
            "ComprobarSiUserReuneRequisitosParaApuntarseAClase" => "No puedes apuntarte a esta clase. No tienes una suscripción activa o no te quedan créditos"
        ]
    ],
    "usuarioApuntaClase" => [
        "unique" => "Ya estás apuntado a esta clase",
        "usuarioId" => [
            "exists" => "No estás apuntado a esta clase",
        ],
        "fechayhora" => [
            "after" => "No te puedes desapuntar de una clase que ya ha terminado"
        ]
    ],
    "tarifa" => [
        "nombre" => [
            "required" => "Ponle un nombre a la tarifa",
            "max" => "El nombre de la tarifa es demasiado largo",
        ],
        "precio" => [
            "required" => "Ponle un precio a la tarifa",
            "decimal" => "El precio ha de ser un número",
            "min" => "El precio debe ser un número positivo",
        ],
        "creditos" => [
            "required" => "Especifica la cantidad de créditos que otorga esta tarifa al suscriptor",
            "integer" => "Los créditos tienen que ser un número válido",
            "min" => "Los créditos deben ser un número positivo",
        ],
        "tipo" => [
            "enum" => "El tipo de la tarifa no es válido"
        ]
    ],
    "suscripcion" => [
        "tarifa" => [
            "required" => "Indica con qué tarifa te quieres suscribir",
            "exists" => "La tarifa no existe"
        ],

        "usuario" => [
            "required" => "Especifica el usuario al que asignar la suscripción",
            "exists" => "El usuario al que quieres asignar la suscripción no forma parte del gimnasio",
        ],
        "usuarioQueSeSuscribe" => [
            "ComprobarQueUserNoTieneSuscripcionEsteMes" => "Este mes ya te has suscrito. Habla con el administrador si quieres cambiar la tarifa o compra un bono"
        ]
    ],
    "articulo" => [
        "nombre" => [
            "required" => "Escribe un nombre para el artículo",
            "max" => "El nombre del artículo es demasiado largo"
        ],
        "descripcion" => [
            "max" => "La descripción del producto es demasiado larga"
        ],
        "stock" => [
            "required" => "Especifica la cantidad de artículos para poner a la venta",
            "integer" => "El stock ha de ser un número entero",
            "min" => "El stock no puede ser inferior a 0"
        ],
        "compra" => [
            "exists" => "La compra que has especificado no existe",
            "ComprobarSiUnaCompraYaEstaPagada" => "La compra ya está pagada",
            "ComprobarSiCompraYaEstaEntregada" => "La compra ya está entregada"
        ],
        "stockdisponible" => [
            "min" => "No hay stock disponible"
        ]
    ],

    "ejercicio" => [
        "nombre" => [
            "required" => "El nombre del ejercicio no puede estar vacío",
            "string" => "El nombre del ejercicio no es válido. ¿Contiene algún carácter extraño?",
            "max:150" => "El nombre del ejercicio es demasiado largo"
        ],

        "descripcion" => [
            "nullable" => "La descripción puede estar vacía",
            "string" => "La descripción del ejercicio no es válida. ¿Contiene algún carácter extraño?",
            "max:500" => "La descripción del ejercicio es demasiado larga"
        ],

        "demostracion" => [
            "nullable" => "La demostración puede estar vacía",
            "url" => "La demostración debe ser un enlace válido a un video"
        ],

        "detalles" => [
            "nullable" => "El detalle de la clase puede estar vacío",
            "string" => "Los detalles de la clase no son válidos. ¿Contiene algún carácter extraño?",
            "max" => "Los detalles son muy largos"
        ],

        "ejercicio" => [
            "unique" => "El ejercicio ya está asociado a la clase"
        ]
    ],

    "metrica" => [
        "peso" => [
            "required" => "El peso no puede estar vacío",
            "decimal" => "El peso debe ser un peso válido (ej. 55,4)"
        ],
        "porcentaje_graso" => [
            "required" => "El porcentaje graso no puede estar vacío",
            "decimal" => "El porcentaje graso debe ser un valor válido (ej. 13,4)"
        ]
    ],

    "ejerciciousuario" => [
        "unorm" => [
            "required" => "Debes especificar el peso en 1RM en este ejercicio",
            "decimal" => "El peso no es válido"
        ]
    ],

    "usuario" => [
        "name" => [
            "required" => "El nombre no puede estar vacío",
            "max" => "El nombre no puede superar los 100 caracteres",
        ],
        "email" => [
            "required" => "El email no puede estar vacío",
            "email" => "El email no es válido",
            "unique" => "Este email ya está registrado",
            "exists" => "El email no es válido"
        ],
        "password" => [
            "required" => "La contraseña no puede estar vacía",
            "confirmed" => "Las contraseñas no coinciden"
        ],

        "correo" => [
            "required" => "Debes especificar el correo de la cuenta que quieres recuperar",
            "email" => "El email no es válido",
        ],

        "contrasenaActual" => [
            "required" => "La contraseña actual no puede estar vacía",
            "ContrasenaActualCorrectaRule" => "La contraseña actual no es correcta",
        ],

        "nuevaContrasena" => [
            "required" => "Debes especificar la contraseña nueva",
            "confirmed" => "Las contraseñas no coinciden"
        ],

        "alertasporcorreo" => [
            "required" => "Debes especificar el campo alertas por correo" ,
            "boolean" => "El campo alertas por correo tiene un formato no valido",
        ],

        "alertaspornotificacion" => [
            "required" => "El campo alertas por correo tiene un formato no valido",
            "boolean" => "El campo alertas por correo tiene un formato no valido",
        ]
    ],

    "enviarsugerencia" => [
        "texto" => [
            "required" => "¡Escribe algo!",
            "string" => "El formato no es válido ¿Contiene caracteres extraños?",
            "max" => "El texto no puede superar los 500 caracteres"
        ]
    ]
];
