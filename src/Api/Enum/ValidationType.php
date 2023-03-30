<?php
namespace NiloBlack\NotificacoesSlack\Api\Enum;

use NiloBlack\NotificacoesSlack\Api\Helpers\BasicEnum;

abstract class ValidationType extends BasicEnum {
    const VT_EMAIL = 'EMAIL';
    const VT_DATE = 'DATA';
    const VT_ENUM = 'ENUM';
    const VT_ARRAY = 'ARRAY';
    const VT_STRING = 'STRING';
    const VT_NUMERIC = 'NUMERIC';
    const VT_BOOL = 'BOOL';    
}