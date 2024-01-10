<?php

return [
    "CorreoConfirmacionUsuarioInvitadoAGimnasio" => [
        "asunto" => "¡Tienes una invitación!",
        "linea1" => "Te han invitado a formar parte del gimnasio :gimnasio",
        "linea2" => "Para aceptar la invitación, simplemente tienes que pulsar en el enlace que encontrarás a continuación",
        "accion" => "Aceptar invitación"
    ],

    "verificarcuenta" => [
        "asunto" => "Verifica tu cuenta de usuario",
        "linea1" => 'Primero de todo, ¡bienvenido a ' . env("APP_NAME") . '!',
        "linea2" => 'Este mensaje se ha generado de forma automática para que verifiques tu cuenta de usuario y así poder disfrutar de todas las funcionalidades.',
        "linea3" => 'Para hacerlo, solo tienes que pulsar en el enlace que encontrarás a continuación:',
        "accion" => 'Verificar cuenta'
    ],

    "recuperarcuenta" => [
        "asunto" => "Recupera tu cuenta de " . env("APP_NAME"),
        "linea1" => 'Este mensaje se ha generado de forma automática para que restablezcas la contraseña de tu cuenta de usuario',
        "linea2" => "Para hacerlo, solo tienes que pulsar en el enlace que encontrarás a continuación:",
        "accion" => "Recuperar cuenta"
    ],

    "sugerencia" => [
        "asunto" => "Nueva sugerencia realizada",
        "linea1" => " ha realizado la siguiente sugerencia:",
    ]
];
