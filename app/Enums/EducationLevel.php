<?php

namespace App\Enums;

enum EducationLevel: string
{
    case ENSINO_FUNDAMENTAL = 'Ensino Fundamental';
    case ENSINO_FUNDAMENTAL_INCOMPLETO = 'Ensino Fundamental Incompleto';
    case ENSINO_MEDIO = 'Ensino Médio';
    case ENSINO_TECNICO = 'Ensino Técnico';
    case ENSINO_SUPERIOR_INCOMPLETO = 'Ensino Superior Incompleto';
    case ENSINO_SUPERIOR_COMPLETO = 'Ensino Superior';
    case POS_GRADUACAO = 'Pós-Graduação';
    case MESTRADO = 'Mestrado';
    case DOUTORADO = 'Doutorado';
}
